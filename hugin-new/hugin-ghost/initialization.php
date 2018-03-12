<?php
session_start();


//variable in show that are necessary for that code
//$_POST["name"]=
//$name="chr8:11390000";
$name="rs6450176"; 
$type="browser";
$Position="";
$HighlightingPosition="";

$action="";
$chr="chr5";
$MidPoint=53298024;#basepair for identifying the fragment

//$chr="chr8";
//$MidPoint=11390000;#basepair for identifying the fragment

$_POST["gene_expression"]=["H1","LI","SX"];
$_POST["plot_options"]=["GENES"];
$_POST["subtype"]="ChrBp";
$_POST["type"]="browser";
$_POST["output"]='';
$_SESSION["qvalue"]='0';

$_POST["genes"]="displaysquished";
$_POST["fires"]="hide";
$_POST["TADs"]="hide";
$_POST["SE"]="hide";
$_POST["CTCF"]="hide";
$_POST["CHIP"]="hide";
$_POST["SNPs"]="hide";
$_POST["py"]="hide";

?>
