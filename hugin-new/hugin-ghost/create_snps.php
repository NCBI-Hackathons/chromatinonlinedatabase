<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$display=$_GET['display'];
//$field_name=$_GET['field_name'];
//$field_name='AD';
function draw_highlight($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);

	// Allocate colors for label text, box background and border:
	$color_fg = imagecolorresolvealpha($img, 255, 200,0,75); // highlight yellow
	$y1 = 0;
	imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color_fg);

}

function draw_SNPs($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$name,$display) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
      

	// Allocate colors for label text, box background and border:
    #$color_fg = imagecolorresolve($img, intval(255*$expression),0, 0); // red -> black scale
//    $file=fopen('junk.txt','a');
//    fwrite($file,$expression."\n");
//    fclose($file);
    $color_fg = imagecolorresolve($img, 255,0,0); // red->white
    $color_border = imagecolorresolve($img,0,0,0);//black

//    imageline($img,$x1,0,$x1+1,30,$color_fg);
//    imagefilledrectangle($img,$x1,0,$x1,$y2,$color_fg);
    imagesetthickness($img,8);
    imageline($img,$x1,$y2,$x1,$y2+100,$color_fg);
    if(strpos($display,"expanded")!==false){
        $plot->SetFontTTF('generic','./Intrepid.ttf',30);
        $plot->DrawText('',0,$x1+8,$y2+40,$color_border,$name,'left','center');
    }
}
function label_figure($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world,$label,$size) = $passthru;
    list($x,$y1)=$plot->GetDeviceXY($x_world,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black boarder
    $plot->SetFontTTF('generic','./Intrepid.ttf',$size);
    $plot->DrawText('',0,$x_world,$y2,$line_color,$label,'left','center');
}

function label_genes($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2,$name) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
      
    $color_border = imagecolorresolve($img,0,0,0);//black

    if ($x2-$x1 > 25 && $_GET['print_text'])
        {
#            $plot->DrawText('Arial Black',15,$x1+2,$y2/2+7,$color_border,$name,'left','center');
        }
}

function draw_line($img, $passthru, $plot_area)
{
	list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black line
    imagesetthickness($img,6);
    imageline($img,$x1,$y1,$x2,$y2,$line_color);  
}

$range=explode(':',str_replace('-',':',$_SESSION['Position']));
$chr=$range[0];
$MidPoint=$_SESSION['MidPoint'];

if($_SESSION["type"]=="heatmap"){
    $range[1]=$range[1]-($range[1]%40000);
    $range[2]=$range[2]-(($range[2]+40000)%40000);
}
$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");

// Check connection - need to bounce the page if a bad connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
}

$sql="select distinct chromStart, name from gwasCatalog WHERE chrom='".$chr."' AND (chromStart<=".$range[2]." AND chromEnd>=".$range[1]." )";
//$sql="select distinct startbp, name from snp147 WHERE chr='".$chr."' AND (startbp>=".$range[1]." AND endbp<=".$range[2]." )";

$result=mysqli_query($con,$sql);
$SNP_data=array();
if ($result){
    while($row=mysqli_fetch_array($result)){
        $SNP_data[]=array('',intval($row["chromStart"]),$row["name"],0);
//        $SNP_data[]=array('',intval($row["startbp"]),$row["name"]);
    }
}

if(count($SNP_data)<=1)
    {
        $SNP_data[]=array('',$MidPoint,'');
    }
mysqli_close($con);

$charcount=0;
$previous_end=0;
$count_temp=0;
$c=0;
$counter=1;
if(strpos($display,"expanded")!==false){
    foreach($SNP_data as $gene_info){
        $SNP_data[$c][3]=$c;
        $c+=1;
    }    
}else{
    foreach($SNP_data as $gene_info){
        $SNP_data[$c][3]=0;
        $c+=1;
    }
}

$height=100*max(array_column($SNP_data,3))+100;
$plot_area=array(200,0,4600);
#_truecolor
$plot=new PHPlot_truecolor(4800,$height);#each graph area is 100 pixels high- with extra for labeling
$plot->SetPrintImage(FALSE);#disables auto-output

    /*function that plots the graph data in the area given by $plot_area*/
    $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+100);
    $plot->SetDataType('data-data');
    $blank_data=array();
    $blank_data[]=array('',$range[1],'');
    $blank_data[]=array('',$range[2],'');
    $plot->SetDataValues($blank_data);
    $plot->SetPlotType('lines');
    $plot->SetLineWidth(2);

    $plot->SetPlotBorderType(array('none'));
    $plot->SetYLabelAngle(90);
    $plot->SetYTickLabelPos('none');
$plot->SetXTickLabelPos('none');
    
    $plot->SetDrawXGrid(FALSE);
    $plot->SetDrawYGrid(FALSE);
    $plot->SetDrawXAxis(FALSE);
    $plot->SetDrawYAxis(FALSE);


    $plot->SetXTickPos('none');
    $plot->SetYTickPos('none');
    $plot->TuneXAutoRange(0,'R',0);
    $plot->SetDrawPlotAreaBackground(FALSE);
$plot->DrawGraph();

if(!empty($_SESSION['HighlightingPosition'])){
    foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
        {
            $hrange=explode('-',$highlighting);
            if($hrange[0]>$range[1]){
                $plot->SetCallback('draw_all', 'draw_highlight', array($plot, $height,$hrange[0],$hrange[1]));
                $plot->DrawGraph();
            }
        }
}

if($MidPoint>$range[1] && $MidPoint<$range[2]){
    $plot->SetCallback('draw_all', 'draw_line', array($plot, 0,$height,intval($MidPoint),intval($MidPoint)+5));
    $plot->DrawGraph();
}

foreach($SNP_data as $data){
    $plot->SetCallback('draw_all', 'draw_SNPs', array($plot,$data[3]*100,$data[1],$data[2],$display));
    $plot->DrawGraph();
    }


    
$plot->SetCallback('draw_all','label_figure',array($plot,$height/2,0,"GWAS\nSNPs",40));
$plot->DrawGraph();        

$plot->PrintImage();


?>