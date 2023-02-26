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
    
    $stmt = $conn->prepare("SELECT DISTINCT BeverageID, BeverageSize, BeveragePrice FROM Beverage WHERE BeverageName = ?");
    $stmt->bind_param("s", $beverage->name);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    $sizes = array();
    while($row = $result->fetch_assoc()){
      $size = new stdClass();
      $size->name = $row['BeverageSize'];
      $size->price = $row['BeveragePrice'];
      $size->id = $row['BeverageID'];
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

var cartOrders = [];
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
    
    categoryButtons.forEach(btn => {
      btn.classList.remove('active-category');
    });

    // Add 'active-category' class to the clicked size button
    button.classList.add('active-category');
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
        itemTitle.value = beverage.id
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
          sizeName.setAttribute("class", "size-name");
          sizeName.setAttribute("beverageID", size.id);
          div.appendChild(sizeName);
          sizeContainer.appendChild(div);
        });

        //Set Default Active Size
        const allSizes = document.querySelectorAll(".size-category");
        allSizes.forEach((size) => {
          var name = size.querySelector(".size-name"); 
          if(name.textContent === beverage.size){
            size.classList.add("active-size");
          }
        });
        
        // ADDONS CONTAINER
        var addonsContainer = document.querySelector(".addons-container");

        //Removes every child of the div
        addonsContainer.innerHTML = "";

        addons.forEach((addon) => {
          var div = document.createElement("div");
          div.classList.add("addons-category");
          div.setAttribute("addonID", addon.id);

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
            var addonTabCount = document.querySelector("#addon-tab-count");
            addonTabCount.value = parseInt(button.querySelector(".addon-count").textContent);
            if(addonTabCount.value == 0) decAddon.disabled = true;
          });
        });

        //Updates the Value of Total Price through Size
        const clickedSize = document.querySelectorAll(".size-category");
        clickedSize.forEach((chosen) => {
          chosen.addEventListener("click", () => {
            itemSize = chosen.querySelector(".size-name");

            var itemTitle = document.querySelector("#item-title");
            itemTitle.value = itemSize.getAttribute('beverageID');

            var itemPrice = document.querySelector("#item-price");
            itemPrice.textContent = itemSize.value;
            
            itemSizePrice = itemSize.value;
            updateTotalPrice();

            clickedSize.forEach(state => {
              state.classList.remove('active-size');
            });

            // Add 'active-size' class to the clicked size button
            chosen.classList.add('active-size');
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
  
  //Reset totaladdon
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
  var addonsObj = [];
  var addons = document.querySelectorAll(".addons-category");
  addons.forEach((addon) => {
    const addonData = {
      "addonDataName": addon.querySelector(".addon-name").textContent,
      "addonDataCount": addon.querySelector(".addon-count").textContent,
      "addonDataID": addon.getAttribute("addonID"),
    };
    addonsObj.push(addonData);
  });
  var orderData = {
    "orderTitle": document.querySelector("#item-title").textContent,
    "orderSize": document.querySelector("div.active-size span.size-name").textContent,
    "orderImg": document.querySelector("#item-image").src,
    "orderPrice": document.querySelector("#order-price").textContent,
    "orderAddons": addonsObj,
    "orderID": document.querySelector("#item-title").value
  }
  cartOrders.push(orderData);
  item.style.display = "none";

  // CART DIV PROTOTYPE

  // create cart-card div element
  const cartCardDiv = document.createElement("div");
  cartCardDiv.classList.add("cart-card");

  // create cart-title h2 element
  const cartTitle = document.createElement("h2");
  cartTitle.textContent = orderData.orderTitle;
  cartTitle.classList.add("cart-title");

  // create cart-sub-container div element
  const cartSubContainerDiv = document.createElement("div");
  cartSubContainerDiv.classList.add("cart-sub-container");

  // create cart-image img element
  var cartImageImg = document.createElement("img");
  cartImageImg.src = orderData.orderImg;
  cartImageImg.classList.add("cart-image");

  // create cart-item-selection div element
  const cartItemSelectionDiv = document.createElement("div");
  cartItemSelectionDiv.classList.add("cart-item-selection");

  // create cart-size span element
  var cartSizeSpan = document.createElement("span");
  cartSizeSpan.textContent = orderData.orderSize;
  cartSizeSpan.classList.add("cart-size");

  // create cart-total span element
  var cartTotalSpan = document.createElement("span");
  cartTotalSpan.textContent = orderData.orderPrice;
  cartTotalSpan.classList.add("cart-total");

  // append cart-size and cart-total to cart-item-selection
  cartItemSelectionDiv.appendChild(cartSizeSpan);
  cartItemSelectionDiv.appendChild(cartTotalSpan);

  // create cart-addons div element
  var cartAddonsDiv = document.createElement("div");
  cartAddonsDiv.classList.add("cart-addons");

  // create contents for cart addons 
  orderData.orderAddons.forEach((data) => {
    var cartAddonCard = document.createElement("div");
    var cartAddonName = document.createElement("span");
    var cartAddonCount = document.createElement("span");

    cartAddonCard.classList.add("cart-addons-card");

    cartAddonName.textContent = data.addonDataName;
    cartAddonCount.textContent = data.addonDataCount;
    
    cartAddonCard.appendChild(cartAddonName);
    cartAddonCard.appendChild(cartAddonCount);
    cartAddonsDiv.appendChild(cartAddonCard);
  });


  // append cart-image, cart-item-selection and cart-addons to cart-sub-container
  cartSubContainerDiv.appendChild(cartImageImg);
  cartSubContainerDiv.appendChild(cartItemSelectionDiv);
  cartSubContainerDiv.appendChild(cartAddonsDiv);
  
  // create an image for X button
  var xButton = document.createElement("img");
  xButton.classList.add("cart-x-button");
  
  // append cart-sub-container and cart-title to cart-card
  cartCardDiv.appendChild(xButton);
  cartCardDiv.appendChild(cartTitle);
  cartCardDiv.appendChild(cartSubContainerDiv);

  const cartCardContainer = document.querySelector(".cart-container");
  cartCardContainer.appendChild(cartCardDiv);

  var totalCount = document.querySelector("#total-count"); 
  var totalPrice = document.querySelector("#total-price"); 
  var total = 0;
  totalCount.textContent = cartOrders.length;
  cartOrders.forEach((order) => {
    total += parseInt(order.orderPrice);
  });
  totalPrice.textContent = total;

});

cancelOrder.addEventListener("click", () => {
  // ....SUM CODE TO SAVE THE CONTENT OF THE DATA
  totalAddonsPrice = 0;
  addons = document.querySelectorAll(".addons-category");
  addons.forEach((addon) => {
    addon.querySelector(".addon-count").textContent = 0;
  });
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