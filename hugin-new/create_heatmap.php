<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library

function draw_circle($img, $passthru, $plot_area)//draw circle at xy position
{
	list($plot,$x_world1,$shift,$diff,$pvalue,$y2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1+$shift,$y2);
    if($pvalue>1)
        $pvalue=1;
    $diff=($diff-1);
    if($diff>1)
        $diff=1;
    if($diff<0)
        $diff=0;
	// Allocate colors for label text, box background and border:
	$boxcolor = imagecolorresolve($img, 255,intval(255*(1-$pvalue)),intval(255*(1-$pvalue))); // highlight yellow
    $line_color = imagecolorresolve($img, 0, 0, 0); // black line
	$y1 = 0+$y2;
	imagefilledellipse($img, $x1,$y2+100,100*$diff,100*$diff,$boxcolor);
    imagesetthickness($img,5);
    imagearc($img,$x1,$y2+100,100*$diff,100*$diff,0,360,$line_color);
}

function draw_circle2($img, $passthru, $plot_area)//quick and dirty spin off to use direct coords
{
	list($plot,$x,$y,$diff,$pvalue) = $passthru;
	// Allocate colors for label text, box background and border:
	$boxcolor = imagecolorresolve($img, 255,intval(255*(1-$pvalue)),intval(255*(1-$pvalue))); // highlight yellow
    $line_color = imagecolorresolve($img, 0, 0, 0); // black line
	imagefilledellipse($img,$x,$y,100*$diff,100*$diff,$boxcolor);
    imagesetthickness($img,5);
    imagearc($img,$x,$y,100*$diff,100*$diff,0,360,$line_color);
}

function draw_h_line($img, $passthru, $plot_area)//draw horizontal line 
{
	list($plot, $y1_world,$y2_world ,$x_world1,$x_world2,$line_color) = $passthru;
    list($x1,$y1)=$plot->GetDeviceXY($x_world1,$y2_world);
    list($x2,$y2)=$plot->GetDeviceXY($x_world2,$y1_world);
	$line_color = imagecolorresolve($img, $line_color[0], $line_color[1], $line_color[2]); // blue line
    $white=imagecolorallocatealpha($img,255,255,255,126);
    imagesetthickness($img,6);
    $style=array_merge(array_fill(0,100,$line_color),array_fill(0,100,$white));
    imagesetstyle($img,$style);
    imageline($img,$x_world1,$y1_world,$x_world2,$y2_world,IMG_COLOR_STYLED);  
}

function draw_line($img, $passthru, $plot_area)//draw vertical line
{
	list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$y1test)=$plot->GetDeviceXY($x_world1,$y1);
    list($x2,$y2test)=$plot->GetDeviceXY($x_world2,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black line
    imagesetthickness($img,6);
    imageline($img,$x1,$y1test,$x2,$y2test,$line_color);  
}

function label_figure($img, $passthru, $plot_area)//label figure
{
	list($plot, $y2 ,$x_world,$label,$size) = $passthru;
    list($x,$y1)=$plot->GetDeviceXY($x_world,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black boarde
    $plot->SetFontTTF('generic','./Intrepid.ttf',$size);
    $plot->DrawText('',0,floor($x_world),floor($y2),$line_color,$label,'left','center');
}

function draw_highlight($img, $passthru, $plot_area)//highlight region
{
    list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$y1test)=$plot->GetDeviceXY($x_world1,$y1);
    list($x2,$y2test)=$plot->GetDeviceXY($x_world2,$y2);
	$color_fg = imagecolorresolvealpha($img, 255, 200,0,75); // highlight yellow
	imagefilledrectangle($img, $x1,$y1test,$x2,$y2test, $color_fg);
}

function draw_marker_bin($img, $passthru, $plot_area)//draw marker bin
{
    list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$y1test)=$plot->GetDeviceXY($x_world1,$y1);
    list($x2,$y2test)=$plot->GetDeviceXY($x_world2,$y2);
	$color_fg = imagecolorresolve($img,75,75,75); // highlight yellow
	imagefilledrectangle($img, $x1,$y1test,$x2,$y2test, $color_fg);
}

