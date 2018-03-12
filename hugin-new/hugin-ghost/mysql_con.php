<?php
#session_start();

$pthresh=array("GM12878"=>0.01408083,
    "H1"=>0.02252529,
    "IMR90"=>0.02189291,
    "MES"=>0.02384716,
    "MSC"=>0.02392835,
    "NPC"=>0.02187148,
    "TRO"=>0.01984676,
    "AD"=>0.02455886,
    "BL"=>0.02418945,
    "DLPFC"=>0.02372019,
    "HC"=>0.0241933,
    "LG"=>0.02580248,
    "OV"=>0.02622267,
    "PA"=>0.02507906,
    "PO"=>0.0248546,
    "SB"=>0.02458211,
    "AO"=>0.02224473,
    "LV"=>0.02633117,
    "RV"=>0.02057612,
    "LI"=>0.01796159,
    "SX"=>0.02251321,
    "BrainAdult"=>pow(10,-1),
    "BrainFetal"=>pow(10,-1),
    "BrainME45"=>pow(10,-1),
    "BrainME46"=>pow(10,-1),
    "BrainME47"=>pow(10,-1),
    "BrainME49"=>pow(10,-1),
    "BrainME50"=>pow(10,-1),
    "BrainME51"=>pow(10,-1),);

$bonferroni=array("GM12878"=>2.93503*pow(10,-8),
    "H1"=>1.737671*pow(10,-8),
    "IMR90"=>1.572059*pow(10,-8),
    "MES"=>1.777502*pow(10,-8),
    "MSC"=>1.545284*pow(10,-8),
    "NPC"=>4.498834*pow(10,-8),
    "TRO"=>2.223034*pow(10,-8),
    "AD"=>3.254318*pow(10,-7),
    "BL"=>2.716904*pow(10,-7),
    "DLPFC"=>2.910649*pow(10,-7),
    "HC"=>2.510292*pow(10,-7),
    "LG"=>4.212548*pow(10,-7),
    "OV"=>1.013151*pow(10,-6),
    "PA"=>2.099385*pow(10,-7),
    "PO"=>4.211307*pow(10,-7),
    "SB"=>7.660957*pow(10,-7),
    "AO"=>4.628134*pow(10,-8),
    "LV"=>2.721505*pow(10,-8),
    "RV"=>8.396108*pow(10,-8),
    "LI"=>2.850557*pow(10,-8),
    "SX"=>1.903268*pow(10,-7),
    "BrainAdult"=>pow(10,-1),
    "BrainFetal"=>pow(10,-1),
    "BrainME45"=>pow(10,-1),
    "BrainME46"=>pow(10,-1),
    "BrainME47"=>pow(10,-1),
    "BrainME49"=>pow(10,-1),
    "BrainME50"=>pow(10,-1),
    "BrainME51"=>pow(10,-1),);

$range=explode(':',str_replace('-',':',$Position));
$MidPoint=$_SESSION['MidPoint'];
$chr=$range[0];

$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");

// Check connection - need to bounce the page if a bad connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
}

$interaction_table=str_replace("chr","HiC_chr",$chr);
$chromosome_number=intval(str_replace("chr","",$chr));

$_SESSION['frag_chr']=$chr;
$_SESSION['MidPoint']=$MidPoint;


$frag_start=intval($MidPoint/40000)*40000;
if($field_name=="IMR90"){
    $sql="select DISTINCT * from ".$interaction_table."_".$field_name."V2 where Frag1_startbp=".$frag_start." or Frag2_startbp=".$frag_start;
    $sql_FDR="select FitHiCQValue, log10FitHiCPValue from ".$interaction_table."_".$field_name."V2 where FitHiCQValue<=0.1 order by FitHiCQValue DESC limit 1";

}else{
    $sql="select DISTINCT * from ".$interaction_table."_".$field_name." where Frag1_startbp=".$frag_start." or Frag2_startbp=".$frag_start;
    $sql_FDR="select FitHiCQValue, log10FitHiCPValue from ".$interaction_table."_".$field_name." where FitHiCQValue<=0.1 order by FitHiCQValue DESC limit 1";
}
#echo $sql.";<br>";
#echo $sql;
$result=mysqli_query($con,$sql);
$hic_data=array();

if (mysqli_query($con,$sql)){
    while($row=mysqli_fetch_array($result)){
        if($row["Frag1_startbp"]==$frag_start){
            $hic_data[]=array('',$row["Frag2_startbp"],$row["ObservedCount"],$row["FitHiCexpectedCount"],$row["log10FitHiCPValue"],-log10($row["FitHiCQValue"]));
            $hic_data[]=array('',$row["Frag2_startbp"]+40000,$row["ObservedCount"],$row["FitHiCexpectedCount"],$row["log10FitHiCPValue"],-log10($row["FitHiCQValue"]));

        }
        else{
            $hic_data[]=array('',$row["Frag1_startbp"],$row["ObservedCount"],$row["FitHiCexpectedCount"],$row["log10FitHiCPValue"],-log10($row["FitHiCQValue"]));
            $hic_data[]=array('',$row["Frag1_startbp"]+40000,$row["ObservedCount"],$row["FitHiCexpectedCount"],$row["log10FitHiCPValue"],-log10($row["FitHiCQValue"]));
        }
    }
}else{
    echo "Error in finding Database <b> hic_db </b>. (mysql_con.php - 111)<br>";
}




if(!empty($_POST["HighlightingPosition"])){
    $position=array();
  
    foreach(explode(':',$_POST['HighlightingPosition']) as $highlight){
        $sql="select Startbp,Endbp from RPKM_expression, refGene where Genename=\"".$highlight."\" and RPKM_expression.Genename=refGene.name2";
        $result=mysqli_query($con,$sql);
#!empty($result["length"])
        if($result){
            if(mysqli_num_rows($result)>0){
            while($row=mysqli_fetch_array($result)){
                $position[]=$row["Startbp"].'-'.$row["Endbp"];
            }
            }else{
                $position[]=$highlight;
            }
        }
           
    }
    $_SESSION["HighlightingPosition"]=implode(':',$position);
}



$result=mysqli_query($con,$sql_FDR);
$FDR_data=array();
$p_data=array();

if (mysqli_query($con,$sql_FDR)){
    while($row=mysqli_fetch_array($result)){
        $FDR_data[]=$row["FitHiCQValue"];
        $p_data[]=$row["log10FitHiCPValue"];
    }
}

$_SESSION["FDR_value".$field_name]=array(array_pop($FDR_data),array_pop($p_data),-log10($pthresh[$field_name]),-log10($bonferroni[$field_name]));

//print_r($_SESSION["FDR_value".$field_name]);
//echo $field_name."\t".$FDR_data[intval(count($FDR_data)/10)]."\t".$p_data[intval(count($FDR_data)/10)]."<br>";
unset($FDR_data);
unset($p_data);
//print_r($FDR_data[intval(count($FDR_data)/10)]);
//echo "<br>";

mysqli_close($con);

$_SESSION["hic_data".$field_name]=$hic_data;#saves in global session variable

//foreach($hic_data as $d)
//    echo $d[2]."<br>";

?>
