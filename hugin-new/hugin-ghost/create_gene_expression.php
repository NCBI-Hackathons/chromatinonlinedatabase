<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$field_name=$_GET['field_name'];
$display=$_GET['display'];

if($field_name=="IMR90")
    $field_name="IMR90V2";
//$field_name='AD';

/*
for ($jj=1;$jj<=$MyHiCData[$j][$ExCount];$jj++){
    $ratio1=($ExStarts[$jj-1]-$StartBp)/($EndBp-$StartBp); //StartBp
    $ratio2 = ($ExEnds[$jj-1]-$StartBp)/($EndBp-$StartBp);  //EndBp (SNP ending information 500 Bp far from SNP starting Bp information) Use StartBp only for SNPs. 
    if ($ratio2>0&&$ratio1<1){
        $ratio1=max($ratio1,0); $ratio2=min($ratio2,1);
        $x1 = intval($HiC_Image_LeftMargin + $ratio1*$HiC_Plot_Width);
        $x2 = intval($HiC_Image_LeftMargin+ $ratio2*$HiC_Plot_Width)+1;// at least one graphical unit.
        $y1=$y0 - $HiC_Image_PerTrackHeight*0.04;
        $y2=$y0 + $HiC_Image_PerTrackHeight*0.04;
        //echo $x1." ".$y1." ".$x2." ".$y2." ".$ExStarts[$jj-1]." ".$ExEnds[$jj-1]."<br>";
        imagefilledrectangle($my_img,$x1,$y1,$x2,$y2,$trackcolor);
    }
*/

function draw_mid_line($img, $passthru, $plot_area)
{
	list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$y1test)=$plot->GetDeviceXY($x_world1,$y1);
    list($x2,$y2test)=$plot->GetDeviceXY($x_world2,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black line
    imagesetthickness($img,3*2);

    imageline($img,$x1,$y1test,$x2,$y2test,$line_color);  
}

function draw_geneexpression($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2,$expression,$strand) = $passthru;
        list($x1,$y)=$plot->GetDeviceXY($x_world1,$y2);
        list($x2,$y)=$plot->GetDeviceXY($x_world2,$y2);
        

	// Allocate colors for label text, box background and border:
    #$color_fg = imagecolorresolve($img, intval(255*$expression),0, 0); // red -> black scale
    $color_fg = imagecolorresolve($img, 255,255-intval(255*$expression),255-intval(255*$expression)); // red->white
    $color_border = imagecolorresolve($img,0,0,0);//black
//need y here 
//image,x,y,x,y
	imagefilledrectangle($img, $x1,$y2-10*2, $x2, $y2+20*4, $color_fg);
//	imagerectangle($img, $x1, 1, $x2, $y2-1, $color_border);
    //imagesetthickness ( $img, 3 ); // use the thick line. 1 pixel.
    imagesetthickness($img,8);
    imageline($img,$x1,$y2,$x2,$y2,$color_border);
        $plot->SetFontTTF('generic','',40);
    switch($strand){
    case '+':
        $plot->DrawText('Arial Black',0,$x1-8,$y2-2,$color_border,'|','left','center');
        $marker=$x1-8+40;
        while($x2-16>$marker){
            $plot->DrawText('Arial Black',0,$marker,$y2-2,$color_border,'>','left','center');
            $marker+=40;
        }
        $plot->DrawText('Arial Black',0,$x2-24,$y2-2,$color_border,'>','left','center');
        break;
    case '-':
        $plot->DrawText('Arial Black',0,$x2-8,$y2-2,$color_border,'|','left','center');
        $marker=$x2-8-40;
        while($x1-16<$marker){
            $plot->DrawText('Arial Black',0,$marker,$y2-2,$color_border,'<','left','center');
            $marker-=40;
        }
        $plot->DrawText('Arial Black',0,$x1-6,$y2-2,$color_border,'<','left','center');
        break;
    }
}

