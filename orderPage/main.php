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
    $stmt->close();
    
    $row = $result->fetch_assoc();
    $beverage = new stdClass();
    $beverage->id = $row['BeverageID'];
    $beverage->name = $row['BeverageName'];
    $beverage->image = $row['BeverageImagePath'];
    $beverage->price = $row['BeveragePrice'];
    $beverage->size = $row['BeverageSize'];
    $beverage->category = $row['BeverageCategory'];
    
    $stmt = $conn->prepare("SELECT DISTINCT BeverageSize FROM Beverage WHERE BeverageName = ?");
    $stmt->bind_param("s", $beverage->name);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    $sizes = array();
    while($row = $result->fetch_assoc()) $sizes[] = $row['BeverageSize'];
    
    
    $addons = array();
    $addonRes = $conn->query("SELECT * FROM AddOn");
    if ($addonRes->num_rows > 0) {
      while ($row = $addonRes->fetch_assoc()) {
        $addon = new stdClass();
        $addon->id = $row['AddOnID'];
        $addon->name = $row['AddOnName'];
        $addon->image = $row['AddOnImagePath'];
        $addon->price = $row['AddOnPrice'];
        $addons[] = $addon;
      }
    }
    $conn->close();
    
    $data = array("beverage" => $beverage, "sizes" => $sizes, "addons" => $addons);

    echo json_encode($data); 
    exit;
  }
  
?>

const categoryButtons = document.querySelectorAll(".category");
const beverages = document.querySelectorAll(".card");
const item = document.getElementById("item");
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
    item.classList.add("active");
    
    // Beverage Container
    var beverageID = card.id;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "main.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
      if (xhr.status >= 200 && xhr.status < 300) {
        
        var data = JSON.parse(xhr.responseText);
        var beverage = data.beverage;
        var sizes = data.sizes;
        var addons = data.addons;

        var itemTitle = document.querySelector('#item-title');
        var itemPrice = document.querySelector('#item-price');
        var itemImage = document.querySelector('#item-image');
        itemTitle.textContent = beverage.name;
        itemPrice.textContent = beverage.price;
        itemImage.src = beverage.image;
        
      // Size Container
        var sizeContainer = document.querySelector('.sizes-container');
        //Removes every child of the div
        sizeContainer.innerHTML = "";
        sizes.forEach(size => {
          const div = document.createElement('div');
          div.classList.add("size-category");
          const sizeName = document.createElement('span');
          sizeName.textContent = size;
          div.appendChild(sizeName);
          sizeContainer.appendChild(div);
          
        // AddOns Container    
        var addonsContainer = document.querySelector('.addons-container');

        //Removes every child of the div
        addonsContainer.innerHTML = "";

        addons.forEach(addon => {
            var div = document.createElement("div");  
            div.classList.add("addons-category");

            // Create the addon name span element
            var addonName = document.createElement("span");
            addonName.classList.add("addon-name");
            addonName.textContent = addon.name;
    
            // Create the addon count span element
            var addonCount = document.createElement("span");
            addonCount.classList.add("addon-count");
            addonCount.setAttribute("id", "addon-count");
            addonCount.textContent = 0;
    
            // Create the addon image element
            var addonImg = document.createElement("img");
            addonImg.src = addon.image;
            addonImg.alt = addon.name;
    
            // Append the elements to the div
            div.appendChild(addonName);
            div.appendChild(addonCount);
            div.appendChild(addonImg);
            div.appendChild(sizeName);
            addonsContainer.appendChild(div);
        });
      });
      } else {
        console.log('The request failed!');
      }
    };
    xhr.send("beverageID=" + beverageID);


  });
});


