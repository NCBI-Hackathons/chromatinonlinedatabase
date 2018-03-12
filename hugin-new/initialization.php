<?php
session_start();//starts the session so data can be passed across programs

$name_array=array("GM12878"=>"Lymphoblastoid Cell",//basically dictionary array between short and full version names
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
"SX"=>"Spleen",);//to add more entries add in a short and long version of the name here. The short version will be used to call the data from the mysql database while the long version will be used for display

//variable in show that are necessary for that code with starting information
$name="rs6450176"; //name of starting point - here it is a snp but can be gene or chr:bp
$type="browser";//default type of display
$Position="";
$HighlightingPosition="";

$action="";
$chr="chr5";//startign chromosome
$MidPoint=53298024;#basepair for identifying the fragment

//$chr="chr8";
//$MidPoint=11390000;#basepair for identifying the fragment

$_POST["gene_expression"]=["H1","LI","SX"];//starting slection
#$_POST["plot_options"]=["GENES"];//startign plot options
$_POST["subtype"]="ChrBp";//
$_POST["type"]="browser";//browser display version
$_POST["output"]='';
#$_SESSION["qvalue"]='0';

//sets up plot options
$_POST["genes"]="display";//displayed squished genes
$_POST["fires"]="hide";//tuned off
$_POST["TADs"]="hide";//turned off
$_POST["SE"]="hide";//turned off
$_POST["CTCF"]="hide";//turned off
$_POST["CHIP"]="hide";//turned off
$_POST["SNPs"]="hide";//turned off
$_POST["py"]="hide";//turned off

?>
