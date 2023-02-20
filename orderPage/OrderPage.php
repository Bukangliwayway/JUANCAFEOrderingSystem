<?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "JuanCafe";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql1 = "SELECT DISTINCT * FROM Beverage WHERE BeverageSize = 'large'";
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

  while ($row = $result2->fetch_assoc()) 
    $categories[] = $row['BeverageCategory'];
  

  $conn->close();

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script defer src="main.php"></script>
  </head>
  <body>
    <div class="coffee-bg">
      <div class="greetings">
        <h1>Hi, Kath</h1>
        <h5>What's your Favorite Drink</h5>
      </div>
      <div class="cart">
        <img src="#" alt="cart" />
      </div>
    </div>
    <div class="upper">
      <h2>Categories</h2>
      <div class="sections">
        <?php
        foreach($categories as $category){
          echo'<div class="category"><img src="#" alt="icon" /><h2>'.$category.'</h2>
            </div>';
        }
        ?>
      </div>
    </div>
    <div class="beverages">
      <?php
        foreach($beverages as $beverage){
          echo '<div class="card" data-category="'.$beverage->category.'" id="'.$beverage->id.'">
                  <span class="beverage-count">0</span>
                  <img class= "beverage-image", src="'.$beverage->image.'" alt="'.$beverage->name.'" />
                  <div class="beverage-info">
                    <h2 class="beverage-title">'.$beverage->name.'</h2>
                    <h2 class="beverage-price">'.$beverage->price.'</h2>
                  </div>
                  <a href="#"><img src="#" alt="add-icon" /></a>
                </div>';
        }
      ?>
    </div>
    <div class="coffee-bg">
      <h3>Items</h3>
      <span id="item">0</span>
      <h3>Total Price</h3>
      <span id="total-price">P 0</span>
    </div>
  </body>
</html>
