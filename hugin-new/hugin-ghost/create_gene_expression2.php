<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$field_name=$_GET['field_name'];
//$field_name='AD';

function draw_geneexpression($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2,$expression,$strand) = $passthru;
    list($x1,$y)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$y)=$plot->GetDeviceXY($x_world2,$y2);
           

	// Allocate colors for label text, box background and border:
    #$color_fg = imagecolorresolve($img, intval(255*$expression),0, 0); // red -> black scale
//    $file=fopen('junk.txt','a');
//    fwrite($file,$expression."\n");
//    fclose($file);
    $color_fg = imagecolorresolve($img, 255,255-intval(255*$expression),255-intval(255*$expression)); // red->white
    $color_border = imagecolorresolve($img,0,0,0);//black

	imagefilledrectangle($img, $x1,-30, $x2, 30, $color_fg);
//	imagerectangle($img, $x1, 1, $x2, $y2-1, $color_border);
    imagesetthickness ( $img, 3 ); // use the thick line. 1 pixel.
    imageline($img,$x1,$y2,$x2,$y2,$color_border);
    switch($strand){
    case '+':
        $plot->DrawText('Arial Black',15,$x1-2,$y2,$color_border,'|','left','center');
        $plot->DrawText('Arial Black',15,$x2-2,$y2,$color_border,'>','left','center');
        break;
    case '-':
        $plot->DrawText('Arial Black',15,$x2-2,$y2,$color_border,'|','left','center');
        $plot->DrawText('Arial Black',15,$x1-2,$y2,$color_border,'<','left','center');
        break;
    }
}

function label_genes($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2,$name) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
        

	// Allocate colors for label text, box background and border:
    #$color_fg = imagecolorresolve($img, intval(255*$expression),0, 0); // red -> black scale
//    $color_fg = imagecolorresolve($img, 255,255-intval(255*$expression),255-intval(255*$expression)); // red->white
    $color_border = imagecolorresolve($img,0,0,0);//black

    $plot->DrawText('Arial Black',15,$x1+2,$y2/2+5,$color_border,$name,'left','center');

}

function draw_line($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world) = $passthru;
    list($x,$ytest)=$plot->GetDeviceXY($x_world,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black boarder

	imagefilledrectangle($img, $x, 0, $x+2, $y2, $line_color);
//    $plot->DrawText('',0,$x,$y2/2,$line_color,$x.'    '.$x_world);
}


$range=explode(':',str_replace('-',':',$_SESSION['Position']));
$chr=$range[0];
$MidPoint=$_SESSION['MidPoint'];

$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");

// Check connection - need to bounce the page if a bad connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
}

$sql="select distinct Startbp,Endbp,Genename,".$field_name.",strand from RPKM_expression, refGene WHERE Chr='".$chr."' AND (Startbp<=".$range[2]." AND Endbp>=".$range[1]." ) and RPKM_expression.Genename=refGene.name2";

$result=mysqli_query($con,$sql);
$gene_data=array();

if ($result){
    while($row=mysqli_fetch_array($result)){
        if($row["Startbp"]<$range[1])
            $row["Startbp"]=$range[1];
        if($row["Endbp"]>$range[2])
            $row["Endbp"]=$range[2];
        $gene_data[]=array('',intval($row["Startbp"]),intval($row["Endbp"]),$row["Genename"],floatval($row[$field_name]),$row["strand"]);

    }
}

/*$file=fopen('gene_text'.$field_name.'.txt','w');
foreach($gene_data as $d)
    fwrite($file,$d[1]."\t".$d[2]."\t".$d[3]."\t".$d[4]."\t".$d[5]."\n");
fclose($file);
*/

if(count($gene_data)<=1)
    {
        $gene_data[]=array('',$range[1],$range[1],'','');
        $gene_data[]=array('',$range[2],$range[2],'','');
    }

$sql="select max(".$field_name.") from RPKM_expression WHERE Chr='".$chr."'";
$result=mysqli_query($con,$sql);

if ($result){
    while($row=mysqli_fetch_assoc($result)){
        $max_value=floatval(array_values($row)[0]);
    }
}else
    $max_value=100000;//max(array_column($gene_data,4));
mysqli_close($con);


/*$file=fopen('junk.txt','w');
sort($gene_data);
$convertion_factor=intval((($range[2]-$range[1])/1150));
$yposition=0;
$strip_number=1;
$fw=imagefontwidth('15');
$strip=array();
foreach($gene_data as $data){
    if($data[1]<$yposition){
        $strip[]=$strip_number+1;
        $strip_number=$strip_number+1;
    }
    $yposition=($data[1]+strlen($data[3])*$fw*$convertion_factor);
    fwrite($file,($data[1]+strlen($data[3])*$fw*$convertion_factor)."\n");
    

}
fclose($file);
*/

#_truecolor
$plot=new PHPlot_truecolor(1200,25);#each graph area is 25 pixels high- with extra for labeling
$plot->SetPrintImage(FALSE);#disables auto-output
$plot=plot_chromosome($plot,$gene_data,array(50, 0, 1150),$max_value,$field_name,$range);

/* if($MidPoint>$range[1] && $MidPoint<$range[2]){ */
/*     $plot->SetCallback('draw_all', 'draw_line', array($plot, 50,intval($MidPoint))); */
/*     $plot->DrawGraph(); */
/* } */

$plot->PrintImage();


function plot_chromosome($plot,$data,$plot_area,$max_value,$yaxislabel,$range){
    $data= array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,2),array_column($data,3),array_column($data,4),array_column($data,5));
    /*function that plots the graph data in the area given by $plot_area*/
    $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+25);
    $plot->SetDataType('data-data');
    $blank_data=array();
    $blank_data[]=array('',$range[1],'');
    $blank_data[]=array('',$range[2],'');
    $plot->SetDataValues($blank_data);
    $plot->SetPlotType('lines');
    $plot->SetLineWidth(2);

//    $plot->SetPlotBorderType('none');

    $plot->SetPlotBorderType(array('none'));
    //$plot->SetYTitle($_GET['field_name']);
//    $plot->SetYTitle($yaxislabel);
    $plot->SetYLabelAngle(90);
    $plot->SetYTickLabelPos('none');
    
    $plot->SetDrawXGrid(FALSE);
    $plot->SetDrawYGrid(FALSE);
    $plot->SetDrawYAxis(FALSE);

#    $plot->SetDrawXaxis(TRUE);
    $plot->SetXTickPos('none');
    $plot->SetYTickPos('none');
    $plot->TuneXAutoRange(0,'R',0);
#    $plot->SetTitle($y);

#    $plot->SetXLabelType('data',0);
    
#    $plot->SetBackgroundColor('white');
#    $plot->SetTransparentColor('white');
    $plot->SetDrawPlotAreaBackground(FALSE);
    $plot->DrawGraph();

    $yplacement=20;
    foreach($data as $gene_info){
            $plot->SetCallback('draw_all', 'draw_geneexpression', array($plot,5,$gene_info[1],$gene_info[2],floatval($gene_info[4])/$max_value,$gene_info[5]));
        $plot->DrawGraph();
            $plot->SetCallback('draw_all', 'label_genes', array($plot,$plot_area[1]+25,$gene_info[1],$gene_info[2],$gene_info[3])); 

        $plot->DrawGraph();
    }
   
        
    return $plot;
}


?>
