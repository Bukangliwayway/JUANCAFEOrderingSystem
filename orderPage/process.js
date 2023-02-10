function addToCart(element) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "save_to_cart.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      // alert("Item added to cart");
      displayCart();
    }
  };
  // element.style.pointerEvents = "none";
  xhr.send("item=" + element.innerHTML);
}

function removeFromCart(element) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "remove_from_cart.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      displayCart();
    }
  };
  // element.style.pointerEvents = "auto";
  xhr.send("item=" + element.innerHTML);
}

function displayCart() {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "selected.txt", true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      var items = xhr.responseText.split("\n");
      var html = "";
      for (var i = 0; i < items.length; i++) {
        if (items[i].trim().length > 0) {
          html += "<div onclick='removeFromCart(this)' style='pointer-events:auto;'>" + items[i] + "</div>";
        }
      }

      document.getElementById("cart").innerHTML = html;
    }
  };
  xhr.send();
}