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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script defer src="main.php"></script>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="coffee-bg">
      <div class="greetings">
        <h2>Hi, Kath</h2>
        <h5>What's your Favorite Drink</h5>
      </div>
      <div class="cart">
        <img src="assets/cart.png" alt="cart" width="30px" />
      </div>
    </div>
    <div class="beverages-div">
      <div class="upper">
        <h2>Categories</h2>
        <div class="sections">
          <?php
          foreach($categories as $category){
            echo'<div class="category"><img src="assets/category-icon.png"  width="20px" alt="icon" /><h3>'.$category.'</h3>
              </div>';
          }
          ?>
        </div>
      </div>
      <div class="beverages">
        <?php
          foreach($beverages as $beverage){
            echo '<div class="card" data-category="'.$beverage->category.'" id="'.$beverage->id.'">
            <img class= "beverage-image", src="'.$beverage->image.'" alt="'.$beverage->name.'"/>
            <div class="beverage-info">
            <h2 class="beverage-title">'.$beverage->name.'</h2>
            <span class="beverage-price">'.$beverage->price.'</span>
            </div>
            <a href="#"><img src="assets/add.png" alt="add-icon" width="20px" class="add-icon" /></a>
            </div>';
          }
        ?>
      </div>
    </div>

    <div class="cart-div" style="display: none">
        <div class="cart-container">
          <h2>My Shopping Cart</h2>
          <span>Total <span id="item-count">0</span> items</span>
          <div class="cart-container"></div>
        </div>
    </div>

    <div id="item" style="display: none">
      <div class="bg-color">
        <div class="beverage-container">
          <div class="transparent-bg">
            <h1 id="item-title">Title</h1>
            <h2 id="item-price">Price</h2>
          </div>
        </div>
        <div class="sizes-container"></div>
        <h2>AddOns:</h2>
        <div class="align-horizontal">
          <div class="addons-container">
            <div class="items-addons"></div>
          </div>
          <div class="align-vertical">
            <div class="price-container">
              <h2> Total Price:</h2>
              <span id="order-price"> P 0</span>
            </div>
            <div class="submission-container">
              <button id="item-order">LockIn</button>
              <button id="item-cancel">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="addon-select" style="display: none">
      <div class="addon-info">
        <div class="transparent-bg">
          <span id="addon-tab-title">Addon Title</span>
          <span id="addon-tab-price">Addon Price</span>
        </div>
      </div>
      <div class="addon-edit-count">
        <button id="addon-dec">-</button>
        <input type="text" id="addon-tab-count" value="0" />
        <button id="addon-inc">+</button>
      </div>
      <button id="addon-submit">Done</button>
    </div>

    <div class="coffee-bg" id="lower">
    <div class="items-final">
      <h3>Items</h3>
      <span id="total-count">0</span>
    </div>
    <div class="price-final">
      <h3>Total Price</h3>
      <span id="total-price">0</span>
    </div>  
    </div>
  </body>
</html>
