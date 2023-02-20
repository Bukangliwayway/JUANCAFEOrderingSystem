<?php 
  if(isset($_POST['beverageID'])){
    $beverageID = $_POST['beverageID'];
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "JuanCafe";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    
    $stmt = $conn->prepare("SELECT * FROM Beverage WHERE BeverageID = ?");
    $stmt->bind_param("i", $beverageID);
    
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    $row = $result->fetch_assoc();
    $beverage = new stdClass();
    $beverage->id = $row['BeverageID'];
    $beverage->name = $row['BeverageName'];
    $beverage->image = $row['BeverageImagePath'];
    $beverage->price = $row['BeveragePrice'];
    $beverage->size = $row['BeverageSize'];
    $beverage->category = $row['BeverageCategory'];
    
    $stmt->close();
    $conn->close();
    
    echo $beverage->name;
    exit;
  }
  
?>

const categoryButtons = document.querySelectorAll(".category");
const beverages = document.querySelectorAll(".card");
categoryButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const selectedCategory = button.textContent.replace(/\s+/g, "");
    beverages.forEach((beverage) => {
      if (beverage.dataset.category.replace(/\s+/g, "") == selectedCategory) {
        beverage.style.display = "block";
      } else {
        beverage.style.display = "none";
      }
    });
  });
});


beverages.forEach((card) => {
  card.addEventListener("click", () => {
    card.classList.add("active");
    while (card.firstChild) card.removeChild(card.firstChild);

    var beverageID = card.id;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "main.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function(){
      console.log(xhr.response);
    }
    xhr.send("beverageID=" + beverageID);
  });
});


