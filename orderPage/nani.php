<?php

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "JuanCafe";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql1 = "SELECT * FROM Beverage";
$result1 = $conn->query($sql1);

$beverages = array();

if ($result1->num_rows > 0) {
  while ($row = $result1->fetch_assoc()) {
    $beverage = new stdClass();
    $beverage->id = $row['BeverageID'];
    $beverage->name = $row['BeverageName'];
    $beverage->image = $row['BeverageImagePath'];
    $beverage->price = $row['BeveragePrice'];
    $beverage->size = $row['BeverageSize'];
    $beverage->category = $row['BeverageCategory'];
    $beverages[] = $beverage;
  }
}

$sql2 = "SELECT DISTINCT BeverageCategory FROM Beverage;";
$result2 = $conn->query($sql2);

$categories = array();

if ($result1->num_rows > 0) 
  while ($row = $result2->fetch_assoc()) 
    $categories[] = $row['BeverageCategory'];
  

$conn->close();

?>

<h1>
<?php
echo json_encode($categories);

?>
</h1>