function plot_maps($plot,$data,$plot_area,$bin_size,$range,$max_pval,$field_name,$MidPoint){//plot the histograms
    $name_array=array("GM12878"=>"Lymphoblast",
    "H1"=>"Embryonic",
    "IMR90"=>"Fibroblast",
    "MES"=>"Mesendoderm",
    "MSC"=>"Mesenchymal",
    "NPC"=>"Neural\nProgenitor",
    "TRO"=>"Trophoblast-like",
    "AD"=>"Adrenal",
    "BL"=>"Bladder",
    "DLPFC"=>"DLPFC",
    "HC"=>"Hippocampus",
    "LG"=>"Lung",
    "OV"=>"Ovary",
    "PA"=>"Pancreas",
    "PO"=>"Psoas",
    "SB"=>"Small\nBowel",
    "AO"=>"Aorta",
    "LV"=>"Left\nVentricle",
    "RV"=>"Right\nVentricle",
    "LI"=>"Liver",
    "SX"=>"Spleen",);
    
    $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+200);
    $plot->SetDataType('data-data');
    
    $blank_data=array();
    $blank_data[]=array('',$range[1],'');
    $blank_data[]=array('',$range[2],'');
    $plot->SetDataValues($blank_data);
    $plot->SetPlotType('lines');
    $plot->SetPlotBorderType(array('none'));
    $plot->SetYTickLabelPos('none');
    $plot->SetxTickLabelPos('none');
    $plot->SetXTickPos('none');
    $plot->SetYTickPos('none');
    $plot->SetDrawXAxis(FALSE);
    $plot->SetDrawYAxis(FALSE);
    $plot->SetDrawXGrid(FALSE);
    $plot->SetDrawYGrid(FALSE);
    $plot->TuneXAutoRange(0,'R',0);
    $plot->SetLineWidth(6);
    $plot->SetXTickCrossing(20);
    $plot->SetXTickLength(10);

    $plot->DrawGraph();
    
    if(!empty($_SESSION['HighlightingPosition'])){
        foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
            {
                $hrange=explode('-',$highlighting);
                if($hrange[0]>$range[1]){
                    $plot->SetCallback('draw_all', 'draw_highlight', array($plot,0,12,$hrange[0],$hrange[1]));
                    $plot->DrawGraph();
                }
            }
    }

    $Mid_range=array();
    $Mid_range[0]=$MidPoint-($MidPoint%40000);
    $Mid_range[1]=$Mid_range[0]+40000;

    $plot->SetCallback('draw_all','draw_h_line',array($plot,$plot_area[1]+100,$plot_area[1]+100,200,4600,array(150,150, 150)));
    $plot->DrawGraph();

    $plot->SetCallback('draw_all','draw_marker_bin',array($plot,0,48,$Mid_range[0],$Mid_range[1]));
    $plot->DrawGraph();
    
    if($MidPoint>$range[1] && $MidPoint<$range[2]){
        $plot->SetCallback('draw_all', 'draw_line', array($plot, 0,4*60,intval($MidPoint),intval($MidPoint)+5));
        $plot->DrawGraph();
    }
    
    foreach($data as $block){
        if($block[1]<$MidPoint){
            $plot->SetCallback('draw_all', 'draw_circle', array($plot,$block[1],-20000,$block[2]/$block[3],$block[4]/$max_pval,$plot_area[1]));
        }else{
            $plot->SetCallback('draw_all', 'draw_circle', array($plot,$block[1],-20000,$block[2]/$block[3],$block[4]/$max_pval,$plot_area[1]));
        }
        $plot->DrawGraph();
    }
    if($field_name=="IMR90V2")//IMR90 hack
        $plot->SetCallback('draw_all','label_figure',array($plot,$plot_area[1]+100,0,$name_array["IMR90"],30));
    else
        $plot->SetCallback('draw_all','label_figure',array($plot,$plot_area[1]+100,0,$name_array[$field_name],30));
    
    $plot->DrawGraph();
    
    if($field_name=="IMR90V2")//IMR90 hack
        $plot->SetCallback('draw_all','label_figure',array($plot,$plot_area[1]+100,4550,$name_array["IMR90"],30));
    else
        $plot->SetCallback('draw_all','label_figure',array($plot,$plot_area[1]+100,4550,$name_array[$field_name],30));
   
    $plot->DrawGraph();
    return $plot;
}

$MidPoint=$_SESSION['MidPoint'];

$range=explode(':',str_replace('-',':',$_SESSION['Position']));

$range[1]=$range[1]-($range[1]%40000);//shifting to the bin
$range[2]=$range[2]-(($range[2]+40000)%40000);

