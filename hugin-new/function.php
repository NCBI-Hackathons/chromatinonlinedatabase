<?php

include 'function_arrays.php'
require_once 'phplot.php';

function draw_highlight($img, $passthru, $plot_area)//draws highlighted regions
{
	list($plot, $y2 ,$x_world1,$x_world2) = $passthru;//pass through
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);//converts coordinates
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
	// Allocate colors for label text, box background and border:
	$color_fg = imagecolorresolvealpha($img, 255, 200,0,75); // highlight yellow
	$y1 = 0;
	imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color_fg);//draws filled rectangle

}

function draw_marker_bin($img, $passthru, $plot_area)//draw anchor bin
{
    list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$y1test)=$plot->GetDeviceXY($x_world1,$y1);
    list($x2,$y2test)=$plot->GetDeviceXY($x_world2,$y2);

	$color_fg = imagecolorresolvealpha($img, 25, 25,25,20); // grey color
//	$y1 = 0;
	imagefilledrectangle($img, $x1,$y1test,$x2,$y2test, $color_fg);
}

function draw_line($img, $passthru, $plot_area)//draws line
{
	list($plot, $y1,$y2 ,$x_world1,$x_world2,$c) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
	$line_color = imagecolorresolve($img, $c[0], $c[1], $c[2]); // black line
    imagesetthickness($img,6);//sets line thickness

    imageline($img,$x1,$y1,$x2,$y2,$line_color); //draws line
}

function draw_h_line($img, $passthru, $plot_area)//draws horizontal line
{
	list($plot, $y1_world,$y2_world ,$x_world1,$x_world2,$line_color) = $passthru;
    list($x1,$y1)=$plot->GetDeviceXY($x_world1,$y2_world);
    list($x2,$y2)=$plot->GetDeviceXY($x_world2,$y1_world);
	$line_color = imagecolorresolve($img, $line_color[0], $line_color[1], $line_color[2]); //sets desired rgb color
    $white=imagecolorallocatealpha($img,255,255,255,126);//sets white color 
    imagesetthickness($img,8);

    $style=array_merge(array_fill(0,150,$line_color),array_fill(0,75,$white));//array to set the style of alternatiting rgb color and white
    imagesetstyle($img,$style);//sets the style using array
    imageline($img,$x_world1,$y1,$x_world2,$y2,IMG_COLOR_STYLED);//draws line with the style

}
function label_figure($img, $passthru, $plot_area, $fdrValue)//text labeling figure
{
	list($plot, $y2 ,$x_world,$label,$size) = $passthru;
//    list($x,$y1)=$plot->GetDeviceXY($x_world,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black boarder
    $plot->SetFontTTF('generic','./Intrepid.ttf',$size);//sets the font
    $plot->DrawText('',0,$x_world,$y2,$line_color,$label,'left','center');//draws the font
}

