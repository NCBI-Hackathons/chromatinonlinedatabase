<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$field_name=$_GET['field_name'];
$display=$_GET['display'];
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

function draw_SE($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
        
    $color_fg = imagecolorresolvealpha($img, 75,0,75,25); // red->white

	imagefilledrectangle($img, $x1, 0, $x2, $y2, $color_fg);
}

function draw_TE($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
        
    $color_fg = imagecolorresolve($img, 0,125,0); // green

	imagefilledrectangle($img, $x1, 0, $x2, $y2, $color_fg);
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



#_truecolor
$plot=new PHPlot_truecolor(4*1200,100);#each graph area is 100 pixels high- with extra for labeling
$plot->SetPrintImage(FALSE);#disables auto-output
//$field_name);
#$plot=plot_chromosome($plot,$gene_data,array(50, 0, 1200),$range);
$plot_area=array(50*4,0,4*1150);

#    $data= array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,2));
    /*function that plots the graph data in the area given by $plot_area*/
    $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+100);
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
$plot->SetXTickLabelPos('none');
    $plot->TuneXAutoRange(0,'R',0);

    $plot->SetDrawXGrid(FALSE);
    $plot->SetDrawYGrid(FALSE);
    $plot->SetDrawXAxis(FALSE);
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

if(!empty($_SESSION['HighlightingPosition'])){
    foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
        {
            $hrange=explode('-',$highlighting);
            if($hrange[0]>$range[1]){
                $plot->SetCallback('draw_all', 'draw_highlight', array($plot, 200,$hrange[0],$hrange[1]));
                $plot->DrawGraph();
            }
        }
}



if($MidPoint>$range[1] && $MidPoint<$range[2]){
    $plot->SetCallback('draw_all', 'draw_line', array($plot, 0,200,intval($MidPoint),intval($MidPoint)+5));
    $plot->DrawGraph();
}


$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");

// Check connection - need to bounce the page if a bad connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
}

    if($field_name=="IMR90")
        $sql="select startbp,endbp,type from enhancers_".$field_name."V2 WHERE chr='".$chr."' AND (endbp<=".$range[2]." AND startbp>=".$range[1]." )";
    else
                $sql="select startbp,endbp,type from enhancers_".$field_name." WHERE chr='".$chr."' AND (endbp<=".$range[2]." AND startbp>=".$range[1]." )";
$result=mysqli_query($con,$sql);


$SE_data=array();
if ($result){
    while($row=mysqli_fetch_array($result)){

        if($row["startbp"]<$range[1])
            $row["startbp"]=$range[1];
        if($row["endbp"]>$range[2])
            $row["endbp"]=$range[2];
        $SE_data[]=array('',intval($row["startbp"]),intval($row["endbp"]),$row["type"]);
    }

    if(strpos($display,"TE")!==false){
    foreach($SE_data as $gene_info){
        if($gene_info[3]=="TE")
            $plot->SetCallback('draw_all', 'draw_TE', array($plot,$plot_area[1]+100,$gene_info[1],$gene_info[2]));
        $plot->DrawGraph();
    }}

    if(strpos($display,"SE")!==false){
    foreach($SE_data as $gene_info){
        if($gene_info[3]=="SE")
            $plot->SetCallback('draw_all', 'draw_SE', array($plot,$plot_area[1]+100,$gene_info[1],$gene_info[2]));
        $plot->DrawGraph();
    }}
}else{
    $plot->SetCallback('draw_all','label_figure',array($plot,40,800,"No Enhancer Data Available",45));
$plot->DrawGraph();
}

$plot->SetCallback('draw_all','label_figure',array($plot,35,0,"Enhancers",35));
$plot->DrawGraph();

mysqli_close($con);        
/* if($MidPoint>$range[1] && $MidPoint<$range[2]){ */
/*     $plot->SetCallback('draw_all', 'draw_line', array($plot, 50,intval($MidPoint))); */
/*     $plot->DrawGraph(); */
/* }     */

$plot->PrintImage();
?>