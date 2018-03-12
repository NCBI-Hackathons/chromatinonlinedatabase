<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">  
<html xmlns="http://www.w3.org/1999/xhtml">


<head>
     <title>Hugin</title>
     <link rel="shortcut icon" href="hugin_icon.ico" type="image/x-icon">
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


<body >
                        
  <table width=100%  align="center" cellpadding="10" cellspacing="0">
    <tr>
      <td>
        <img src="Hugin_Logo.png" width="300px" height="auto">
      </td>
<td>
  <h2><u>H</u>i-C <u>U</u>nifying <u>G</u>enomic <u>In</u>terrogator</h2>
    <p>HUGIn is designed to explore chromatin organizations across multiple human cell lines and primary tissues. HUGIn incorporates data from multiple sources including genetic variants (SNPs), chromatin organization features (including topologically associating domain (TAD) boundaries, frequently interacting regions (FIRE)s, and long-range chromatin interactions from the analysis of Hi-C data) gene expression (from tissue or cell line specific RNA-Seq data), and multiple epigenetic datasets (including information on typical and super enhancers, CTCF binding sites, H3K4me1, H3K4me3, H3K27ac, H3K36me3, H3K27me3 and H3K9me3 peaks). Current data are all mapped to the reference genome hg19 (Genome Reference Consortium GRCh37). For more details and a complete tutorial, please see our <a href="./tutorial_page.php"> Tutorial page</a>.
  </p>
  </td>
      </tr>
      </table>

     