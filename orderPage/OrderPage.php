<?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "JuanCafe";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $beveragesRes = $conn->query("SELECT DISTINCT * FROM Beverage WHERE BeverageSize = 'large'");

  $beverages = array();

  if ($beveragesRes->num_rows > 0) {
    while ($row = $beveragesRes->fetch_assoc()) {
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

  $categoriesRes = $conn->query("SELECT DISTINCT BeverageCategory FROM Beverage;");

  $categories = array();

  while ($row = $categoriesRes->fetch_assoc()) 
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
      <span id="item-count">0</span>
      <h3>Total Price</h3>
      <span id="total-price">P 0</span>
    </div>


    <!-- beverage selection -->
    <div id="item" style="display: none">
      <div class="beverage-container">
        <h1 id="item-title">Title</h1>
        <h2 id="item-price">Price</h2>
        <img src="#" alt="beverage" id="item-image"/>
      </div>
      <div class="sizes-container">
        
      </div>
      <h2>AddOns:</h2>
      <div class="addons-container">
        <div class="items-addons"></div>
      </div>
      <div class="price-container">
        <span> Total Price:</span>
        <span id="total-price"> P 0</span>
      </div>
      <div class="submission-container">
        <button id="item-order">LockIn</button>
        <button id="item-cancel">Cancel</button>
      </div>
    </div>
  
  <div id="addon-select" style="display: none">
    <span id="addon-tab-title">Addon Title</span>
    <span id="addon-tab-price">Addon Price</span>
    <img src="#" alt="" class="addon-img">
    <button id="addon-dec">-</button>
    <input type="text" id="addon-tab-count" value=0>
    <button id="addon-inc">+</button>
    <button id="addon-submit">Done</button>
  </div>
  
  </body>
</html>
