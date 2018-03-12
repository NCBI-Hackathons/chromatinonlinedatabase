<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$field_name=$_GET['field_name'];
$display=$_GET['pdisplay'];
//$userinputhic=$_SESSION['userinputhic'];

//$file=fopen('junk.txt','w');
//foreach($userinputhic as $data){
//    foreach($data as $u){
//        fwrite($file,$u."\t");
//    }
//    fwrite($file,"\n");
//}
//fclose($file);


//$user_hic=explode("\t",str_replace("\n","\t",str_replace("\r","",str_replace(" ","",$user_hic))));
#$user_hic=explode("\t",$user_hic);
$name_array=array("GM12878"=>"Lymphoblastoid Cell",
    "H1"=>"Human Embryonic\nStem Cell",
    "IMR90"=>"Fetal Lung\nFibroblast Cell",
    "MES"=>"Mesendoderm Cell",
    "MSC"=>"Mesenchymal Stem Cell",
    "NPC"=>"Neural Progenitor Cell",
    "TRO"=>"Trophoblast-like Cell",
    "AD"=>"Adrenal",
    "BL"=>"Bladder",
    "DLPFC"=>"Dorsolateral Prefrontal Cortex",
    "HC"=>"Hippocampus",
    "LG"=>"Lung",
    "OV"=>"Ovary",
    "PA"=>"Pancreas",
    "PO"=>"Psoas",
    "SB"=>"Small Bowel",
    "AO"=>"Aorta",
    "LV"=>"Left Ventricle",
    "RV"=>"Right Ventricle",
    "LI"=>"Liver",
    "BrainAdult"=>"Brain Adult",
    "BrainFetal"=>"Brain Fetal",
    "BrainME45"=>"Brain ME45",
    "BrainME46"=>"Brain ME46",
    "BrainME47"=>"Brain ME47",
    "BrainME49"=>"Brain ME49",
    "BrainME50"=>"Brain ME50",
    "BrainME51"=>"Brain ME51",);

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

function draw_marker_bin($img, $passthru, $plot_area)
{
    list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$y1test)=$plot->GetDeviceXY($x_world1,$y1);
    list($x2,$y2test)=$plot->GetDeviceXY($x_world2,$y2);

	$color_fg = imagecolorresolvealpha($img, 25, 25,25,25); // highlight yellow
//	$y1 = 0;
	imagefilledrectangle($img, $x1,$y1test,$x2,$y2test, $color_fg);
//    $file=fopen('junk.txt','a');
//    fwrite($file,"in call back\n");

}

function draw_line($img, $passthru, $plot_area)
{
	list($plot, $y1,$y2 ,$x_world1,$x_world2,$c) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
	$line_color = imagecolorresolve($img, $c[0], $c[1], $c[2]); // black line
    imagesetthickness($img,6);

    imageline($img,$x1,$y1,$x2,$y2,$line_color);  

}
function draw_h_line($img, $passthru, $plot_area)
{
	list($plot, $y1_world,$y2_world ,$x_world1,$x_world2,$line_color) = $passthru;
    list($x1,$y1)=$plot->GetDeviceXY($x_world1,$y2_world);
    list($x2,$y2)=$plot->GetDeviceXY($x_world2,$y1_world);
	$line_color = imagecolorresolve($img, $line_color[0], $line_color[1], $line_color[2]); // blue line
    $white=imagecolorallocatealpha($img,255,255,255,126);
    imagesetthickness($img,6);

    $style=array_merge(array_fill(0,100,$line_color),array_fill(0,20*2,$white));
    imagesetstyle($img,$style);
    imageline($img,$x_world1,$y1,$x_world2,$y2,IMG_COLOR_STYLED);  

}
function label_figure($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world,$label,$size) = $passthru;
//    list($x,$y1)=$plot->GetDeviceXY($x_world,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black boarder
    $plot->SetFontTTF('generic','./Intrepid.ttf',$size);
    $plot->DrawText('',0,$x_world,$y2,$line_color,$label,'left','center');
}

$MidPoint=$_SESSION['MidPoint'];
$hic_data=$_SESSION["hic_data".$field_name];// $_SESSION["hic_data"];

$range=explode(':',str_replace('-',':',$_SESSION['Position']));

$data=array();