function draw_legend($img, $passthru)
{
    list($plot,$max) = $passthru;
    $back_color=imagecolorresolve($img,200,200,200);
    imagefilledrectangle($img, 4575,0, 4600,100, imagecolorresolve($img,255,255,255));
    imagefilledrectangle($img, 4580,0, 4800,100, $back_color);
    for($i=0;$i<220;$i+=5){
        $scale=(220-$i)/220;
        imagefilledrectangle($img, 4580+$i,0, 4800,50,imagecolorresolve($img,255,255-intval(255*$scale),255-intval(255*$scale)));

    }
    $plot->SetFontTTF('generic','',25);
    $plot->DrawText('Arial Black',90,4775,25,imagecolorresolve($img,0,0,0),'0.0','left','center');
    $plot->DrawText('Arial Black',90,4580,25,imagecolorresolve($img,0,0,0),round($max,1),'left','center');
    $plot->DrawText('Arial Black',90,4680,25,imagecolorresolve($img,0,0,0),round($max/2,1),'left','center');
    $plot->SetFontTTF('generic','',20);
    $plot->DrawText('Arial Black',0,4620,60,imagecolorresolve($img,0,0,0),'expression','left','center');
    $plot->DrawText('Arial Black',0,4640,85,imagecolorresolve($img,0,0,0),'(log10)','left','center');
//        $color_fg = imagecolorresolve($img, 255,255-intval(255*$expression),255-intval(255*$expression)); // red->white
}

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

function label_genes($img, $passthru, $plot_area)
{
	list($plot, $y2 ,$x_world1,$x_world2,$name) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
        
    $color_border = imagecolorresolve($img,0,0,0);//black

    $plot->DrawText('Arial Black',0,$x1+2,$y2+25,$color_border,$name,'left','center');

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

if($_SESSION["type"]=="heatmap"){
    $range[1]=$range[1]-($range[1]%20000);
    $range[2]=$range[2]-(($range[2]+20000)%20000);

    $sql="select distinct Startbp,Endbp,Genename,strand from RPKM_expression, refGene WHERE Chr='".$chr."' AND (Startbp<=".$range[2]." AND Endbp>=".$range[1]." ) and RPKM_expression.Genename=refGene.name2 order by Startbp ASC"; 

    
}else{
    if(strpos($field_name,'Brain')>=0){
           $sql="select distinct Startbp,Endbp,Genename,strand from RPKM_expression, refGene WHERE Chr='".$chr."' AND (Startbp<=".$range[2]." AND Endbp>=".$range[1]." ) and RPKM_expression.Genename=refGene.name2 order by Startbp ASC";
    }elseif($field_name=="IMR90")
        $sql="select distinct Startbp,Endbp,Genename,".$field_name."V2 ,strand from RPKM_expression, refGene WHERE Chr='".$chr."' AND (Startbp<=".$range[2]." AND Endbp>=".$range[1]." ) and RPKM_expression.Genename=refGene.name2 order by Startbp ASC";
    else
        $sql="select distinct Startbp,Endbp,Genename,".$field_name.",strand from RPKM_expression, refGene WHERE Chr='".$chr."' AND (Startbp<=".$range[2]." AND Endbp>=".$range[1]." ) and RPKM_expression.Genename=refGene.name2 order by Startbp ASC";

}
$result=mysqli_query($con,$sql);
$gene_data=array();

if ($result){
    while($row=mysqli_fetch_array($result)){
        if($row["Startbp"]<$range[1])
            $row["Startbp"]=$range[1];
        if($row["Endbp"]>$range[2])
            $row["Endbp"]=$range[2];
        if($_SESSION["type"]=="heatmap" or strpos($field_name,'Brain')>=0)
            $gene_data[]=array('',intval($row["Startbp"]),intval($row["Endbp"]),$row["Genename"],floatval(0.0),$row["strand"],0);
        else
            $gene_data[]=array('',intval($row["Startbp"]),intval($row["Endbp"]),$row["Genename"],log10(floatval($row[$field_name])+1),$row["strand"],0);
    }
}
fclose($file);
if(count($gene_data)<=1)
    {
        $gene_data[]=array('',$range[1],$range[1],'','');
        $gene_data[]=array('',$range[2],$range[2],'','');
    }

$sql="select max(".$field_name.") from RPKM_expression WHERE Chr='".$chr."'";
$result=mysqli_query($con,$sql);

//if ($result){
//    while($row=mysqli_fetch_assoc($result)){
//        $max_value=log10(floatval(array_values($row)[0])+1);
//    }
//}else{
//    $max_value=100000;//max(array_column($gene_data,4));
//}
$max_value=4.6;
mysqli_close($con);

$charcount=0;
$previous_end=0;
$count_temp=0;
$c=0;
$counter=1;

if(strpos($display,"squish")!==false){
    foreach($gene_data as $gene_info){
        $gene_data[$c][6]=0;
        $c+=1;
    }
}
elseif(strpos($display,"expanded")!==false){
    foreach($gene_data as $gene_info){
        $gene_data[$c][6]=$c;
        $c+=1;
    }    
}else{
    $storage_array=array();
    
    foreach($gene_data as $gene_info){
        $storage_array[$counter]=$previous_end;
        
        if($previous_end>=$gene_info[1]-10){
			$counter=1;
			while($storage_array[$counter]>$gene_info[1]){
				$counter++;		}
        }else{
            $counter++;
        }
        $gene_data[$c][6]=$counter;
        $c+=1;
        $previous_end=max($gene_info[1]+strlen($gene_info[3])*50,$gene_info[2]);
    }
    
}
#_truecolor
//height needs to be 25* length of the gene info
$height=100*max(array_column($gene_data,6))+100;
$plot=new PHPlot_truecolor(4800,$height);#each graph area is 25 pixels high- with extra for labeling
$plot->SetPrintImage(FALSE);#disables auto-output

$plot_area=array(200,0,4600);
$yaxislabel=$field_name;
$data= array_map(NULL,array_column($gene_data,0),array_column($gene_data,1),array_column($gene_data,2),array_column($gene_data,3),array_column($gene_data,4),array_column($gene_data,5),array_column($gene_data,6));
    /*function that plots the graph data in the area given by $plot_area*/
    $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+$height);
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

    $yplacement=20;
