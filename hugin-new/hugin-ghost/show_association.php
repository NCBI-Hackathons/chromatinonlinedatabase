<?php
#session_start();

function get_SNP_pos($connection,$SNP)
{
    if(strtoupper(substr($SNP,0,2))=='RS'){
        $_POST["subtype"]="snp147";
        $sql="select chr,startbp,endbp from snp147 where name=\"".$SNP."\"";
        $result=mysqli_query($connection,$sql);
        if (mysqli_query($connection,$sql)){
            while($row=mysqli_fetch_array($result)){
                $chr=$row["chr"];
                $refPoint=$row["startbp"];
            }
        }
    }else{
        $SNP=explode(":",$SNP);
        $refPoint=$SNP[1];
        $chr="chr".str_replace("chr","",$SNP[0]);
    }
   
    return array($chr,$refPoint);
}

function get_fragment($field_name,$frag_start,$interaction_table,$connection)
{
    if($field_name=="IMR90"){
        $sql="select * from ".$interaction_table."_".$field_name."V2 where Frag1_startbp=".$frag_start." or Frag2_startbp=".$frag_start;       
    }else{
        $sql="select * from ".$interaction_table."_".$field_name." where Frag1_startbp=".$frag_start." or Frag2_startbp=".$frag_start;
    }
    #echo $sql;
    $result=mysqli_query($connection,$sql);
    $hic_data=array();
    
    if (mysqli_query($connection,$sql)){
        $frag_start=0;
        $logp_value=0;
        while($row=mysqli_fetch_array($result)){
            if($row["Frag1_startbp"]==$frag_start and abs($row["Frag1_startbp"]-$row["Frag2_startbp"])<10^6){
                if($logp_value<$row["log10FitHiCPValue"]){
                    $logp_value=$row["log10FitHiCPValue"];
                    $frag_start=$row["Frag2_startbp"];
                }
            }
            elseif(abs($row["Frag1_startbp"]-$row["Frag2_startbp"])<10^6){
                if($logp_value<$row["log10FitHiCPValue"]){
                    $logp_value=$row["log10FitHiCPValue"];
                    $frag_start=$row["Frag1_startbp"];
                }
            }
        }
    }else{
        echo "Error in finding Database <b> hic_db </b>. (mysql_con.php - 45)<br>";
    }
    
    return array($frag_start,$logp_value);
}

function get_pvalues($field_name,$frag_start,$interaction_table,$connection,$genes)
{
    $interaction_table=str_replace("X","23",$interaction_table);
//    $file=fopen('junk','a');
    if($field_name=="IMR90")
        $field_name="IMR90V2";
    $data=array();
    foreach($genes as $gene){
        $txStart_frag=intval($gene[1]/40000)*40000;
#        echo $gene[0].":".$frag_start.":".$txStart_frag."\t";
        if($txStart_frag>$frag_start)
            $sql="select distinct * from ".$interaction_table."_".$field_name." where Frag1_startbp=".$frag_start." and Frag2_startbp=".$txStart_frag;
        else
            $sql="select distinct * from ".$interaction_table."_".$field_name." where Frag1_startbp=".$txStart_frag." and Frag2_startbp=".$frag_start;

        $result=mysqli_query($connection,$sql);
        if (mysqli_query($connection,$sql)){
            while($row=mysqli_fetch_array($result)){
                //              fwrite($file,$gene[0]."\t".$gene[1]."\t".$row["log10FitHiCPValue"]."\n");
                $data[]=array($gene[0],$gene[1],$txStart_frag,$row["log10FitHiCPValue"],$row["ObservedCount"],$row["FitHiCexpectedCount"]);
            }
        }else{
            echo $interaction_table."Error in finding Database <b> hic_db </b>. (show_association - 81)<br>";
        }
    }
    $highest_p=max(array_column($data,3));

    $id_genes=[];
    foreach($data as $d)
        {
            if($d[3]==$highest_p)
                $id_genes[]=array($d[0],$d[3],$d[2],$d[4],$d[5]);
        }
//    fclose$file);
    return $id_genes;
}

///outside of functions
$SNPs=$_POST["output"];

$SNPs=explode("\t",str_replace("\n","\t",str_replace("\r","",str_replace(" ","",$SNPs))));
$con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");
if (mysqli_connect_errno()) #connections to mysql database
    echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
echo "<table width=100%  align=\"center\" border=\"5\" bordercolor=\"6B0000\" cellpadding=\"10\" cellspacing=\"0\">";#writes tables
echo "<tr><th>SNP</th><th>location</th>";
$savetxt="SNP\tlocation\t";
foreach($_POST["gene_expression"] as $field_name){
    echo "<th>".$field_name." (-log10(p-value)/counts/expected)</th>";
    $savetxt=$savetxt.$field_name." (-log10(p-value)/counts/expected)\t";
}
$savetxt=$savetxt."\n";
echo "</tr>";#table header written out

$field_name=$_POST["gene_expression"][0];
//$file=fopen('junk','a');
foreach($SNPs as $SNP){
    if(empty($SNP))
        continue;

    $SNP_pos=get_SNP_pos($con,$SNP);
#    $refPoint=$SNP_pos[1];
    $frag_start=intval($SNP_pos[1]/40000)*40000;
    $chromosome_number=intval(str_replace("chr","",$SNP_pos[0]));
#    list($frag_start,$logp_value)=get_fragment($field_name,$frag_start,str_replace("chr","HiC_chr",$SNP_pos[0]),$con);
    
    $sql="select distinct Genename,Startbp,Endbp,strand from RPKM_expression,refGene WHERE Chr='".$SNP_pos[0]."' AND (Startbp>=".($frag_start-pow(10,6))." AND Startbp<=".($frag_start+40000+pow(10,6))." ) and RPKM_expression.Genename=refGene.name2";

$result=mysqli_query($con,$sql);
$gene_data=[];

if ($result){
    while($row=mysqli_fetch_array($result)){
        if($row["strand"]=='+' and abs($row["Startbp"]-$frag_start)>15000 and abs($row["Startbp"]+40000-$frag_start)>15000)
            $gene_data[]=array($row["Genename"],$row["Startbp"]);
        elseif(abs($row["Endbp"]-$frag_start)>15000 and abs($row["Endbp"]+40000-$frag_start)>15000)
            $gene_data[]=array($row["Genename"],$row["Endbp"]);
    }
}
if(empty($gene_data))#if no data is return go to next iteration
    continue;

echo "<tr><td>";
echo $SNP;#name of input
$savetxt=$savetxt.$SNP."\t";
echo "</td><td>";
echo $SNP_pos[0].":".$SNP_pos[1];#position
$savetxt=$savetxt.$SNP_pos[0].":".$SNP_pos[1]."\t";
echo "</td>";
foreach($_POST["gene_expression"] as $field_name){
    $data=get_pvalues($field_name,$frag_start,str_replace("chr","HiC_chr",$SNP_pos[0]),$con,$gene_data);
    
    echo "<td>";
//            echo implode(' ',array_column($data,0))."(".$data[0][2]."-".($data[0][2]+40000).":".$data[0][1].")";
    $s=sprintf('%s (%0.2f/%d/%d)',implode(' ',array_column($data,0)),$data[0][1],$data[0][3],$data[0][4]);
    echo $s;
    $savetxt=$savetxt.$s."\t";
    echo "</td>";
}
$savetxt=$savetxt."\n";
flush();
}
echo "</table>";
mysqli_close($con);

echo "<button onclick='download(\"".str_replace("\n",'\r\n',$savetxt)."\", \"SNP_association.txt\", \"text/richtext\")'>save results</button>";
?>