$plot=new PHPlot_truecolor(4800,200+220*sizeof($_SESSION['gene_expression']));
$plot->SetPrintImage(FALSE);#disables auto-output

$counter=0;
$plot->SetFontTTF('generic','./Intrepid.ttf',10);

foreach($_SESSION['gene_expression'] as $field_name)//
    {
        $hic_data=$_SESSION["hic_data".$field_name];// $_SESSION["hic_data"];
        if($_SESSION['qvalue']==1)
            $max_pval=3.9;
        else
            $max_pval=3.9;
        $data=array();
        $bin_size=($hic_data[1][1]-$hic_data[0][1]);

        $previous_value=-1;
        while(sizeof($hic_data)>0)
            {
                $hic=array_pop($hic_data);
                if($hic[1]>$range[1] & $hic[1]<$range[2] & $previous_value!=$hic[2]){
                    if($_SESSION['qvalue']==1){
                        $data[]=array($hic[0],$hic[1],$hic[2],$hic[3],$hic[5]);
                    }
                    else
                        $data[]=array($hic[0],$hic[1],$hic[2],$hic[3],$hic[4]);
                }
                $previous_value=$hic[2];
            }
        $plot=plot_maps($plot,$data,array(200,4*$counter,4600),$bin_size,$range,$max_pval,$field_name,$MidPoint);
        $counter+=55;
    }

//setting up legend
$plot->SetPlotAreaPixels(200,4*$counter+60,4600,240);
$plot->SetDataType('data-data');
$blank_data=array();
$blank_data[]=array('',$range[1],'');
$blank_data[]=array('',$range[2],'');
$plot->SetDataValues($blank_data);
$plot->SetPlotBorderType(array('TOP'));
$plot->SetYTickLabelPos('none');
$plot->SetXTickLabelPos('plotup');
$plot->SetXTickPos('plotup');
$plot->SetYTickPos('none');
$plot->SetDrawXAxis(FALSE);
$plot->SetDrawYAxis(FALSE);
$plot->SetDrawXGrid(FALSE);
$plot->SetDrawYGrid(FALSE);
$plot->SetFontTTF('x_label','Intrepid.ttf',35);
$plot->SetFontTTF('y_label','Intrepid.ttf',30);
$plot->SetFontTTF('y_title','Intrepid.ttf',45);
$plot->SetFontTTF('x_title','Intrepid.ttf',45);
$plot->DrawGraph();
#$plot->SetFontTTF('generic','./Intrepid.ttf',13);

//plots circles based upon data
if($_SESSION['qvalue']==1)
    $plot->SetCallback('draw_all','label_figure',array($plot,4*$counter+120,120,$range[0].":".$range[1]."-".$range[2].": q-value",45));
else
    $plot->SetCallback('draw_all','label_figure',array($plot,4*$counter+120,120,$range[0].":".$range[1]."-".$range[2].": -log10(p-value)",45));
$plot->DrawGraph();
//sets up legend with labels 
$plot->SetCallback('draw_all','label_figure',array($plot,4*$counter+123,1420,":0.0       :1.5     :3.0     :>4.5         Observed/Expected       :<1.0    :1.3      :1.6      :>2.0",48));
$plot->DrawGraph();
//draws circles showing legends for the data
$plot->SetCallback('draw_all', 'draw_circle2', array($plot,1360,4*$counter+128,1,0));
    $plot->DrawGraph();
$plot->SetCallback('draw_all', 'draw_circle2', array($plot,1680,4*$counter+128,1,1.3/4.5));
    $plot->DrawGraph();
$plot->SetCallback('draw_all', 'draw_circle2', array($plot,1920,4*$counter+128,1,2.6/4.5));
    $plot->DrawGraph();
$plot->SetCallback('draw_all', 'draw_circle2', array($plot,2200,4*$counter+128,1,3.9/4.5));
    $plot->DrawGraph();

$plot->SetCallback('draw_all', 'draw_circle2', array($plot,2400,4*$counter+128,0.01,1));
    $plot->DrawGraph();
$plot->SetCallback('draw_all', 'draw_circle2', array($plot,3620,4*$counter+128,0.3,1));
    $plot->DrawGraph();
$plot->SetCallback('draw_all', 'draw_circle2', array($plot,3896,4*$counter+128,.6,1));
    $plot->DrawGraph();
$plot->SetCallback('draw_all', 'draw_circle2', array($plot,4168,4*$counter+128,1,1));
    $plot->DrawGraph();

$plot->PrintImage();

?>
