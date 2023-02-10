<?php
  $item = $_POST["item"];
  $file = fopen("selected.txt", "a");
  fwrite($file, $item . "\n");
  fclose($file);
?>