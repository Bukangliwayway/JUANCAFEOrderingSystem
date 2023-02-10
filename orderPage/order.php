<?php 
  require '../header.php';
?>

<script type="text/javascript" src="/qt/orderPage/process.js"></script>
<main>  
  <!-- <img src="#" alt="a huge logo"> -->
  <br>
  <div class="milktea">
    <div onclick="addToCart(this)" id="m1">Milktea 1</div>
    <div onclick="addToCart(this)">Milktea 2</div>
    <div onclick="addToCart(this)">Milktea 3</div>
  </div>
  <div class="coffee">
    <div onclick="addToCart(this)">Coffee 1</div>
    <div onclick="addToCart(this)">Coffee 2</div>
    <div onclick="addToCart(this)">Coffee 3</div>
  </div>
    <br>
    <div id="cart"></div>
</main>

<?php
  require "footer.php";
?>
