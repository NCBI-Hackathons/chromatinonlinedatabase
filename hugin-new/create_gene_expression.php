<?php
session_start();#starts the session
require_once 'phplot.php';#loads the library
$field_name=$_GET['field_name'];
$display=$_GET['display'];

if($field_name=="IMR90")//hack for IMR90
    $field_name="IMR90V2";

function draw_mid_line($img, $passthru, $plot_area)//function to draw horizontal at anchor point
{
	list($plot, $y1,$y2 ,$x_world1,$x_world2) = $passthru;//pulls out passthrough arguments
    list($x1,$y1test)=$plot->GetDeviceXY($x_world1,$y1);//converts coordinates
    list($x2,$y2test)=$plot->GetDeviceXY($x_world2,$y2);
	$line_color = imagecolorresolve($img, 0, 0, 0); // black line
    imagesetthickness($img,6);//sets line thickness

    imageline($img,$x1,$y1test,$x2,$y2test,$line_color);//draws line
}

function draw_geneexpression($img, $passthru, $plot_area)//function to draw gene expression
{
	list($plot, $y2 ,$x_world1,$x_world2,$expression,$strand) = $passthru;//pulls out pass through arguemnts
    list($x1,$y)=$plot->GetDeviceXY($x_world1,$y2);//converts coordinates
    list($x2,$y)=$plot->GetDeviceXY($x_world2,$y2);
	// Allocate colors for label text, box background and border:
    #$color_fg = imagecolorresolve($img, intval(255*$expression),0, 0); // red -> black scale
    $color_fg = imagecolorresolve($img, 255,255-intval(255*$expression),255-intval(255*$expression)); // red->white
    $color_border = imagecolorresolve($img,0,0,0);//black
	imagefilledrectangle($img, $x1,$y2-10*2, $x2, $y2+20*4, $color_fg);//creates filled rectangle based upon expression
    imagesetthickness($img,8);//sets line thickness
    imageline($img,$x1,$y2,$x2,$y2,$color_border);//draws the line representing the genes
    $plot->SetFontTTF('generic','',40);//sets fonts to use in labeling the graphs
    switch($strand){//switch based upon transcription direction
    case '+':
        $plot->DrawText('Arial Black',0,$x1-8,$y2-2,$color_border,'|','left','center');//draws tail of arrow
        $marker=$x1-8+40;
        while($x2-16>$marker){//adds in multiple directions
            $plot->DrawText('Arial Black',0,$marker,$y2-2,$color_border,'>','left','center');
            $marker+=40;
        }
        $plot->DrawText('Arial Black',0,$x2-24,$y2-2,$color_border,'>','left','center');//draws head
        break;
    case '-'://ditto but other direction
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

function draw_legend($img, $passthru)//draws legend of gene expression
{
    list($plot,$max) = $passthru;//explodes passthrough
    $back_color=imagecolorresolve($img,200,200,200);//base color
    imagefilledrectangle($img, 4575,0, 4600,100, imagecolorresolve($img,255,255,255));//filled rectangle
    imagefilledrectangle($img, 4580,0, 4800,100, $back_color);//filled rectangle
    for($i=0;$i<220;$i+=5){//creates gradient of colors across the legend
        $scale=(220-$i)/220;
        imagefilledrectangle($img, 4580+$i,0, 4800,50,imagecolorresolve($img,255,255-intval(255*$scale),255-intval(255*$scale)));
    }
    $plot->SetFontTTF('generic','',25);//sets up labels on the legend
    $plot->DrawText('Arial Black',90,4775,25,imagecolorresolve($img,0,0,0),'0.0','left','center');
    $plot->DrawText('Arial Black',90,4580,25,imagecolorresolve($img,0,0,0),round($max,1),'left','center');
    $plot->DrawText('Arial Black',90,4680,25,imagecolorresolve($img,0,0,0),round($max/2,1),'left','center');
    $plot->SetFontTTF('generic','',20);
    $plot->DrawText('Arial Black',0,4620,60,imagecolorresolve($img,0,0,0),'expression','left','center');
    $plot->DrawText('Arial Black',0,4640,85,imagecolorresolve($img,0,0,0),'(log10)','left','center');
}

function draw_highlight($img, $passthru, $plot_area)//function to highlight desired area
{
	list($plot, $y2 ,$x_world1,$x_world2) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
	// Allocate colors for label text, box background and border:
	$color_fg = imagecolorresolvealpha($img, 255, 200,0,75); // highlight yellow
	imagefilledrectangle($img, $x1, 0, $x2, $y2, $color_fg);
}

function label_genes($img, $passthru, $plot_area)//function to label genes
{
	list($plot, $y2 ,$x_world1,$x_world2,$name) = $passthru;
    list($x1,$ytest)=$plot->GetDeviceXY($x_world1,$y2);
    list($x2,$ytest)=$plot->GetDeviceXY($x_world2,$y2);
    $color_border = imagecolorresolve($img,0,0,0);//black
    $plot->DrawText('./arial.ttf',0,$x1+2,$y2+25,$color_border,$name,'left','center');//adds text of gene name
}

function label_figure($img, $passthru, $plot_area)//allows the labeling of figure
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
}

$range=explode(':',str_replace('-',':',$_SESSION['Position']));//gets the range of viewing area
$chr=$range[0];
$MidPoint=$_SESSION['MidPoint'];

$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");//sets up mysql connection

// Check connection - need to bounce the page if a bad connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
}