#$file=fopen('junk.txt','w');
#fwrite($file,$range[1]."\t".$range[2]."\n");
foreach($hic_data as $hic)//cuts out the section of data that needs to be shown
{
    foreach($hic as $h)
 #       fwrite($file,$h."\t");
 #   fwrite($file,"\n");
	if($hic[1]>=$range[1] & $hic[1]<=$range[2]){
        if($_SESSION['qvalue']==1)
            $data[]=array($hic[0],$hic[1],$hic[2],$hic[3],$hic[5]);

        else
            $data[]=array($hic[0],$hic[1],$hic[2],$hic[3],$hic[4]);
    }
}
//code to stretch out the data to fill the range of the graph
array_unshift($data,array('',$range[1],$data[0][2],$data[0][3],$data[0][4]));
$last=array_pop($data);
array_push($data,$last);
$last[1]=$range[2];
array_push($data,$last);

if(count($data)<=1)
    {
        for($x=$range[1];$x<=$range[2];$x=$x+20000)
            $data[]=array('',$range[1],'');
        $field_name=$field_name.'  -  no data in range';
    }

$WIDTH=4600;
$HEIGHT=800;
$Xpad=200;
$Ypad=5;

$plot=new PHPlot_truecolor($WIDTH+$Xpad,$HEIGHT+$Xpad);#each graph area is 200 pixels high- with extra for labeling
$plot_area=array($Xpad, 5, $WIDTH);
#$plot=new PHPlot_truecolor(4800,1000);#each graph area is 200 pixels high- with extra for labeling
#$plot_area=array(200, 20, 4600);

$plot->SetLineWidth(6);
$plot->SetPrintImage(FALSE);#disables auto-output

$plotdata= array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,2),array_column($data,3));
$plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+$HEIGHT);
#$plot->SetPlotAreaWorld($range[1],NULL,$range[2]);
$plot->SetDataType('data-data');
$plot->SetDataValues($plotdata);
$plot->SetPlotType('lines');

$plot->SetPlotBorderType(array('left','bottom','right'));
$plot->SetXTickCrossing(20);
$plot->SetXTickLength(10);
$plot->SetYTickCrossing(20);
#    $plot->SetDrawXAxis(FALSE)

$plot->SetDrawXGrid(FALSE);
$plot->SetDrawYGrid(FALSE);
$plot->TuneYAutoRange(1,'T',0);
$plot->TuneXAutoRange(0,'I',0);
$plot->SetNumberFormat('.',',');
$plot->SetXLabelType("data",0);
# $plot->SetTitle($y);
$plot->SetXTitle($range[0].' : NT Base');
$plot->SetYTitle('Hi-C Counts','plotleft');

$plot->SetBackgroundColor('white');
$plot->SetDataColors(array('black','red','blue'));

$plot->SetLegend(array('Observed Counts','Expected Counts','-log10(p-value)'));

$plot->SetLegendBgColor('white');
#$plot->setLegendBgColor('red');#changes the background color of the legend
$plot->SetFontTTF('legend','Intrepid.ttf',35);
$plot->SetFontTTF('x_label','Intrepid.ttf',35);
$plot->SetFontTTF('y_label','Intrepid.ttf',30);
$plot->SetFontTTF('y_title','Intrepid.ttf',45);
$plot->SetFontTTF('x_title','Intrepid.ttf',45);
$plot->SetSkipBottomTick(True);
$plot->SetSkipTopTick(True);
$plot->DrawGraph();

#plotting pvalue
$plotdata= array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,4));#pvalue data
$plot->SetDrawPlotAreaBackground(FALSE);
$plot->SetDataValues($plotdata);#plots the pvalue
$plot->SetPlotBorderType(array('none'));
#if(max(array_column($data,4))<$_SESSION["FDR_value".$field_name][3]*4/3.0 and $_SESSION["qvalue"]!=1)
#    $plot->SetPlotAreaWorld($range[1],0,$range[2],$_SESSION["FDR_value".$field_name][3]*4/3.0);
#else
if($_SESSION["qvalue"]==1)
    $plot->SetPlotAreaWorld($range[1],0,$range[2],NULL);
else
    $plot->SetPlotAreaWorld($range[1],0,$range[2],NULL);

