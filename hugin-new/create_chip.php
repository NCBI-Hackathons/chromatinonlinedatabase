<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$field_name=$_GET['field_name'];

function draw_highlight($img, $passthru, $plot_area)//draws highlight bar
{
	list($plot, $y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);

	// Allocate colors for label text, box background and border:
	$color_fg = imagecolorresolvealpha($img, 255, 200,0,75); // highlight yellow
	$y1 = 0;
	imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color_fg);
}


function label_figure($img, $passthru, $plot_area)//labels the figure
{
	list($plot, $y2 ,$x_world,$label,$size) = $passthru;
    list($x,$y1)=$plot->GetDeviceXY($x_world,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black boarder
    $plot->SetFontTTF('generic','./Intrepid.ttf',$size);
    $plot->DrawText('',0,$x_world,$y2,$line_color,$label,'left','center');
}

function draw_line($img, $passthru, $plot_area)//draw vertical line
{
	list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black line
    imagesetthickness($img,6);

    imageline($img,$x1,$y1,$x2,$y2,$line_color);  
}

$range=explode(':',str_replace('-',':',$_SESSION['Position']));//gets range
$chr=$range[0];//gets chromosome
$MidPoint=$_SESSION['MidPoint'];

$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");

// Check connection - need to bounce the page if a bad connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
}

if($field_name=="IMR90")
    $sql="select * from  ChIPseq_".$field_name."V2 WHERE chr='".str_replace('chr','',$chr)."' ";
else
    $sql="select * from  ChIPseq_".$field_name." WHERE chr='".str_replace('chr','',$chr)."' ";

$result=mysqli_query($con,$sql);//gets query from mysql
$chip_data=array();
if ($result){
    while($row=mysqli_fetch_array($result)){//loops over results
        $chip_data[]=array('',intval($row["startbp"]),$row["H3K27Ac_1"],$row["H3K27Ac_2"],$row["H3K4me1_1"],$row["H3K4me1_2"],$row["H3K4me3_1"],$row["H3K4me3_2"]);
        $chip_data[]=array('',intval($row["startbp"])+40000,$row["H3K27Ac_1"],$row["H3K27Ac_2"],$row["H3K4me1_1"],$row["H3K4me1_2"],$row["H3K4me3_1"],$row["H3K4me3_2"]);
    }
}

$data=array();
foreach($chip_data as $chip)//cuts out the section of data that needs to be shown
{
	if($chip[1]>=$range[1] & $chip[1]<=$range[2]){
        $data[]=$chip;
    }
}

//code to stretch out the data to fill the range of the graph
array_unshift($data,array('',$range[1],$data[0][2],$data[0][3],$data[0][4],$data[0][5],$data[0][6],$data[0][7]));
$last=array_pop($data);
array_push($data,$last);
$last[1]=$range[2];
array_push($data,$last);

if(count($data)<=1){
        $data[]=array('',$range[1],'');
        $data[]=array('',$range[2],'');       
    }

mysqli_close($con);

$max=max(array(max(array_column($data,2)),max(array_column($data,3)),max(array_column($data,4)),max(array_column($data,5)),max(array_column($data,6)),max(array_column($data,7))));

#_truecolor
$plot=new PHPlot_truecolor(4800,600);#each graph area is 100 pixels high- with extra for labeling
$plot->SetPrintImage(FALSE);#disables auto-output
$plot=plot_chip($plot,array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,2),array_column($data,3)),array(200,20,4600),$MidPoint,"H3K27ac",$max);
$plot=plot_chip($plot,array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,4),array_column($data,5)),array(200,220,4600),$MidPoint,"H3K4me1",$max);
$plot=plot_chip($plot,array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,6),array_column($data,7)),array(200,420,4600),$MidPoint,"H3K4me3",$max);

$plot->SetLegendColorboxBorders('none');
$plot->SetLegendPosition(1,0,'plot',1,0,200,-420);
$plot->SetLegend(array('replicate 1','replicate 2'));
$plot->DrawGraph();

if(!empty($_SESSION['HighlightingPosition'])){//highlighting areas
    foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
        {
            $hrange=explode('-',$highlighting);
            if($hrange[0]>$range[1]){
                $plot->SetCallback('draw_all', 'draw_highlight', array($plot, 600,$hrange[0],$hrange[1]));
                $plot->DrawGraph();
            }
        }
}

if($MidPoint>$range[1] && $MidPoint<$range[2]){//draws vertical line
    $plot->SetCallback('draw_all', 'draw_line', array($plot, 0,600,intval($MidPoint),intval($MidPoint)+5));
    $plot->DrawGraph();
}

if(count($data)==2){
    $plot->SetCallback('draw_all','label_figure',array($plot,300,800,"No ChIP-seq data Available",80));//labels no chip data if data not pressense
    $plot->DrawGraph();
}

$plot->PrintImage();

/*function that plots the graph data in the area given by $plot_area*/
function plot_chip($plot,$data,$plot_area,$MidPoint,$name,$max){
#sets up graph
    $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+160);
    $plot->SetDataType('data-data');
    $plot->SetDataValues($data);
    $plot->SetPlotType('lines');
    $plot->SetLineWidth(2);
    $plot->SetDefaultDashedStyle('200-100');
    $plot->SetLineStyles(array('solid','dashed'));
    $plot->SetPlotBorderType(array('bottom','right'));
    $plot->SetXTickLabelPos('none');
    $plot->SetNumYTicks(1);
    $plot->SetBackgroundColor('white');
    $plot->SetDrawXGrid(FALSE);
    $plot->SetDrawYGrid(FALSE);
    $plot->SetDrawXAxis(True);
    $plot->SetDrawYAxis(True);
    $plot->SetPlotAreaWorld(NULL,0,NULL,$max);
    $plot->TuneXAutoRange(0,'R',0);
    $plot->TuneYAutoRange(1,'R',0);
    $plot->SetDrawPlotAreaBackground(FALSE);
    $plot->SetLineWidth(6);
    $plot->SetFontTTF('legend','Intrepid.ttf',35);
    $plot->SetFontTTF('x_label','Intrepid.ttf',35);
    $plot->SetFontTTF('y_label','Intrepid.ttf',30);
    $plot->SetFontTTF('y_title','Intrepid.ttf',45);
    $plot->SetFontTTF('x_title','Intrepid.ttf',45);
    $plot->SetDataColors(array('magenta','orange'));
    $plot->DrawGraph();
    
    $plot->SetCallback('draw_all','label_figure',array($plot,55+$plot_area[1],0,"ChIP-Seq\n".$name,38));//labels the figure
    $plot->DrawGraph();
    return $plot;
}
?>