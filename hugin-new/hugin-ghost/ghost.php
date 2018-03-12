<?php
ini_set("max_execution_time", 0);
//error_reporting(E_ALL);
//ini_set("display_errors", 1);


if (isset($_POST['action'])&&!isset($_POST['reset'])){
	$action=$_POST['action'];
}else {
	$action = 'initialization';
}


if ($action == 'initialization'){
    include('./initialization.php');
    include('./header.php');
    include('./show.html');
    include('./footer.php');
}
elseif ($action == 'list_products')
{
    $Position=$_POST["Position"];
    asort($_POST['gene_expression']);

    $_SESSION['HighlightingPosition']=$_POST["HighlightingPosition"];
    include('./header.php');
    include('./HTML_controls.php');


//    print_r($_POST['gene_expression']);
//    echo "<br>";
//    print_r($_POST["type"]);
//    echo "<br>";
//    print_r($name);
    
    if($_POST["type"]=='browser'){
        include('./show.html');
        if($_POST['gene_expression']!=[""]){
            if($_POST['gene_expression']!=[""]){
                foreach($_POST['gene_expression'] as $field_name){
                    include('./mysql_con.php');
                }
            }
            $_SESSION["type"]=$_POST["type"];
            if (in_array("pvalue",$_POST["plot_options"]))
                {$_SESSION["qvalue"]=1;}
            else
                {$_SESSION["qvalue"]=0;}
            echo "<div id=\"allplots\">";
            echo "<span id=\"plots\"></span>";
            foreach($_POST['gene_expression'] as $field_name){
                echo "<div> <button style=\"display:none\" class=\"removebuttons\"  onclick=removeimage(this,\"".$field_name."\") title=\"Button to removed desired dataset\">remove ".$field_name."</button>";
                echo "<button style=\"display:none\" class=\"printbuttons\" onclick=PrintDiv(this) title=\"Button to print data window\">print ".$field_name."</button>";
                if (strpos($_POST["genes"],"display")!==false)
                    {
                        $display=$_POST["genes"];

                        echo "<img src=\"./create_gene_expression.php?field_name=$field_name&display=$display\" style='max-width:100%; height: auto;'/>";
                    }
                if (strpos($_POST["fires"],"display")!==false)
                    {
                        $display=$_POST["fires"];
                        echo "<img src=\"./create_FIRES_image.php?field_name=$field_name\" style='max-width:100%; height: auto;'/>";
                    }
                if (strpos($_POST["TADs"],"display")!==false)
                    {
                        $display=$_POST["TADs"];
                        echo "<img src=\"./create_TAD_image.php?field_name=$field_name\" style='max-width:100%; height: auto;'/>";
                    }
                if (strpos($_POST["SE"],"display")!==false)
                    {
                        $display=$_POST["SE"];
                        echo "<img src=\"./create_SE_image.php?field_name=$field_name&display=$display\" style='max-width:100%; height: auto;'/>";
                    }
                if (strpos($_POST["CTCF"],"display")!==false)
                    {
                        $display=$_POST["CTCF"];
                        echo "<img src=\"./create_CTCF.php?field_name=$field_name\" style='max-width:100%; height: auto;'/>";      
                    }
                if (strpos($_POST["SNPs"],"display")!==false)
                    {
                        $display=$_POST["SNPs"];
                        echo "<img src=\"./create_snps.php?display=$display\" style='max-width:100%; height: auto;'/>";      
                    }
                if (strpos($_POST["CHIP"],"display")!==false)
                    {
                        $display=$_POST["CHIP"];
                        echo "<img src=\"./create_chip.php?field_name=$field_name\" style='max-width:100%; height: auto;'/>";      
                    }
                $pdisplay=$_POST["py"];

                $_SESSION['userhic']=array();
                
#                list($chr,$frag1,$chr2,$frag2,$count,$p,$q,$E)=sscanf(str_replace(" ","",explode("\n",$_POST["output"])),"%d\t%d\t%d\t%d\t%d\t%f\t%f\t%f");
#                print_r($frag1);
#                $_SESSION['userinputhic']=array_map($chr1,$frag1,$frag2,$count,$E,-log10($p));

                //print_r($userhic);
                echo "<img  src=\"./create_image.php?field_name=$field_name&pdisplay=$pdisplay\" style='max-width:100%; height: auto;' title=\"Hi-C plots\" />";
                echo "</div>";
                //      echo "</div></td></tr>";
            }
            // echo "</table>";
        }
                    echo "</div>";
    }
    elseif($_POST["type"]=="heatmap" && !in_array("",$_POST['gene_expression'])){
        include('./show.html');
        if(strpos($_POST["genes"],"display")!==false){
            foreach($_POST['gene_expression'] as $field_name){
                include('./mysql_con.php');
            }
        }
        $_SESSION["type"]=$_POST["type"];
        if (in_array("pvalue",$_POST["plot_options"]))
            {$_SESSION["qvalue"]=1;}
        else
            {$_SESSION["qvalue"]=0;}
        
        $_SESSION['gene_expression']=$_POST['gene_expression'];
        $field_name=array_values($_POST['gene_expression'])[0];
        echo "<div id=\"allplots\">";
        echo "<span id=\"plots\"></span>";
        echo "<div><button onclick=PrintDiv(this) style=\"display:none\" class=\"printbuttons\" title=\"Button to print\">print image</button>";
        if (strpos($_POST["genes"],"display")!==false)
            {
                $display=$_POST["genes"];
                echo "<img src=\"./create_gene_expression.php?field_name=$field_name&display=$display\" style='max-width:100%; height: auto; image-rendering: auto;'/>";
            }
        if (strpos($_POST["SNPs"],"display")!==false)
            {
                $display=$_POST["SNPs"];
                echo "<img src=\"./create_snps.php?display=$display\" style='max-width:100%; height: auto;'/>";      
            }
        echo "<img src=\"./create_heatmap.php?\" style='max-width:100%; height: auto;image-rendering: auto;'/>";
        echo "</div>";
        echo "</div>";
//    include('./create_heatmap.php');
    }
    elseif($_POST["type"]=="association" ){
    include('./show_association.html');
        
        if(isset($_POST["output"]) and !in_array("",$_POST['gene_expression'])){
            include('./show_association.php');
        }
    }
    else{
        include('./show.html');

    }

    include('./footer.php');
}


?>


