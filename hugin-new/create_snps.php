<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$display=$_GET['display'];

function draw_highlight($img, $passthru, $plot_area)//highlighting function
{
	list($plot, $y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
	// Allocate colors for label text, box background and border:
	$color_fg = imagecolorresolvealpha($img, 255, 200,0,75); // highlight yellow
	$y1 = 0;
	imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color_fg);
}

function draw_SNPs($img, $passthru, $plot_area)//draw snp
{
	list($plot, $y2 ,$x_world1,$name,$display) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    $color_fg = imagecolorresolve($img, 255,0,0); // red
    $color_border = imagecolorresolve($img,0,0,0);//black
    imagesetthickness($img,8);
    imageline($img,$x1,$y2,$x1,$y2+100,$color_fg);
    if(strpos($display,"expanded")!==false or strpos($display,"squished")==false){
        $plot->SetFontTTF('generic','./Intrepid.ttf',35);
        $plot->DrawText('',0,$x1+10,$y2+45,$color_border,$name,'left','center');
    }
}
function label_figure($img, $passthru, $plot_area)//label figure
{
	list($plot, $y2 ,$x_world,$label,$size) = $passthru;
    list($x,$y1)=$plot->GetDeviceXY($x_world,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black boarder
    $plot->SetFontTTF('generic','./Intrepid.ttf',$size);
    $plot->DrawText('',0,$x_world,$y2,$line_color,$label,'left','center');
}

function draw_line($img, $passthru, $plot_area)//draw vertical mid line
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

if($_SESSION["type"]=="heatmap"){//modifies range if heatmap is being viewed to make sure a complete bin is shown
    $range[1]=$range[1]-($range[1]%40000);
    $range[2]=$range[2]-(($range[2]+40000)%40000);
}
$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");//database

// Check connection - need to bounce the page if a bad connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
}
$sql="select distinct startbp, name from gwasCatalog74 WHERE chr='".$chr."' AND (startbp<=".$range[2]." AND startbp>=".$range[1]." ) order by startbp";//sql command
$result=mysqli_query($con,$sql);

if ($result){
    $c=0;
    while($row=mysqli_fetch_array($result)){//loops over results
        if(strpos($display,"expanded")!==false){//expanded view each snp individual strip
            fwrite($file,"expanded\n");
            $c+=1;
        }
        elseif(strpos($display,"squished")!==false){//squished everything in one strip
            fwrite($file,"squished\n");
        }
        else{
            $data[]=array(intval($row["startbp"]),$row["name"],0);//creates the data
        }
    }
}

if(strpos($display,"expanded")==false and strpos($display,"squished")==false){//if comfortable view get data
    $c=0;//code for assigning strips
    $storage_array=array_column($data,2);
    $grange=$range[2]-$range[1];
    $numb=0;
    foreach($data as $snp){//loops over snps
        $box=imagettfbbox(35,0,'./Intrepid.ttf',$snp[1]);//gets text box
        $width=($box[2]+20)*$grange/4400.0;//converts to pixels
        $counter=0;
        foreach($storage_array as $s){//loops over storage_array to find best snp
            if($s<$snp[0]){
                $storage_array[$counter]=$snp[0]+$width;
                $snp[2]=$counter;
                break;
            }
            $counter+=1;
        }
        $data[$numb]=$snp;
        $numb+=1;
    }
    $c=max(array_column($data,2));//gets max strip assigned
}

$height=100*$c+100;
#$height=100*max(array_column($SNP_data,3))+100;
$plot_area=array(200,0,4600);
#_truecolor
$plot=new PHPlot_truecolor(4800,$height);#each graph area is 100 pixels high- with extra for labeling
$plot->SetPrintImage(FALSE);#disables auto-output

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

if(!empty($_SESSION['HighlightingPosition'])){//highlighting regions
    foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
        {
            $hrange=explode('-',$highlighting);
            if($hrange[0]>$range[1]){
                $plot->SetCallback('draw_all', 'draw_highlight', array($plot, $height,$hrange[0],$hrange[1]));
                $plot->DrawGraph();
            }
        }
}

if($MidPoint>$range[1] && $MidPoint<$range[2]){//midpoint line
    $plot->SetCallback('draw_all', 'draw_line', array($plot, 0,$height,intval($MidPoint),intval($MidPoint)+5));
    $plot->DrawGraph();
}

$result=mysqli_query($con,$sql);//query 

if(strpos($display,"expanded")!==false or strpos($display,"squished")!==false){//conditional
    if ($result){
        $c=0;
        while($row=mysqli_fetch_array($result)){//loops over results
            if(strpos($display,"expanded")!==false){
                $plot->SetCallback('draw_all', 'draw_SNPs', array($plot,$c*100,intval($row["startbp"]),$row["name"],$display));
                $plot->DrawGraph();
                $c+=1;
            }else{
                $plot->SetCallback('draw_all', 'draw_SNPs', array($plot,$c*100,intval($row["startbp"]),$row["name"],$display));
                $plot->DrawGraph();
            }
        }
    }
}else{
    foreach($data as $snp){//loops over snps that have been pulled out
        $plot->SetCallback('draw_all', 'draw_SNPs', array($plot,$snp[2]*100,$snp[0],$snp[1],$display));
        $plot->DrawGraph();
    }
}

mysqli_close($con);//closes the connection
    
$plot->SetCallback('draw_all','label_figure',array($plot,$height/2,0,"GWAS\nSNPs",40));//labels the graph
$plot->DrawGraph();        

$plot->PrintImage();

?>