if($_SESSION["type"]=="heatmap"){//if heatmap type use a slightly different sql command
    $range[1]=$range[1]-($range[1]%40000);
    $range[2]=$range[2]-(($range[2]+40000)%40000);
    $sql="select distinct Startbp,Endbp,Genename,strand from RPKM_expression, refGene WHERE Chr='".$chr."' AND (Startbp<=".$range[2]." AND Endbp>=".$range[1]." ) and RPKM_expression.Genename=refGene.name2 order by Startbp ASC"; 
}else{
    if($field_name=="IMR90")//hack because IMR90
        $sql="select distinct Startbp,Endbp,Genename,".$field_name."V2 ,strand from RPKM_expression, refGene WHERE Chr='".$chr."' AND (Startbp<=".$range[2]." AND Endbp>=".$range[1]." ) and RPKM_expression.Genename=refGene.name2 order by Startbp ASC";
    else
        $sql="select distinct Startbp,Endbp,Genename,".$field_name.",strand from RPKM_expression, refGene WHERE Chr='".$chr."' AND (Startbp<=".$range[2]." AND Endbp>=".$range[1]." ) and RPKM_expression.Genename=refGene.name2 order by Startbp ASC";
}

$result=mysqli_query($con,$sql);//mysql query
$gene_data=array();//empty data array

if ($result){
    while($row=mysqli_fetch_array($result)){//cycles over genes in range
        if($row["Startbp"]<$range[1])
            $row["Startbp"]=$range[1];
        if($row["Endbp"]>$range[2])
            $row["Endbp"]=$range[2];
        if($_SESSION["type"]=="heatmap")//if heatmap blank out gene values
            $gene_data[]=array('',intval($row["Startbp"]),intval($row["Endbp"]),$row["Genename"],floatval(0.0),$row["strand"],0);
        else
            $gene_data[]=array('',intval($row["Startbp"]),intval($row["Endbp"]),$row["Genename"],log10(floatval($row[$field_name])+1),$row["strand"],0);
    }
}

if(count($gene_data)<=1)//fills in data if genes are blank
    {
        $gene_data[]=array('',$range[1],$range[1],'','');
        $gene_data[]=array('',$range[2],$range[2],'','');
    }

#$sql="select max(".$field_name.") from RPKM_expression WHERE Chr='".$chr."'";
#$result=mysqli_query($con,$sql);

