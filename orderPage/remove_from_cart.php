<?php
  $item = $_POST["item"];
  $cart = file("selected.txt");
  $new_cart = array();
  foreach ($cart as $line) {
    if (trim($line) != $item) {
      $new_cart[] = $line;
    }
  }
  file_put_contents("selected.txt", implode("", $new_cart));
?>