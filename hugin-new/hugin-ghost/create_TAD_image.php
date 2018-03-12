<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$field_name=$_GET['field_name'];
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
function draw_TADS($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
        
    $color_fg = imagecolorresolve($img, 0,0,255); // red->white
    $color_border = imagecolorresolve($img,0,0,0);//black

	imagefilledrectangle($img, $x1, 0, $x2, $y2+25*4, $color_fg);

}
function label_figure($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world,$label,$size) = $passthru;
    list($x,$y1)=$plot->GetDeviceXY($x_world,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black boarder
    $plot->SetFontTTF('generic','./Intrepid.ttf',$size);
    $plot->DrawText('',0,$x_world,$y2,$line_color,$label,'left','center');
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

$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");

// Check connection - need to bounce the page if a bad connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
}

if($field_name=="IMR90")
$sql="select startbp,endbp from TAD_".$field_name."V2 WHERE chr='".$chr."' AND (endbp<=".$range[2]." AND startbp>=".$range[1]." )";
else
    $sql="select startbp,endbp from TAD_".$field_name." WHERE chr='".$chr."' AND (endbp<=".$range[2]." AND startbp>=".$range[1]." )";
#echo $sql;

$result=mysqli_query($con,$sql);
$TAD_data=array();

if ($result){
    while($row=mysqli_fetch_array($result)){

        if($row["startbp"]<$range[1])
            $row["startbp"]=$range[1];
        if($row["endbp"]>$range[2])
            $row["endbp"]=$range[2];
        $row["endbp"]=$row["endbp"];
        $TAD_data[]=array('',intval($row["startbp"]),intval($row["endbp"]));

    }
}

#_truecolor
mysqli_close($con);
$plot=new PHPlot_truecolor(1200*4,25*4);#each graph area is 100 pixels high- with extra for labeling
$plot->SetPrintImage(FALSE);#disables auto-output
//$field_name);
$plot=plot_chromosome($plot,$TAD_data,array(50*4, 0, 1150*4),$range,$MidPoint);

/* if($MidPoint>$range[1] && $MidPoint<$range[2]){ */
/*     $plot->SetCallback('draw_all', 'draw_line', array($plot, 50,intval($MidPoint))); */
/*     $plot->DrawGraph(); */
/* } */

$plot->PrintImage();

function plot_chromosome($plot,$data,$plot_area,$range,$MidPoint){
#    $data= array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,2));
    /*function that plots the graph data in the area given by $plot_area*/
    $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+25*4);
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
    $plot->TuneXAutoRange(0,'R',0);
    
    $plot->SetDrawXGrid(FALSE);
    $plot->SetDrawYGrid(FALSE);
    $plot->SetDrawXAxis(FALSE);
    $plot->SetDrawYAxis(FALSE);

#    $plot->SetDrawXaxis(TRUE);
    $plot->SetXTickPos('none');
    $plot->SetYTickPos('none');
    $plot->TuneYAutoRange(0,'R',0);
#    $plot->SetTitle($y);

#    $plot->SetXLabelType('data',0);
    
#    $plot->SetBackgroundColor('white');
#    $plot->SetTransparentColor('white');
    $plot->SetDrawPlotAreaBackground(FALSE);
    $plot->DrawGraph();

if(!empty($_SESSION['HighlightingPosition'])){
    foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
        {
            $hrange=explode('-',$highlighting);
            if($hrange[0]>$range[1]){
                $plot->SetCallback('draw_all', 'draw_highlight', array($plot, 100,$hrange[0],$hrange[1]));
                $plot->DrawGraph();
            }
        }
}
    
    if($MidPoint>$range[1] && $MidPoint<$range[2]){
    $plot->SetCallback('draw_all', 'draw_line', array($plot, 0,100,intval($MidPoint),intval($MidPoint)+5));
    $plot->DrawGraph();
}
    $file=fopen('junk.txt','a');
    foreach($data as $TAD_info){
        fwrite($file,$TAD_info[1]."   ".$TAD_info[2]."\n");
        $plot->SetCallback('draw_all', 'draw_TADS', array($plot,$plot_area[1],$TAD_info[1],$TAD_info[2]));
            $plot->DrawGraph();
    }
    fclose($file);


$plot->SetCallback('draw_all','label_figure',array($plot,45,0,"TAD\nBoundaries",32));
$plot->DrawGraph();

        
    return $plot;
}

?>