function createImage($fieldName, $pdisplay, $midpoint, $hic_data, $position, $name_array) {
	$data = [];
	foreach($hic_data as $hic)//cuts out the section of data that needs to be shown
	{
		if($hic[1]>=$range[1] & $hic[1]<=$range[2]){
			$data[]=array($hic[0],$hic[1],$hic[2],$hic[3],$hic[4]);
		}
	}
	
	if($data[0][1]-$range[1]<40000) {//extends the line to range unless range exceeds data
		array_unshift($data,array('',$range[1],$data[0][2],$data[0][3],$data[0][4]));//extends
	} else{
		array_unshift($data,array('',$data[0][1],0,0,0));//sets data to zero if there is no data to follow
		array_unshift($data,array('',$range[1],0,0,0));//extends the data for whole graph
	}
	$last=array_pop($data);//pops off last data point
	$data[] = $last;//puts it back
	
	if($range[2]-$last[1]<40000){
		$last[1]=$range[2];//changes end data point
		$data[] = $last;//attaches the last point back on with the range extended
	} else {
		$last=array('',$last[1],0,0,0);//changes end data point
		$data[] = $last;//adds to data array
		$last=array('',$range[2],0,0,0);//changes end data point to extend the whole range
		$data[] = $last;//adds to data array
	}
	
	if(count($data) <= 1) {//if no data is found adds blank data and changes field name to include no data found
        for($x=$range[1];$x<=$range[2];$x=$x+20000)
            $data[] = array('',$range[1],'');
			$fieldName=$field_name.'  -  no data in range';
    }
	
	$width=4600;//sets size of pieces
	$height=800;
	$xPad=200;
	$yPad=5;
	
	$plot = new PHPlot_truecolor($width+$xPad,$height+$xPad);
	$plot_area = array($xPad, 5, $width);//sets plot area
	
	$plot->SetLineWidth(6);
	$plot->SetPrintImage(FALSE);#disables auto-output

	$plotdata= array_map(NULL,array_column($data,0),array_column($data,1),array_column($data,2),array_column($data,3));
	$plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+$height);
	$plot->SetDataType('data-data');
	$plot->SetDataValues($plotdata);
	$plot->SetPlotType('lines');
	$plot->SetPlotBorderType(array('left','bottom','right'));
	$plot->SetXTickCrossing(20);
	$plot->SetXTickLength(10);
	$plot->SetYTickCrossing(20);
	$plot->SetDrawXGrid(FALSE);
	$plot->SetDrawYGrid(FALSE);
	$plot->TuneYAutoRange(1,'T',0);
	$plot->TuneXAutoRange(0,'I',0);
	$plot->SetNumberFormat('.',',');
	$plot->SetXLabelType("data",0);
	$plot->SetXTitle($range[0].' : NT Base');
	$plot->SetYTitle('Hi-C Counts','plotleft');
	$plot->SetBackgroundColor('white');
	$plot->SetDataColors(array('black','red','blue'));
	$plot->SetLegend(array('Observed Counts','Expected Counts','-log10(p-value)'));
	$plot->SetLegendBgColor('white');
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

	$plot->SetPlotAreaWorld($range[1],0,$range[2],NULL);

	$ymax=max(array_column($data,4));//sets y axis based upon options selected in interface
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
					$plot->SetCallback('draw_all', 'draw_highlight', array($plot, $height,$hrange[0],$hrange[1]));
					$plot->DrawGraph();
				}
			}
	}else{//if highlightign is not defined just centers it on the anchor point
		$hrange[0]=$MidPoint-80000;
			$hrange[1]=$MidPoint+80000;
			$_SESSION['HighlightingPosition']=$hrange[0].'-'.$hrange[1];
			if($hrange[0]>$range[1]){
				$plot->SetCallback('draw_all', 'draw_highlight', array($plot,$height,$hrange[0],$hrange[1]));
				$plot->DrawGraph();
			}
	}

	$Mid_range=array();//gets marker bin coords
	$Mid_range[0]=$MidPoint-($MidPoint%40000);
	$Mid_range[1]=$Mid_range[0]+40000;

	$plot->SetCallback('draw_all','draw_marker_bin',array($plot,0,$height,$Mid_range[0],$Mid_range[1]));//draws the marker bin
	$plot->DrawGraph();

	if($MidPoint>$range[1] && $MidPoint<$range[2]){//only draws the marker line if it is in range
		$plot->SetCallback('draw_all', 'draw_line', array($plot, 0,$height,intval($MidPoint),intval($MidPoint)+5,array(0,0,0)));
		$plot->DrawGraph();
	}

	$plot->SetCallback('draw_all','draw_h_line',array($plot,$_SESSION["FDR_value".$field_name][2],$_SESSION["FDR_value".$field_name][2],$xPad,$width,array(255, 50, 0)));//draws FDR line
	$plot->DrawGraph();
	$plot->SetCallback('draw_all','draw_h_line',array($plot,$_SESSION["FDR_value".$field_name][3],$_SESSION["FDR_value".$field_name][3],$xPad,$width,array(100, 0, 200)));//draws bonferoni line
	$plot->DrawGraph();

	//labels the figure
	$plot->SetCallback('draw_all','label_figure',array($plot,100,240,$name_array[$field_name],70));//labels the figure with field name
	$plot->DrawGraph();

	list($x,$y)=$plot->GetDeviceXY(0,$_SESSION["FDR_value".$field_name][2]);//converts FDR coords
	list($xb,$yb)=$plot->GetDeviceXY(0,$_SESSION["FDR_value".$field_name][3]);

	if($y-30>240 && strpos($display,"nolabel")==false){//if line is close to top or labels are turned off, don't print label
	$plot->SetCallback('draw_all','label_figure',array($plot,$y-10*3,$xPad+20,'FDR=0.05',40));
	$plot->DrawGraph();
	}

	if($yb-30>240 && strpos($display,"nolabel")==false){//if line is close to top or labels are turned off, don't print label
	$plot->SetCallback('draw_all','label_figure',array($plot,$yb-10*3,$xPad+20,'Bonferroni',40));
	$plot->DrawGraph();
	}

	$plot->SetDataColors(array('black','red','blue'));//sets the colors to plot

	$plot->SetLegend(array('Observed Counts','Expected Counts','-log10(p-value)'));//turns on legend

	$plot->SetLegendBgColor('white');//white background to cover up lines
	$plot->DrawGraph();
	$plot->SetDataColors('blue');
	$plot->SetLegend(NULL);#legend
	$plot->DrawGraph();
	$plot->PrintImage();//draws the image to screen
	
}

?>