$max_value=4.6;//max value of gene expression
mysqli_close($con);

#$charcount=0;
#$previous_end=0;
#$count_temp=0;
$c=0;

if(strpos($display,"squish")!==false){//squish display
    foreach($gene_data as $gene_info){
        $gene_data[$c][6]=0;
        $c+=1;
    }
}elseif(strpos($display,"expanded")!==false){//expanded display
    foreach($gene_data as $gene_info){
        $gene_data[$c][6]=$c;
        $c+=1;
    }    
}else{//comfortable display
    $storage_array=array();
    $storage_array=array_column($gene_data,6);
    $grange=$range[2]-$range[1];
    $gene_numb=0;
    foreach($gene_data as $gene_info){
        $box=imagettfbbox(40,0,'./arial.ttf',$gene_info[3]);
        $width=max(20+$box[2]*$grange/4400.0,abs($gene_info[2]-$gene_info[1])+20);
        $counter=0;
        foreach($storage_array as $s){
            if($s<$gene_info[1]){
                $storage_array[$counter]=$gene_info[1]+$width;
                $gene_info[6]=$counter;
                break;
            }
            $counter+=1;
        }
        $gene_data[$gene_numb]=$gene_info;
        $gene_numb+=1;
    }
}

#_truecolor
//height needs to be 25* length of the gene info
$height=100*max(array_column($gene_data,6))+100;//setting up display area
$plot=new PHPlot_truecolor(4800,$height);#each graph area is 25 pixels high- with extra for labeling
$plot->SetPrintImage(FALSE);#disables auto-output

$plot_area=array(200,0,4600);//sets aside window side
$yaxislabel=$field_name;
$data= array_map(NULL,array_column($gene_data,0),array_column($gene_data,1),array_column($gene_data,2),array_column($gene_data,3),array_column($gene_data,4),array_column($gene_data,5),array_column($gene_data,6));
    /*function that plots the graph data in the area given by $plot_area*/
$plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+$height);//sets plot area in window
//plot options
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

if(!empty($_SESSION['HighlightingPosition'])){//adds highlighting boxes
    foreach(explode(':',$_SESSION['HighlightingPosition']) as $highlighting)
        {
            $hrange=explode('-',$highlighting);
            if($hrange[0]>$range[1]){
                $plot->SetCallback('draw_all', 'draw_highlight', array($plot, $height,$hrange[0],$hrange[1]));
                $plot->SetPlotAreaPixels($plot_area[0],$plot_area[1],$plot_area[2],$plot_area[1]+$height);
                $plot->DrawGraph();//flushes the highlight box
            }
        }
}

foreach($data as $gene_info){//loops over genes
    $plot->SetCallback('draw_all', 'draw_geneexpression', array($plot,$gene_info[6]*100+20,$gene_info[1],$gene_info[2],floatval($gene_info[4])/$max_value,$gene_info[5]));//draws gene expression
    $plot->DrawGraph();//flushes the gene expression

    $plot->SetCallback('draw_all', 'label_genes', array($plot,$gene_info[6]*100+40,$gene_info[1],$gene_info[2],$gene_info[3]));//draw genes
    $plot->DrawGraph();//flushes the label genes
}

if($MidPoint>$range[1] && $MidPoint<$range[2]){
    $plot->SetCallback('draw_all', 'draw_mid_line', array($plot, 0,60,intval($MidPoint),intval($MidPoint)+5));//draw midline
    $plot->DrawGraph();//flushes mid line
}

$plot->SetCallback('draw_all','label_figure',array($plot,30,0,"Expression",37));//draws y axis label
$plot->DrawGraph();//flush label figures

if($_SESSION["type"]!=="heatmap"){
    $plot->SetCallback('draw_all','draw_legend',array($plot,$max_value));//draws label
    $plot->DrawGraph();//flush heatmap
}
$plot->PrintImage();//flushes whole image


?>