#$plot->DrawGraph();

if(!empty($_SESSION['HighlightingPosition'])){
    foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
        {
            $hrange=explode('-',$highlighting);
            if($hrange[0]>$range[1]){
                $plot->SetCallback('draw_all', 'draw_highlight', array($plot, $height,$hrange[0],$hrange[1]));
//added
                $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+$height);
                $plot->DrawGraph();
            }
        }
}

foreach($data as $gene_info){
    $plot->SetCallback('draw_all', 'draw_geneexpression', array($plot,$gene_info[6]*100+20,$gene_info[1],$gene_info[2],floatval($gene_info[4])/$max_value,$gene_info[5]));
    $plot->DrawGraph();

    $plot->SetCallback('draw_all', 'label_genes', array($plot,$gene_info[6]*100+40,$gene_info[1],$gene_info[2],$gene_info[3]));
    $plot->DrawGraph();
}

    if($MidPoint>$range[1] && $MidPoint<$range[2]){
        $plot->SetCallback('draw_all', 'draw_mid_line', array($plot, 0,60,intval($MidPoint),intval($MidPoint)+5));
        $plot->DrawGraph();
    }

$plot->SetCallback('draw_all','label_figure',array($plot,30,0,"Expression",37));
$plot->DrawGraph();
#$plot->SetCallback('draw_all','label_figure',array($plot,64,0,"Log10(value)",6*4));
#$plot->DrawGraph();

if($_SESSION["type"]!=="heatmap"  or strpos($field_name,'Brain')>=0){
    $plot->SetCallback('draw_all','draw_legend',array($plot,$max_value));
    $plot->DrawGraph();
}
$plot->PrintImage();


?>