$ymax=max(array_column($data,4));
if(strpos($display,"hide")!==false){
    $plot->SetPlotAreaWorld($range[1],0,$range[2],NULL);
    #    $plot->TuneYAutoRange(1,'R',0);
}elseif(strpos($display,"fdr")!==false && $_SESSION["FDR_value".$field_name][2]*2 >$ymax){
    $plot->SetPlotAreaWorld($range[1],0,$range[2],$_SESSION["FDR_value".$field_name][2]*2);
}elseif(strpos($display,"bon")!==false && $_SESSION["FDR_value".$field_name][3]*2 >$ymax){
    $plot->SetPlotAreaWorld($range[1],0,$range[2],$_SESSION["FDR_value".$field_name][3]*2);
}else{
    $plot->SetPlotAreaWorld($range[1],0,$range[2],NULL);
}

$plot->SetDrawYGrid(FALSE);
$plot->SetDrawXaxis(FALSE);
#$plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+200);
$plot->SetYTickPos('plotright');
$plot->SetYTickLabelPos('plotright');
$plot->SetYTitle('-log10(p-value)','plotright');

$plot->TuneYAutoRange(1,'R',0);

//$plot->SetYTickIncrement(0.5);
$plot->SetXTickIncrement();
$plot->SetDataColors('blue');
$plot->SetLegend(NULL);#legend
//$plot->DrawGraph();

    // Register a callback for drawing the extra label:
if(!empty($_SESSION['HighlightingPosition'])){
    foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
        {
            $hrange=explode('-',$highlighting);
            if($hrange[0]>$range[1]){
                $plot->SetCallback('draw_all', 'draw_highlight', array($plot, $HEIGHT,$hrange[0],$hrange[1]));
                $plot->DrawGraph();
            }
        }
}else{
    $hrange[0]=$MidPoint-80000;
        $hrange[1]=$MidPoint+80000;
        $_SESSION['HighlightingPosition']=$hrange[0].'-'.$hrange[1];
        if($hrange[0]>$range[1]){
            $plot->SetCallback('draw_all', 'draw_highlight', array($plot,$HEIGHT,$hrange[0],$hrange[1]));
            $plot->DrawGraph();
        }
}
    $Mid_range=array();
    $Mid_range[0]=$MidPoint-($MidPoint%40000);
    $Mid_range[1]=$Mid_range[0]+40000;

    $plot->SetCallback('draw_all','draw_marker_bin',array($plot,0,$HEIGHT,$Mid_range[0],$Mid_range[1]));
    $plot->DrawGraph();

if($MidPoint>$range[1] && $MidPoint<$range[2]){
    $plot->SetCallback('draw_all', 'draw_line', array($plot, 0,$HEIGHT,intval($MidPoint),intval($MidPoint)+5,array(0,0,0)));
    $plot->DrawGraph();
}

    $plot->SetCallback('draw_all','draw_h_line',array($plot,$_SESSION["FDR_value".$field_name][2],$_SESSION["FDR_value".$field_name][2],$Xpad,$WIDTH,array(0, 200, 255)));
    $plot->DrawGraph();
    $plot->SetCallback('draw_all','draw_h_line',array($plot,$_SESSION["FDR_value".$field_name][3],$_SESSION["FDR_value".$field_name][3],$Xpad,$WIDTH,array(0, 0, 255)));
    $plot->DrawGraph();


#$plot->SetCallback('draw_all','label_figure',array($plot,25*4,60*4,$range[0]":".$name_array[$field_name],70));
$plot->SetCallback('draw_all','label_figure',array($plot,100,240,$name_array[$field_name],70));
$plot->DrawGraph();

    list($x,$y)=$plot->GetDeviceXY(0,$_SESSION["FDR_value".$field_name][2]);
    list($xb,$yb)=$plot->GetDeviceXY(0,$_SESSION["FDR_value".$field_name][3]);

if($y-30>240 && strpos($display,"nolabel")==false){
$plot->SetCallback('draw_all','label_figure',array($plot,$y-10*3,$Xpad+20,'FDR=0.05',40));
$plot->DrawGraph();
}

    $plot->SetCallback('draw_all','label_figure',array($plot,$yb-10*3,$Xpad+20,'Bonferroni',40));
    $plot->DrawGraph();


$plot->SetDataColors(array('black','red','blue'));

$plot->SetLegend(array('Observed Counts','Expected Counts','-log10(p-value)'));


$plot->SetLegendBgColor('white');
$plot->DrawGraph();
$plot->SetDataColors('blue');
$plot->SetLegend(NULL);#legend
$plot->DrawGraph();
$plot->PrintImage();

?>
