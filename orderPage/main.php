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
    
    $stmt = $conn->prepare("SELECT DISTINCT BeverageSize, BeveragePrice FROM Beverage WHERE BeverageName = ?");
    $stmt->bind_param("s", $beverage->name);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    $sizes = array();
    while($row = $result->fetch_assoc()){
      $size = new stdClass();
      $size->name = $row['BeverageSize'];
      $size->price = $row['BeveragePrice'];
      $sizes[] = $size;
    }
    
    
    
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

var totalAddonsPrice = 0;
var itemSizePrice = 0;

// Beverage Selection Variables
const item = document.getElementById("item");
const order = document.querySelector("#item-order");
const cancelOrder = document.querySelector("#item-cancel");

//Addon Selection Variables
const addonSelection = document.querySelector("#addon-select");
const incAddon = document.querySelector("#addon-inc");
const addOnQuantity = document.querySelector("#addon-tab-count");
const decAddon = document.querySelector("#addon-dec");
const addonSubmit = document.querySelector("#addon-submit");

// Depressing Part

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
    //Set the Beverage Selection Visibility
    item.style.display = "block";

    // Beverage Container
    var beverageID = card.id;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "main.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
      if (xhr.status >= 200 && xhr.status < 300) {
        var data = JSON.parse(xhr.responseText);
        var beverage = data.beverage;
        var sizes = data.sizes;
        var addons = data.addons;

        var itemTitle = document.querySelector("#item-title");
        var itemPrice = document.querySelector("#item-price");
        var itemImage = document.querySelector("#item-image");
        itemTitle.textContent = beverage.name;
        itemPrice.textContent = beverage.price;
        itemImage.src = beverage.image;

        itemSizePrice = beverage.price;
        updateTotalPrice();

        // SIZE CONTAINER
        var sizeContainer = document.querySelector(".sizes-container");

        //Removes every child of the div
        sizeContainer.innerHTML = "";
        
        sizes.forEach((size) => {
          const div = document.createElement("div");
          div.classList.add("size-category");
          const sizeName = document.createElement("span");
          sizeName.textContent = size.name;
          sizeName.value = size.price;
          div.appendChild(sizeName);
          sizeContainer.appendChild(div);
        });

        // ADDONS CONTAINER
        var addonsContainer = document.querySelector(".addons-container");

        //Removes every child of the div
        addonsContainer.innerHTML = "";

        addons.forEach((addon) => {
          var div = document.createElement("div");
          div.classList.add("addons-category");

          // Create the addon name span element
          var addonName = document.createElement("span");
          addonName.classList.add("addon-name");
          addonName.textContent = addon.name;
          addonName.value = addon.price;

          // Create the addon count span element
          var addonCount = document.createElement("span");
          addonCount.classList.add("addon-count");
          addonCount.setAttribute("class", "addon-count");
          addonCount.textContent = 0;

          // Create the addon image element
          var addonImg = document.createElement("img");
          addonImg.src = addon.image;
          addonImg.alt = addon.name;

          // Append the elements to the div
          div.appendChild(addonName);
          div.appendChild(addonCount);
          div.appendChild(addonImg);
          addonsContainer.appendChild(div);
        });

        //Set the Visibility of addonSelection
        const clickedAddon = document.querySelectorAll(".addons-category");
        clickedAddon.forEach((button) => {
          button.addEventListener("click", () => {
            const addonSpan = button.querySelector('.addon-count');
            addonSpan.setAttribute('id', 'chosen-addon');
            addonSelection.style.display = "block";
            decAddon.disabled = true;
          });
        });

        //Set the Value of Total Price
        const clickedSize = document.querySelectorAll(".size-category");
        clickedSize.forEach((chosen) => {
          chosen.addEventListener("click", () => {
            itemSizePrice = chosen.querySelector("span").value;
            
            var itemPrice = document.querySelector("#item-price");
            itemPrice.textContent = itemSizePrice;
            
            updateTotalPrice();
          });
        });

      } else console.log("The request failed!");
    };
    xhr.send("beverageID=" + beverageID);
  });
});


//Addon Selection Buttons
addonSubmit.addEventListener("click", () => {
  const activeAddonCount = document.querySelector("#chosen-addon");
  const addonDiv = document.querySelectorAll(".addons-category");

  activeAddonCount.innerHTML = addOnQuantity.value;
  activeAddonCount.removeAttribute('id');

  //Resets its value
  totalAddonsPrice = 0;

  addonDiv.forEach((div) => {
    totalAddonsPrice += div.querySelector(".addon-count").textContent * div.querySelector(".addon-name").value;
  });
  
  updateTotalPrice();
  
  addonSelection.style.display = "none";
  addOnQuantity.value = 0;

});

// Beverage Selection Variables
order.addEventListener("click", () => {
  // ....SUM CODE TO SAVE THE CONTENT OF THE DATA
  item.style.display = "none";
});

cancelOrder.addEventListener("click", () => {
  // ....SUM CODE TO SAVE THE CONTENT OF THE DATA
  item.style.display = "none";
});


incAddon.addEventListener("click", () => {
  addOnQuantity.value = parseInt(addOnQuantity.value) + 1;
  decAddon.disabled = false;
});

decAddon.addEventListener("click", () => {
  addOnQuantity.value = parseInt(addOnQuantity.value) - 1;
  if (parseInt(addOnQuantity.value) < 1) decAddon.disabled = true;
});

function updateTotalPrice() {
  var totalPrice = document.querySelector("#order-price");
  totalPrice.textContent = parseFloat(totalAddonsPrice) + parseFloat(itemSizePrice);
  
}