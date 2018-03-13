<?php

$showDefault = true;

include 'function.php';

if (isset($_REQUEST['type'])) {
	$type = addslashes ( strip_tags ( trim ( $_GET['type'] ) ) );
}

if ($section == 'image') {
	$showDefault = false;
	include 'mysql_con.php';
	createImage($fieldName, $pdisplay, $_GET['midpoint'], $hicData, $_GET['position'], $name_array);
}

if ($showDefault) {
	echo 'Hi!';
}
?>