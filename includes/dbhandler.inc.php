<?php 
  $dbh = mysqli_connect("localhost", "root", "", "JuanCafeOrderingSystem", "3306");
  if(!$dbh) die("Connection Failed :( ".mysqli_connect_error());
  
