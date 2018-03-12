<?php
#session_start();
#print_r($_POST['gene_expression']);
//Array name abbreviations
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
    "SX"=>"Spleen",);

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

$sql="select * from ".$interaction_table."_".$_POST['gene_expression'][0]." where Frag1_startbp=".$frag_start." or Frag2_startbp=".$frag_start;
$result=mysqli_query($con,$sql);
$bp_bins=[];
if ($result){
    while($row=mysqli_fetch_array($result)){
        $bp_bins[]=$row["Frag1_startbp"];
        $bp_bins[]=$row["Frag2_startbp"];
    }
}

$bp_bins=array_unique($bp_bins);
#print_r($bp_bins);
#$data=[];

foreach($_POST['gene_expression'] as $field_name){
    $_SESSION["hic_data".$field_name]=[];
    
if($field_name=="IMR90"){
    $sql="select * from ".$interaction_table."_".$field_name."V2 where Frag1_startbp=".$frag_start." or Frag2_startbp=".$frag_start;

}else{
    $sql="select * from ".$interaction_table."_".$field_name." where Frag1_startbp=".$frag_start." or Frag2_startbp=".$frag_start;
}
 
$result=mysqli_query($con,$sql);

if ($result){
    while($row=mysqli_fetch_array($result)){
        if($row["Frag1_startbp"]==$frag_start){
            $_SESSION["hic_data".$field_name][$row["Frag2_startbp"]]=array($row["ObservedCount"],$row["FitHiCexpectedCount"],$row["log10FitHiCPValue"]);   
        }
        elseif($row["Frag2_startbp"]==$frag_start){
            $_SESSION["hic_data".$field_name][$row["Frag1_startbp"]]=array($row["ObservedCount"],$row["FitHiCexpectedCount"],$row["log10FitHiCPValue"]);   
        }
    }
}
#print_r($_SESSION["hic_data".$field_name]);
}

$data=[];
    foreach(array_keys($_SESSION["hic_data".$_POST['gene_expression'][0]]) as $keys ){
        
        $data[$keys]=array($keys);
        
    }

foreach($_POST['gene_expression'] as $field_name){
    foreach(array_keys($_SESSION["hic_data".$field_name]) as $keys ){
        $data[$keys]=array_merge($data[$keys],$_SESSION["hic_data".$field_name][$keys]);
    }
}

#print_r($data);
$savetxt="Fragment Start\t";
echo "<table width=100%  align=\"center\" border=\"5\" bordercolor=\"6B0000\" cellpadding=\"10\" cellspacing=\"0\">";#writes tables
echo "<tr><th>Fragment Start</th>";
foreach($_POST["gene_expression"] as $field_name){
    echo "<th>".$name_array[$field_name]." Observed Count</th>";
    $savetxt=$savetxt.$name_array[$field_name]." Observed Count\t";
    echo "<th>".$name_array[$field_name]." Expected Count</th>";
        $savetxt=$savetxt.$name_array[$field_name]." Expected Count\t";
    echo "<th>".$name_array[$field_name]." -log10(pValue)</th>";
        $savetxt=$savetxt.$name_array[$field_name]."-log10(pValue)\t";
}
$savetxt=$savetxt."\n";
echo "</tr><tr>";

foreach(array_keys($data) as $keys ){
    if(intval($keys)<=intval($range[2]) and intval($keys)>=intval($range[1])){            
        foreach($data[$keys] as $d){
            echo "<td>".$d."</td>";
            $savetxt=$savetxt.$d."\t";
        }
    }
    $savetxt=$savetxt."\n";
    echo "</tr>";
}

echo "</table>";

mysqli_close($con);

echo "<input type=\"button\" onclick='download(\"".str_replace("\n",'\r\n',$savetxt)."\", \"HiC-data.txt\", \"text/richtext\")' value=\"Save to File\" >";//html button for printing out data

?>