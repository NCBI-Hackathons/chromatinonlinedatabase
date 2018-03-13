<?php
#base file that gets called first when the page is hit
#
#this page can be replace to redirect or hide a site

ini_set("max_execution_time", 0);//sets the time out so won't result in a time out error
error_reporting(0);
$email="jsmartin at email.unc.edu";

include 'generalFunctions.php';

//default
$show_default = true;

//get variables
$displayGenes = (isset($_GET['genes']) ? cleanData($_GET['genes']) : 'display');
$displayFires = (isset($_GET['fires']) ? cleanData($_GET['fires']) : 'hide');
$displayTads = (isset($_GET['tads']) ? cleanData($_GET['tads']) : 'hide');
$displaySE = (isset($_GET['se']) ? cleanData($_GET['se']) : 'hide');
$displayCTCF = (isset($_GET['ctcf']) ? cleanData($_GET['ctcf']) : 'hide');
$displayChip = (isset($_GET['chip']) ? cleanData($_GET['chip']) : 'hide');
$displaySNPs = (isset($_GET['snps']) ? cleanData($_GET['snps']) : 'hide');
$displayPy = (isset($_GET['py']) ? cleanData($_GET['py']) : 'hide');
$position = (isset($_GET['position']) ? cleanData($_GET['position']) : 'rs6450176');
$type = (isset($_GET['type']) ? cleanData($_GET['type']) : 'browser');
$midpoint = (isset($_GET['midpoint']) ? cleanData($_GET['midpoint']) : 53298024);
$hpL = $midpoint - 10000;
$hpH = $midpoint + 10000;
$highlightingPosition = (isset($_GET['highlightingPosition']) ? cleanData($_GET['highlightingPosition']) : $hpL . '-' . $hpH);
$action = (isset($_GET['action']) ? cleanData($_GET['action']) : '');
$chrL = $midpoint - 1000000;
$chrH = $midpoint + 1000000;
$chr = (isset($_GET['chr']) ? cleanData($_GET['chr']) : 'chr5:' . $chrL . '-' . $chrH);
$midpoint = (isset($_GET['midpoint']) ? cleanData($_GET['midpoint']) : 53298024);
$hclarray = [];
$hclarray[] = 'H1';
$hcl = (isset($_GET['hcl']) ? $_GET['hcl'] : $hclarray);
$hptarray = [];
$hptarray[] = 'LI';
$hptarray[] = 'SX';
$hpt = (isset($_GET['hpt']) ? $_GET['hpt'] : $hptarray);


include('./header.php');//general header
include('./form.php');//html with interface

//first load
if ($show_default) {
	include('./mysql_con.php');
	
	
?>
	<div id="allplots">
		<div>
			<img src="./create_gene_expression.php?field_name=H1&display=display" style='max-width:100%; height: auto;'/><img  src="./create_image.php?field_name=H1&pdisplay=hide" style='max-width:100%; height: auto;' title="Hi-C plots" />
		</div>
		<div>
			<img src="./create_gene_expression.php?field_name=LI&display=display" style='max-width:100%; height: auto;'/><img  src="./create_image.php?field_name=LI&pdisplay=hide" style='max-width:100%; height: auto;' title="Hi-C plots" />
		</div>
		<div>
			<img src="./create_gene_expression.php?field_name=SX&display=display" style='max-width:100%; height: auto;'/><img  src="./create_image.php?field_name=SX&pdisplay=hide" style='max-width:100%; height: auto;' title="Hi-C plots" />
		</div>
	</div>
<?php
}

    include('./footer.php');//general footer that closes the page


?>


