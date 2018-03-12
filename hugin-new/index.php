<?php
#base file that gets called first when the page is hit
#
#this page can be replace to redirect or hide a site

ini_set("max_execution_time", 0);//sets the time out so won't result in a time out error
$email="jsmartin at email.unc.edu";
if (isset($_POST['action'])&&!isset($_POST['reset'])){#if 
	$action=$_POST['action'];#does what ever action is called
}else {
	$action = 'initialization';#sets initialization flag
}


if ($action == 'initialization'){//initialization code
    include('./initialization.php');//contains starting parameters
    include('./header.php');//general header
    include('./show.html');//html with interface
    include('./footer.php');//general footer
}
elseif ($action == 'list_products')
{
    $Position=$_POST["Position"];//copes posted position to variable
    asort($_POST['gene_expression']);//sorts the sample names to keep consistent order

    $_SESSION['HighlightingPosition']=$_POST["HighlightingPosition"];//copies highlighting positions to the session from post
    include('./header.php');//general header
    include('./HTML_controls.php');//php to handle the backend of the controls
    
    if($_POST["type"]=='browser'){//if the virutual 4C is select (browser mode)
        include('./show.html');
        if($_POST['gene_expression']!=[""]){
            if($_POST['gene_expression']!=[""]){
                foreach($_POST['gene_expression'] as $field_name){//loops over selected samples to get hic data
                    include('./mysql_con.php');//php to get mysql data for hic
                }
            }
            $_SESSION["type"]=$_POST["type"];

            echo "<div id=\"allplots\">";//sets up div to be called to print out all the graphs
            echo "<span id=\"plots\"></span>";
            foreach($_POST['gene_expression'] as $field_name){//loops over each selected sample
                echo "<div> <input type=\"button\" style=\"display:none\" class=\"removebuttons\"  onclick=removeimage(this,\"".$field_name."\") title=\"Button to removed desired dataset\" value=\"Remove ".$field_name."\" >";//individual buttons to remove individual tissue/cells
                echo "<input type=\"button\" style=\"display:none\" class=\"printbuttons\" onclick=PrintDiv(this) title=\"Button to print data window\" value=\"Print ".$field_name."\" >";//individual buttons to print individual tissue/cells
                if (strpos($_POST["genes"],"display")!==false)//makes sure gene position and gene expression is displayed
                    {
                        $display=$_POST["genes"];//gets genes displayed selection
                        echo "<img src=\"./create_gene_expression.php?field_name=$field_name&display=$display\" style='max-width:100%; height: auto;'/>";//uses the create_gene_expression.php file to generate image with the proper tissue/cell using the display argument from the dropdown
                    }
                if (strpos($_POST["fires"],"display")!==false)//tests if FIRES are to be displayed
                    {
                        $display=$_POST["fires"];//gets the fires displayed selection
                        echo "<img src=\"./create_FIRES_image.php?field_name=$field_name\" style='max-width:100%; height: auto;'/>";//uses the crete_FIRES_image.php to generate image and display for proper tissue/cells
                    }
                if (strpos($_POST["TADs"],"display")!==false)//tests if TADS are to be displayed
                    {
                        $display=$_POST["TADs"];//gets arguments for TAD display
                        echo "<img src=\"./create_TAD_image.php?field_name=$field_name\" style='max-width:100%; height: auto;'/>";//generates and displays TADs 
                    }
                if (strpos($_POST["SE"],"display")!==false)//tests if enhancers are to be displayed
                    {
                        $display=$_POST["SE"];//gets arguments for enhancer display
                        echo "<img src=\"./create_SE_image.php?field_name=$field_name&display=$display\" style='max-width:100%; height: auto;'/>";//generates and displays enhancers using arguments
                    }
                if (strpos($_POST["CTCF"],"display")!==false)//tests if CTCF proteins are to be displayed
                    {
                        $display=$_POST["CTCF"];//gets arguments for CTCF display
                        echo "<img src=\"./create_CTCF.php?field_name=$field_name\" style='max-width:100%; height: auto;'/>";//generates and displays ctcf data      
                    }
                if (strpos($_POST["SNPs"],"display")!==false)//tests if SNPs are to be displayed
                    {
                        $display=$_POST["SNPs"];//gets snp arguments
                        echo "<img src=\"./create_snps.php?display=$display\" style='max-width:100%; height: auto;'/>";//generates and displays SNPs with approprate arguments
                    }
                if (strpos($_POST["CHIP"],"display")!==false)//tests if CHIP data is to be displayed
                    {
                        $display=$_POST["CHIP"];//gets chip arguments
                        echo "<img src=\"./create_chip.php?field_name=$field_name\" style='max-width:100%; height: auto;'/>";//generates and displays chip with aproprate arguments
                    }
                $pdisplay=$_POST["py"];#gets p-value argument to determine y-axis 

                #$_SESSION['userhic']=array();//non-working code to read in user data for hi-c
                
#                list($chr,$frag1,$chr2,$frag2,$count,$p,$q,$E)=sscanf(str_replace(" ","",explode("\n",$_POST["output"])),"%d\t%d\t%d\t%d\t%d\t%f\t%f\t%f");
#                print_r($frag1);
#                $_SESSION['userinputhic']=array_map($chr1,$frag1,$frag2,$count,$E,-log10($p));

                //print_r($userhic);
                
                echo "<img  src=\"./create_image.php?field_name=$field_name&pdisplay=$pdisplay\" style='max-width:100%; height: auto;' title=\"Hi-C plots\" />";//data to generate hi-c data
                echo "</div>";
                //      echo "</div></td></tr>";
            }
            // echo "</table>";
        }
                    echo "</div>";
    }
    elseif($_POST["type"]=="heatmap" && !in_array("",$_POST['gene_expression'])){//else if to display heatmap version
        include('./show.html');
        if(strpos($_POST["genes"],"display")!==false){
            foreach($_POST['gene_expression'] as $field_name){//loops over selections
                include('./mysql_con.php');//gets hi-c data for selected tissues and cells
            }
        }
        $_SESSION["type"]=$_POST["type"];       
        $_SESSION['gene_expression']=$_POST['gene_expression'];
        $field_name=array_values($_POST['gene_expression'])[0];
        echo "<div id=\"allplots\">";
        echo "<span id=\"plots\"></span>";
        echo "<div><input type=\"button\" onclick=PrintDiv(this) style=\"display:none\" class=\"printbuttons\" title=\"Button to print\" value=\"Print Image\" >";
        if (strpos($_POST["genes"],"display")!==false)//tests to see if the genes should be displayed
            {
                $display=$_POST["genes"];//gets the arguments for the gene display
                echo "<img src=\"./create_gene_expression.php?field_name=$field_name&display=$display\" style='max-width:100%; height: auto; image-rendering: auto;'/>";//generates and shows the gene information figure
            }
        if (strpos($_POST["SNPs"],"display")!==false)//tests to see if snps should be displayed
            {
                $display=$_POST["SNPs"];//gets the arguments for the snp display
                echo "<img src=\"./create_snps.php?display=$display\" style='max-width:100%; height: auto;'/>";//creates and displays the snps with the proper arguments
            }
        echo "<img src=\"./create_heatmap.php?\" style='max-width:100%; height: auto;image-rendering: auto;'/>";//creates and displays the heatmap
        echo "</div>";
        echo "</div>";
//    include('./create_heatmap.php');
    }
    elseif($_POST["type"]=="association" ){//else if to do the association analysis
        include('./show_association.html');//shows the show_association interface that allows for the loading of snps
        
        if(isset($_POST["output"]) and !in_array("",$_POST['gene_expression'])){
            include('./show_association.php');//calls the show_association.php to run the data analysis to get the snp gene association
        }
    }
    elseif($_POST["type"]=="outtext" ){//else if to just display the text
        include('./show.html');
        foreach($_POST['gene_expression'] as $field_name){//gets the data using the mysql_con.php file
            include('./mysql_con.php');
        }
        include('./text_output.php');//php file to set up data table 
        
        echo "</div>";
                    
    }
    else{//else to catch any mistakes
        include('./show.html');//just pulls up the show.html page

    }

    include('./footer.php');//general footer that closes the page
}


?>


