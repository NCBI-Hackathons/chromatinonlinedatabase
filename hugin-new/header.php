<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="hugin_icon.ico" type="image/x-icon">

    <title>Hugin</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<script defer src="js/fontawesome-all.js"></script>
     
	 
     <style type="text/css" media="screen">
     .switch {
	position: relative;
	display: block;
	vertical-align: top;
	width: 247px;
	height: 25px;
	padding: 3px;
	margin: 0 10px 10px 0;
	background: linear-gradient(to bottom, #eeeeee, #FFFFFF 25px);
	background-image: -webkit-linear-gradient(top, #eeeeee, #FFFFFF 25px);
	border-radius: 18px;
	box-shadow: inset 0 -1px white, inset 0 1px 1px rgba(0, 0, 0, 0.05);
	cursor: pointer;
	box-sizing:content-box;
    float:left
}
.switch-input {
	position: absolute;
	top: 0;
	left: 0;
	opacity: 0;
	box-sizing:content-box;
}
.switch-label {
	position: relative;
	display: block;
	height: inherit;
	font-size: 10px;
	text-transform: uppercase;
        background: #cfcfd0;
	border-radius: inherit;
	box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.12), inset 0 0 2px rgba(0, 0, 0, 0.15);
	box-sizing:content-box;
}
.switch-label:before, .switch-label:after {
	position: absolute;
	top: 50%;
	margin-top: -.5em;
	line-height: 1;
	-webkit-transition: inherit;
	-moz-transition: inherit;
	-o-transition: inherit;
	transition: inherit;
	box-sizing:content-box;
}
.switch-label:before {
	content: attr(data-off);
	right: 11px;
          color: #7E0000;##000000;
            font-weight:bold;
	text-shadow: 0 1px rgba(255, 255, 255, 0.5);
}
.switch-label:after {
	content: attr(data-on);
	left: 11px;
    color: white;##000000;
	text-shadow: 0 1px rgba(200, 200, 200, 0.2);
    font-weight:bold;
	opacity: 0;
}
.switch-input:checked ~ .switch-label {
	background: #00A115;
	box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), inset 0 0 3px rgba(0, 0, 0, 0.2);
}
.switch-input:checked ~ .switch-label:before {
	opacity: 0;
}
.switch-input:checked ~ .switch-label:after {
	opacity: 1;
}
.switch-handle {
	position: absolute;
	top: 4px;
	left: 4px;
	width: 23px;
	height: 23px;
	background: linear-gradient(to bottom, #FFFFFF 40%, #f0f0f0);
	background-image: -webkit-linear-gradient(top, #FFFFFF 40%, #f0f0f0);
	border-radius: 100%;
	box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
}
.switch-handle:before {
	content: "";
	position: absolute;
	top: 50%;
	left: 50%;
	margin: -6px 0 0 -6px;
	width: 11px;
	height: 11px;
	background: linear-gradient(to bottom, #eeeeee, #FFFFFF);
	background-image: -webkit-linear-gradient(top, #eeeeee, #FFFFFF);
	border-radius: 6px;
	box-shadow: inset 0 1px rgba(0, 0, 0, 0.02);
}
.switch-input:checked ~ .switch-handle {
	left: 226px;/*amount moved to the right*/
	box-shadow: -1px 1px 5px rgba(0, 0, 0, 0.2);
}
 
/* Transition
========================== */
.switch-label, .switch-handle {
	transition: All 0.1s ease;
	-webkit-transition: All 0.3s ease;
	-moz-transition: All 0.3s ease;
	-o-transition: All 0.3s ease;
}

    .datacontrols{
#	position: relative;
	display: inline-block;
	vertical-align: top;
	width: auto;
	height: 15px;
	padding: 3px;
        text-align:center;
	margin: 0px 10px 10px 0px;
	background: transparent;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
	border-radius: 10px;
#	box-shadow: inset 0 0px white, inset 0 1px 1px rgba(0, 0, 2100, 0.05);
	cursor: pointer;
	box-sizing:content-box;
      overflow:hidden;
    float:left;

      color:#7E0000;
#    background-color:#7BAFD4;
    background-color:#cfcfd0;
    font-weight:bold;
}


    </style>
     <script type="text/javascript" src="javascripts.js"></script>
  </head>

  <body>
		<header>
      <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand d-flex align-items-center">
            <strong>HUGIN</strong>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>
	  <div class="collapse bg-dark" id="navbarHeader">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <h4 class="text-white">View Options</h4>
              <form>
				<div class="form-group row">
					<div class="col-md-5">
						<div class="form-group">
							<label class="text-white" for="exampleInputEmail1">Information Type</label>
							<select class="form-control" name="type" title="Selector for switching between modes" id="typeselect">
								<option id="browser" value="browser"<?php if ($type == 'browser') { echo ' selected'; } ?>> Virtual 4C plot </option>
								<option id="heatmap" value="heatmap"<?php if ($type == 'heatmap') { echo ' selected'; } ?>> Heatmap </option>
								<option id="outtext" value="outtext"<?php if ($type == 'outtext') { echo ' selected'; } ?>> Text Information </option>
								<option id="association" value="association"<?php if ($type == 'association') { echo ' selected'; } ?>> Association </option>
							</select>
						</div>
					</div>
					<div class="col-md-5">
						<div class="form-group">
							<label class="text-white" for="exampleInputEmail1">Anchor Position</label>
							<input class="form-control"  type="text" name="Name1" value="<?php echo $position;?>" title="Anchor position which can be given as chr:bp, snp, or gene name">
						</div>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-4">
						<h5 class="text-white">Human Cell Lines</h5>
						<div class="menu-options">
							<div class="form-check">
								<input type="checkbox" class="form-check-input" id="hcl-GM12878" name="hcl[]" value="GM12878"<?php if (in_array('GM12878', $hcl)) { echo ' checked';} ?>>
								<label class="form-check-label <?php if (in_array('GM12878', $hcl)) {echo 'text-success';} else {echo 'text-white';} ?>" for="hcl-GM12878">Lymphoblastoid Cell (GM12878)</label>
							</div>
							<div class="form-check">
								<input type="checkbox" class="form-check-input" id="hcl-H1" name="hcl[]" value="H1"<?php if (in_array('H1', $hcl)) { echo ' checked';} ?>>
								<label class="form-check-label <?php if (in_array('H1', $hcl)) {echo 'text-success';} else {echo 'text-white';} ?>" for="hcl-H1">Human Embryonic Stem Cell</label>
							</div>
							<div class="form-check">
								<input type="checkbox" class="form-check-input" id="hcl-IMR90" name="hcl[]" value="IMR90"<?php if (in_array('IMR90', $hcl)) { echo ' checked';} ?>>
								<label class="form-check-label <?php if (in_array('IMR90', $hcl)) {echo 'text-success';} else {echo 'text-white';} ?>" for="hcl-IMR90">Fetal Lung Fibroblast Cell (IMR90)</label>
							</div>
							<div class="form-check">
								<input type="checkbox" class="form-check-input" id="hcl-MES" name="hcl[]" value="MES"<?php if (in_array('MES', $hcl)) { echo ' checked';} ?>>
								<label class="form-check-label <?php if (in_array('MES', $hcl)) {echo 'text-success';} else {echo 'text-white';} ?>" for="hcl-MES">Mesendodern Cell</label>
							</div>
							<div class="form-check">
								<input type="checkbox" class="form-check-input" id="hcl-MSC" name="hcl[]" value="MSC"<?php if (in_array('MSC', $hcl)) { echo ' checked';} ?>>
								<label class="form-check-label <?php if (in_array('MSC', $hcl)) {echo 'text-success';} else {echo 'text-white';} ?>" for="hcl-MSC">Mesenchymal Stem Cell</label>
							</div>
							<div class="form-check">
								<input type="checkbox" class="form-check-input" id="hcl-NPC" name="hcl[]" value="NPC"<?php if (in_array('NPC', $hcl)) { echo ' checked';} ?>>
								<label class="form-check-label <?php if (in_array('NPC', $hcl)) {echo 'text-success';} else {echo 'text-white';} ?>" for="hcl-NPC">Neural Progenitor Cell</label>
							</div>
							<div class="form-check">
								<input type="checkbox" class="form-check-input" id="hcl-TRO" name="hcl[]" value="TRO"<?php if (in_array('TRO', $hcl)) { echo ' checked';} ?>>
								<label class="form-check-label <?php if (in_array('TRO', $hcl)) {echo 'text-success';} else {echo 'text-white';} ?>" for="hcl-TRO">Trophoblast-Like Cell</label>
							</div>
						</div>
					</div>
					<div class="col-8">
						<h5 class="text-white">Human Primary Tissues</h5>
						<div class="row menu-options">
							<div class="col-6">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-AD" name="hpt[]" value="AD"<?php if (in_array('AD', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('AD', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-AD">Adrenal</label>
								</div>
								
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-AO" name="hpt[]" value="AO"<?php if (in_array('AO', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('AO', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-AO">Aorta</label>
								</div>
								
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-BL" name="hpt[]" value="BL"<?php if (in_array('BL', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('BL', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-BL">Bladder</label>
								</div>

								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-DLPFC" name="hpt[]" value="DLPFC"<?php if (in_array('DLPFC', $hpt)) { echo ' checked';} ?>>
									<label class="form-check- <?php if (in_array('DLPFC', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-DLPFC">Dorsolateral Prefrontal Cortex</label>
								</div>


								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-HC" name="hpt[]" value="HC"<?php if (in_array('HC', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('HC', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-HC">Hippocampus</label>
								</div>
								
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-LI" name="hpt[]" value="LI"<?php if (in_array('LI', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('LI', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-LI">Liver</label>
								</div>

								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-LG" name="hpt[]" value="LG"<?php if (in_array('LG', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('LG', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-LG">Lung</label>
								</div>
							</div>
							<div class="col-6">
							
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-LV" name="hpt[]" value="LV"<?php if (in_array('LV', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('LV', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-LV">Left Ventricle</label>
								</div>

								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-RV" name="hpt[]" value="RV"<?php if (in_array('RV', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('RV', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-RV">Right Ventricle</label>
								</div>
								
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-OV" name="hpt[]" value="OV"<?php if (in_array('OV', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('OV', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-OV">Ovary</label>
								</div>

								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-PA" name="hpt[]" value="PA"<?php if (in_array('PA', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('PA', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-PA">Pancreas</label>
								</div>

								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-PO" name="hpt[]" value="PO"<?php if (in_array('PO', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('PO', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-PO">Psoas</label>
								</div>

								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-SB" name="hpt[]" value="SB"<?php if (in_array('SB', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('SB', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-SB">Small Bowel</label>
								</div>
							
								   
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="hpt-SX" name="hpt[]" value="SX"<?php if (in_array('SX', $hpt)) { echo ' checked';} ?>>
									<label class="form-check-label <?php if (in_array('SX', $hpt)) {echo 'text-success';} else {echo 'text-white';} ?>" for="mcl-SX">Spleen</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-12">
						<h5 class="text-white">Plot Options</h5>
						<p class="text-center">
							<button class="btn btn-outline-light" id="btn-display-all">Display All</button>
							<button class="btn btn-outline-light" id="btn-hide-all">Hide All</button>
							<button class="btn btn-outline-light" id="btn-print-all">Print All</button>
						</p>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-6">
					<p>
						<select class="form-control" name="genes" id="select-genes">
							<option value="hide"<?php if ($displayGenes == 'hide') { echo ' selected'; } ?>> Hide Genes Expression</option>
							<option value="display"<?php if ($displayGenes == 'display') { echo ' selected'; } ?>> Display Gene Expression</option>
							<option value="displaysquished"<?php if ($displayGenes == 'displaysquished') { echo ' selected'; } ?>> Compact Display Genes Expression</option>
							<option value="displayexpanded"<?php if ($displayGenes == 'displayexpanded') { echo ' selected'; } ?>> Expanded Display Genes Expression</option>
						</select>
					</p>
					<p>
						<select class="form-control" name="fires" id="select-fires">
							<option value="hide"<?php if ($displayFires == 'hide') { echo ' selected'; } ?>> Hide FIREs</option>
							<option value="display"<?php if ($displayFires == 'display') { echo ' selected'; } ?>> Display FIREs</option>
						</select>
					</p>
					<p>
						<select class="form-control" name="tads" id="select-tads">
							<option value="hide"<?php if ($displayTads == 'hide') { echo ' selected'; } ?>> Hide TAD Boundaries</option>
							<option value="display"<?php if ($displayTads == 'display') { echo ' selected'; } ?>> Display TAD Boundaries</option>
						</select> 
					</p>
					<p>
						<select class="form-control" name="se" id="select-se">
							<option value="hide"<?php if ($displaySE == 'hide') { echo ' selected'; } ?>> Hide Enhancers</option>
							<option value="displayTESE"<?php if ($displaySE == 'displayTESE') { echo ' selected'; } ?>> Display all Enhancers</option><!--displays both regular and super enhacers-->
							<option value="displayTE"<?php if ($displaySE == 'displayTE') { echo ' selected'; } ?>> Display regular Enhancers</option><!--displays just regular enhancers-->
							<option value="displaySE"<?php if ($displaySE == 'SE') { echo ' selected'; } ?>> Display super Enhancers</option><!--displays just super enhancers-->
						</select> 
					</p>
				</div>
				<div class="col-6">
					<p>
						<select class="form-control" name="ctcf" id="select-ctcf">
							<option value="hide"<?php if ($displayCTCF == 'hide') { echo ' selected'; } ?>> Hide CTCF peaks</option>
							<option value="display"<?php if ($displayCTCF == 'display') { echo ' selected'; } ?>> Display CTCF peaks</option>
						</select> 
					</p>
					<p>
						<select class="form-control" name="chip" id="select-chip">
							<option value="hide"<?php if ($displayChip == 'hide') { echo ' selected'; } ?>> Hide ChIP-Seq</option>
							<option value="display"<?php if ($displayChip == 'hide') { echo ' selected'; } ?>> Display ChIP-Seq</option>
						</select> 
					</p>
					<p>
						<select class="form-control" name="snps" id="select-snps">
							<option value="hide"<?php if ($displaySNPs == 'hide') { echo ' selected'; } ?>> Hide GWAS</option>
							<option value="display"<?php if ($displaySNPs == 'display') { echo ' selected'; } ?>> Display GWAS</option>
							<option value="displaysquished"<?php if ($displaySNPs == 'displaysquished') { echo ' selected'; } ?>> Compact Display GWAS</option>
							<option value="displayexpanded"<?php if ($displaySNPs == 'displayexpanded') { echo ' selected'; } ?>> Expanded Display GWAS</option>
						</select>
					</p>
					<p>
						<select class="form-control" name="py" id="select-py">
							<option value="hide"<?php if ($displayPy == 'hide') { echo ' selected'; } ?>> Free Float p-value Range</option>
							<option value="displaynolabel"<?php if ($displayPy == 'displaynolabel') { echo ' selected'; } ?>> Free Float p-value Range (no label)</option>
							<option value="displayfdr"<?php if ($displayPy == 'displayfdr') { echo ' selected'; } ?>> Display FDR in Range</option><!--makes sure the fdr is displayed-->
							<option value="displaybon"<?php if ($displaypY == 'displaybon') { echo ' selected'; } ?>> Display Bonferroni in Range</option><!--makes sure the bonferroni is displayed-->
						</select>
					</p>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-12">
						<button class="btn" type="submit">Run</button>
						<button class="btn" type="reset">Reset</button>
					</div>
				</div>
			</form>
		  </div>
        </div>
      </div>
    </header>

    <main role="main" class="container">
	
		<div class="row">
			<div class="col-md-4">
				<img src="Hugin_Logo.png" width="300px" height="auto">
			</div>
			<div class="col-md-8">
				<h2><u>H</u>i-C <u>U</u>nifying <u>G</u>enomic <u>In</u>terrogator</h2>
				<p>HUGIn is designed to explore chromatin organizations across multiple human cell lines and primary tissues. HUGIn incorporates data from multiple sources including genetic variants (SNPs), chromatin organization features (including topologically associating domain (TAD) boundaries, frequently interacting regions (FIRE)s, and long-range chromatin interactions from the analysis of Hi-C data) gene expression (from tissue or cell line specific RNA-Seq data), and multiple epigenetic datasets (including information on typical and super enhancers, CTCF binding sites, H3K4me1, H3K4me3, H3K27ac, H3K36me3, H3K27me3 and H3K9me3 peaks). Current data are all mapped to the reference genome hg19 (Genome Reference Consortium GRCh37). For more details and a complete tutorial, please see our <a href="./tutorial_page.php"> Tutorial page</a>.</p>
			</div>
		</div>

     