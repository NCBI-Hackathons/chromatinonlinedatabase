<?php

function cleanData($data) {
  $data = trim($data);
  $data = stripslashes($data);
  //$data = addslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>