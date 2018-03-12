<?php
session_start();//starts up the session to make sure variables can be passed across php files

$name=$_POST["Name1"];//gets the name of the position
if(isset($_POST["view"]) and !empty($_SESSION['MidPoint'])){//if statement to change viewing window range

    $range=explode(':',str_replace('-',':',$Position));//explodes the position if given as chr:bp
        $chr=$range[0];
        $MidPoint= $_SESSION['MidPoint'];
        if(isset($_POST["view"])){
            $diff=$range[2]-$range[1];#gets the distance between the start and end bp
            $mid=($range[1]+$range[2])/2;#gets the mid point between the start and the end
            $shift=0;
            switch($_POST["view"]){//controls to shift and change displayed
            case '<<':
                $shift=-floor(($diff)*0.25);//shift left by 25%
                break;
            case '<':
                $shift=-floor(($diff)*0.1);//shift left by 10%
                break;
            case 'Center':
                
                $range[1]=$MidPoint-$diff/2;//centers everything back on the midpoint
                $range[2]=$MidPoint+$diff/2;
                break;
            case '>':
                $shift=floor(($diff)*0.1);//shift right by 10%
                break;
            case '>>':
                $shift=floor(($diff)*0.25);//shift right by 25%
                break;
            case '1x':
                $range[1]=$mid-1000000;//resets zoom value as +-1MB
                $range[2]=$mid+1000000;
                break;
            case '2x':
                $range[1]=$mid-0.5*$diff/2;//changes the zoom by 1/2
                $range[2]=$mid+0.5*$diff/2;
                break;
            case '-2x':
                $range[1]=$mid-2*$diff/2;//changes the zoom by 2
                $range[2]=$mid+2*$diff/2;
                break;
            case '10x':
                $range[1]=$mid-0.1*$diff/2;//changes the zoom by 0.1
                $range[2]=$mid+0.1*$diff/2;
                break;
            case '-10x':
                $range[1]=$mid-10*$diff/2;//changes the zoom by 10
                $range[2]=$mid+10*$diff/2;
                break;
            default:
                $_SESSION['HighlightingPosition']=$_POST["HighlightingPosition"];
                
                break;
            }
            unset($_POST["view"]);
            $Position=$range[0].":".intval($range[1]+$shift)."-".intval($range[2]+$shift); #builds the $Position varaible
       }
}
elseif(isset($_POST["run"]) or !isset($MidPoint)){
    $con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");//connects to thirty-four database
    if (mysqli_connect_errno()) //test to make sure it connects
        echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";
    $name=str_replace(',','',$_POST["Name1"]);
    if(strtoupper(substr($name,0,3))=='CHR')//if using chr:bp just pulls the coordinates
        {
            $_POST["subtype"]="ChrBp";
            $name=trim($name);
            $SplitChrBp=explode(":",$name);//expands the name into individual pieces if possible
            $chr=$SplitChrBp[0];//gets chromosomes
            $MidPoint=$SplitChrBp[1];//gets mid point
            $Position='';//resets Position variable 
        }
    elseif(strtoupper(substr($name,0,2))=='RS')//finds rs for snp name
        {
            if($_POST["Name1"]=='rs1191511')//hack to change snp name
                $name='rs176213';
            $_POST["subtype"]="snp147";//sets up subtype to use snps
            $sql="select chr,startbp,endbp from snp147 where name=\"".$name."\"";//build sql command
            $result=mysqli_query($con,$sql);//does query with command
            if (mysqli_query($con,$sql)){//pulls out the info
                while($row=mysqli_fetch_array($result)){
                    $chr=$row["chr"];//fills out the chr and bp data
                    $MidPoint=$row["startbp"];
                }
                if($_POST["Name1"]=='rs1191511')//hack for rs1191511 
                    $name='rs1191511';
            }
        $Position='';
        }
    elseif(strtoupper(substr($name,0,2))=='NR')//finds nr for gene name
        {
            $_POST["subtype"]="refGene1";
            $sql="select chrom,cdsStart,txStart from refGene where name=\"".$_POST["Name1"]."\"";//build sql command
            $result=mysqli_query($con,$sql);
            if (mysqli_query($con,$sql)){
                while($row=mysqli_fetch_array($result)){
                    $chr=$row["chrom"];//gets chr
                    $start_Positions[]=$row["cdsStart"];
                    $start_Positions[]=$row["txStart"];//gets txt start position
                }
            }
            $MidPoint=intval(array_sum($start_Positions)/count($start_Positions));
            $Position='';
        }
    elseif(strtoupper(substr($name,0,4))=='ENST')//find enst for gene name
        {
            $_POST["subtype"]="enstGene";
            $sql="select chrom,cdsStart,txStart from refGene where name=\"".$_POST["Name1"]."\"";//build sql command
            $result=mysqli_query($con,$sql);
            if (mysqli_query($con,$sql)){
                while($row=mysqli_fetch_array($result)){
                    $chr=$row["chrom"];//gets chr
                    $start_Positions[]=$row["cdsStart"];//gets start position
                    $start_Positions[]=$row["txStart"];
                }
            }
            $MidPoint=intval(array_sum($start_Positions)/count($start_Positions));
            $Position='';
        }
    elseif(strtoupper(substr($name,0,4))=='ENSG')//finds ensg but doesnt' work yet
        {
            $_POST["subtype"]="ensgGene";
            echo "not working<br>";
        }
    elseif(strtoupper(substr($name,0,2))=='UC')//finds UC data
        {
            $_POST["subtype"]="knownGene";
            $sql="select chrom,cdsStart,txStart from knownGene where name=\"".$_POST["Name1"]."\"";//build sql command
            $result=mysqli_query($con,$sql);
            if (mysqli_query($con,$sql)){
                while($row=mysqli_fetch_array($result)){//loops over results from mysql
                    $chr=$row["chrom"];//finds chr 
                    $start_Positions[]=$row["cdsStart"];//gets start position
                    $start_Positions[]=$row["txStart"];
                }
            }
            $MidPoint=intval(array_sum($start_Positions)/count($start_Positions));
            $Position='';
        }
    else//else statement if others don't catch it
        {
            $_POST["subtype"]="refGene2";
            $name=strtoupper($name);
            $_POST["Name1"]=strtoupper($_POST["Name1"]);
            $sql="select chr,Startbp,Endbp,strand from RPKM_expression, refGene where Genename=\"".$_POST["Name1"]."\" and RPKM_expression.Genename=refGene.name2";
            $result=mysqli_query($con,$sql);
            if (mysqli_query($con,$sql)){
                while($row=mysqli_fetch_array($result)){
                    $chr=$row["chr"];
                    if($row["strand"]=='+')
                        $MidPoint=intval($row["Startbp"]);
                    else
                        $MidPoint=intval($row["Endbp"]);
                }
            }
            $Position='';
        }
    
    mysqli_close($con);
    if(!isset($chr))//catches errors
        $chr='chr0';
    if(!isset($MidPoint))
        $MidPoint=0;
    $Position=$chr.":".intval($MidPoint-1000000)."-".intval($MidPoint+1000000);//sets start range 1mb
    $_SESSION['MidPoint']=$MidPoint;//copies midpoint to session variable
    unset($_POST["run"]);//removes the run variable from the post session
    if(isset($_POST["HiCButton"])){//buttons to select all or none of the data
        switch($_POST["HiCButton"]){//switch statement from hiCbutton
        case 'Select All':
            $_POST["gene_expression"]=array('AD','AO','BL','DLPFC','HC','LG','LI','LV','RV','OV','PA','PO','SB','SX','GM12878','H1','IMR90','MES','MSC','NPC','TRO');//add short names for tissues here to include new data in select all
            break;
        case 'Unselect All':
            $_POST["gene_expression"]=[""];//removes all selected tissues
            break;
        }
        $Position=$_SESSION['Position'];
    }  
    if(isset($_POST["plot_options_Button"])){//plot option buttons
        switch($_POST["plot_options_Button"]){
        case 'Display All'://displays all the extra data
            $_POST["genes"]="displaysquished";
            $_POST["fires"]="display";
            $_POST["TADs"]="display";
            $_POST["SE"]="displayTESE";
            $_POST["CTCF"]="display";
            $_POST["CHIP"]="display";
            $_POST["SNPs"]="display";
            break;
        case 'Hide All'://turns off all extra data
            $_POST["genes"]="hide";
            $_POST["fires"]="hide";
            $_POST["TADs"]="hide";
            $_POST["SE"]="hide";
            $_POST["CTCF"]="hide";
            $_POST["CHIP"]="hide";
            $_POST["SNPs"]="hide";
            break;
        }
        $Position=$_SESSION['Position'];
    }  
}

$_SESSION['HighlightingPosition']=$_POST["HighlightingPosition"];

if(!empty($_SESSION['HighlightingPosition'])){//sets highlighting positions
    $range=explode('-',$_SESSION['HighlightingPosition']);
    #need to test and control to make sure there are two numbers
}else{#if no highlight is given then it is created around the midpoint
    $range[0]=$MidPoint-10000;
    $range[1]=$MidPoint+10000;
    $_SESSION['HighlightingPosition']=$range[0].'-'.$range[1];#saves highlight variables
}
$HighlightingPosition=$_SESSION["HighlightingPosition"];

$_SESSION['Position']=$Position;
?>