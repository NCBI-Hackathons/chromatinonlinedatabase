<?php
session_start();

$name=$_POST["Name1"];
if(isset($_POST["view"]) and !empty($_SESSION['MidPoint'])){

        $range=explode(':',str_replace('-',':',$Position));
        $chr=$range[0];
        $MidPoint= $_SESSION['MidPoint'];
        if(isset($_POST["view"])){
            $diff=$range[2]-$range[1];#gets the distance between the start and end bp
            $mid=($range[1]+$range[2])/2;#gets the mid point between the start and the end
            $shift=0;
            switch($_POST["view"]){
            case '<<':
                $shift=-floor(($diff)*0.25);
                break;
            case '<':
                $shift=-floor(($diff)*0.1);
                break;
            case 'Center':
                
                $range[1]=$MidPoint-$diff/2;
                $range[2]=$MidPoint+$diff/2;
                break;
            case '>':
                $shift=floor(($diff)*0.1);
                break;
            case '>>':
                $shift=floor(($diff)*0.25);
                break;
            case '1x':
                $range[1]=$mid-1000000;
                $range[2]=$mid+1000000;
                break;
            case '2x':
                $range[1]=$mid-0.5*$diff/2;
                $range[2]=$mid+0.5*$diff/2;
                break;
            case '-2x':
                $range[1]=$mid-2*$diff/2;
                $range[2]=$mid+2*$diff/2;
                break;
            case '10x':
                $range[1]=$mid-0.1*$diff/2;
                $range[2]=$mid+0.1*$diff/2;
                break;
            case '-10x':
                $range[1]=$mid-10*$diff/2;
                $range[2]=$mid+10*$diff/2;
                break;
            default:
                $_SESSION['HighlightingPosition']=$_POST["HighlightingPosition"];
                
                break;
            }
            unset($_POST["view"]);
            $Position=$range[0].":".intval($range[1]+$shift)."-".intval($range[2]+$shift); 
       }
}
elseif(isset($_POST["run"]) or !isset($MidPoint)){

    $con=mysqli_connect("thirty-four.its.unc.edu","hic_db_user","ro_hic","hic_db");
    if (mysqli_connect_errno()) 
        echo "Failed to connect to MySQLs <b> UNC Hi-C </b> database: <b>" . mysqli_connect_error()."</b>";

    $name=str_replace(',','',$_POST["Name1"]);

    //ifstatements to do name testing to identify the type of name used
    if(strtoupper(substr($name,0,3))=='CHR')
        {
            $_POST["subtype"]="ChrBp";
            $name=trim($name);
            
        $SplitChrBp=explode(":",$name);
        $chr=$SplitChrBp[0];
        $MidPoint=$SplitChrBp[1];
        $Position='';

        }
    elseif(strtoupper(substr($name,0,2))=='RS')
        {
            if($_POST["Name1"]=='rs1191511')
                $name='rs176213';
            
            $_POST["subtype"]="snp147";
            $sql="select chr,startbp,endbp from snp147 where name=\"".$name."\"";
            $result=mysqli_query($con,$sql);
            if (mysqli_query($con,$sql)){
                while($row=mysqli_fetch_array($result)){
                    $chr=$row["chr"];
                    $MidPoint=$row["startbp"];
                    
            }
                if($_POST["Name1"]=='rs1191511')
                    $name='rs1191511';
            }
        $Position='';

        }
    elseif(strtoupper(substr($name,0,2))=='NR')
        {
            $_POST["subtype"]="refGene1";
                    $sql="select chrom,cdsStart,txStart from refGene where name=\"".$_POST["Name1"]."\"";
        $result=mysqli_query($con,$sql);
        if (mysqli_query($con,$sql)){
            while($row=mysqli_fetch_array($result)){
                $chr=$row["chrom"];
                $start_Positions[]=$row["cdsStart"];
                $start_Positions[]=$row["txStart"];
            }
        }
        $MidPoint=intval(array_sum($start_Positions)/count($start_Positions));
        $Position='';

        }
    elseif(strtoupper(substr($name,0,4))=='ENST')
        {
            $_POST["subtype"]="enstGene";
            $sql="select chrom,cdsStart,txStart from refGene where name=\"".$_POST["Name1"]."\"";
            $result=mysqli_query($con,$sql);
            if (mysqli_query($con,$sql)){
                while($row=mysqli_fetch_array($result)){
                    $chr=$row["chrom"];
                    $start_Positions[]=$row["cdsStart"];
                    $start_Positions[]=$row["txStart"];
                }
            }
            $MidPoint=intval(array_sum($start_Positions)/count($start_Positions));
            $Position='';
        }
    elseif(strtoupper(substr($name,0,4))=='ENSG')
        {
            $_POST["subtype"]="ensgGene";
            echo "not working<br>";
        }
    elseif(strtoupper(substr($name,0,2))=='UC')
        {
            $_POST["subtype"]="knownGene";
                    $sql="select chrom,cdsStart,txStart from knownGene where name=\"".$_POST["Name1"]."\"";
        $result=mysqli_query($con,$sql);
        if (mysqli_query($con,$sql)){
            while($row=mysqli_fetch_array($result)){
                $chr=$row["chrom"];
                $start_Positions[]=$row["cdsStart"];
                $start_Positions[]=$row["txStart"];
                
            }
        }
        $MidPoint=intval(array_sum($start_Positions)/count($start_Positions));
        $Position='';

        }
    else
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
#    $chr=intval(str_replace("chr","",$chr));
    if(!isset($chr))
        $chr='chr0';
    if(!isset($MidPoint))
        $MidPoint=0;
    $Position=$chr.":".intval($MidPoint-1000000)."-".intval($MidPoint+1000000);
    $_SESSION['MidPoint']=$MidPoint;
    unset($_POST["run"]);

    
if(isset($_POST["HiCButton"])){
    switch($_POST["HiCButton"]){
    case 'Select All':
        $_POST["gene_expression"]=array('AD','AO','BL','DLPFC','HC','LG','LI','LV','RV','OV','PA','PO','SB','SX','GM12878','H1','IMR90','MES','MSC','NPC','TRO');
        break;
    case 'Unselect All':
        $_POST["gene_expression"]=[""];
        break;

    }

    $Position=$_SESSION['Position'];

}  

if(isset($_POST["plot_options_Button"])){
    switch($_POST["plot_options_Button"]){
    case 'Display All':
                $_POST["genes"]="displaysquished";
        $_POST["fires"]="display";
        $_POST["TADs"]="display";
        $_POST["SE"]="displayTESE";
        $_POST["CTCF"]="display";
        $_POST["CHIP"]="display";
        $_POST["SNPs"]="display";

        break;
    case 'Hide All':
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

    

    // $_POST['HighlightingPosition']=intval($MidPoint-25000)."-".intval($MidPoint+25000);
}

$_SESSION['HighlightingPosition']=$_POST["HighlightingPosition"];

if(!empty($_SESSION['HighlightingPosition'])){
    $range=explode('-',$_SESSION['HighlightingPosition']);
    #need to test and control to make sure there are two numbers
}else{
    $range[0]=$MidPoint-10000;
    $range[1]=$MidPoint+10000;
    $_SESSION['HighlightingPosition']=$range[0].'-'.$range[1];
}
$HighlightingPosition=$_SESSION["HighlightingPosition"];

$_SESSION['Position']=$Position;
?>