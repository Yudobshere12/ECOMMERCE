<?php
// Example: Fetching cart items for a specific user
$user_id = $_SESSION['user_id'];

$sql = "SELECT cart.id, products.name, products.price, cart.quantity
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    echo "Product: " . $row['name'] . "<br>";
    echo "Price: ₱" . $row['price'] . "<br>";
    echo "Quantity: " . $row['quantity'] . "<br><hr>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cart - Redstore</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      max-width: 1000px;
      margin: auto;
      background: #f8f8f8;
    }
    h1 {
      text-align: center;
      margin-bottom: 10px;
    }
    .logout-container {
      text-align: right;
      margin-bottom: 20px;
    }
    .logout-container button {
      padding: 10px 15px;
      background: #dc3545;
      color: white;
      border: none;
      cursor: pointer;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }
    table th, table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    .cart-info {
      display: flex;
      align-items: center;
    }
    .cart-info img {
      width: 80px;
      margin-right: 10px;
    }
    input[type='number'] {
      width: 50px;
    }
    .remove {
      color: red;
      text-decoration: none;
      font-weight: bold;
    }
    .total-price {
      margin-top: 20px;
    }
    .total-price table {
      width: 100%;
    }
    #order-summary {
      margin-top: 20px;
      font-size: 18px;
      background: #fff3cd;
      padding: 15px;
      border-left: 5px solid #ffc107;
    }
    #place-order {
      margin-top: 20px;
      padding: 10px 20px;
      background: #28a745;
      color: white;
      border: none;
      font-size: 16px;
      cursor: pointer;
    }
    #receipt {
      display: none;
      background: #ffffff;
      padding: 20px;
      margin-top: 30px;
      border: 1px solid #ccc;
    }
    #receipt h2 {
      text-align: center;
    }
  </style>
</head>
<body>

  <!-- Logout Button -->
 <div class="logout-container">
  <form action="logout.php" method="post">
    <button type="submit">Logout</button>
  </form>
</div>


  <h1>Your Cart</h1>

  <table id="cart-table">
    <tr>
      <th>Product</th>
      <th>Quantity</th>
      <th>Subtotal</th>
      <th>Remove</th>
    </tr>

    <!-- Product Rows (static demo) -->
    <tr>
      <td class="cart-info">
        <img src="image/NIKE+DUNK+LOW.avif" alt="DUNK LOW " />
        <div>
          <p>Red Printed T-Shirt</p>
          <small>Price: $25.00</small>
        </div>
      </td>
      <td><input type="number" value="1" min="1"/></td>
      <td>$25.00</td>
      <td><a href="#" class="remove">Remove</a></td>
    </tr>
    <tr>
      <td class="cart-info">
        <img src="image/product-2.jpg" alt="HRX Black Shoes" />
        <div>
          <p>Black Shoes</p>
          <small>Price: $60.00</small>
        </div>
      </td>
      <td><input type="number" value="1" min="1"/></td>
      <td>$60.00</td>
      <td><a href="#" class="remove">Remove</a></td>
    </tr>
    <tr>
      <td class="cart-info">
        <img src="image/shoes.avif" alt="NIKE DUNK LOW WOMENS" />
        <div>
          <p>Blue Jeans</p>
          <small>Price: $40.00</small>
        </div>
      </td>
      <td><input type="number" value="1" min="1"/></td>
      <td>$40.00</td>
      <td><a href="#" class="remove">Remove</a></td>
    </tr>
    <tr>
      <td class="cart-info">
        <img src="image/product-8.jpg" alt="BLACK FOSSIL RUNNING WATCH" />
        <div>
          <p>White Hoodie</p>
          <small>Price: $35.00</small>
        </div>
      </td>
      <td><input type="number" value="1" min="1"/></td>
      <td>$35.00</td>
      <td><a href="#" class="remove">Remove</a></td>
    </tr>
    <tr>
      <td class="cart-info">
        <img src="image/exclusive.png" alt="Sport Watch" />
        <div>
          <p>Sport Watch</p>
          <small>Price: $50.00</small>
        </div>
      </td>
      <td><input type="number" value="1" min="1"/></td>
      <td>$50.00</td>
      <td><a href="#" class="remove">Remove</a></td>
    </tr>
  </table>

  <div class="total-price">
    <table></table>
  </div>

  <p id="order-summary"></p>
  <button id="place-order">Place Order</button>
  
  <div id="receipt">
    <h2>Thank You for Your Order!</h2>
    <p id="receipt-details"></p>
    <p><strong>Total Paid:</strong> <span id="receipt-total"></span></p>
    <p><em>This receipt can be printed or saved for your records.</em></p>
  </div>

  <script>
    function updateCart() {
      const rows = document.querySelectorAll("#cart-table tr");
      let subtotal = 0;
      let summary = [];

      for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        if (!row.querySelector(".cart-info")) continue;

        const name = row.querySelector("p").innerText;
        const priceText = row.querySelector("small").innerText;
        const price = parseFloat(priceText.replace("Price: $", ""));
        const qtyInput = row.querySelector("input");
        const qty = parseInt(qtyInput.value);
        const subtotalCell = row.querySelectorAll("td")[2];

        const itemTotal = price * qty;
        subtotal += itemTotal;

        subtotalCell.innerText = `$${itemTotal.toFixed(2)}`;
        summary.push(`${qty} × ${name}`);
      }

      const tax = subtotal * 0.10;
      const total = subtotal + tax;

      document.querySelector(".total-price table").innerHTML = `
        <tr><td>Subtotal</td><td>$${subtotal.toFixed(2)}</td></tr>
        <tr><td>Tax (10%)</td><td>$${tax.toFixed(2)}</td></tr>
        <tr><td><strong>Total</strong></td><td><strong>$${total.toFixed(2)}</strong></td></tr>
      `;

      const summaryText = summary.length > 0
        ? `You ordered: ${summary.join(", ")}.`
        : "Your cart is empty.";
      document.getElementById("order-summary").innerText = summaryText;

      return { summaryText, total };
    }

    document.addEventListener("DOMContentLoaded", () => {
      document.querySelectorAll("input[type='number']").forEach(input => {
        input.addEventListener("change", updateCart);
      });

      document.querySelectorAll(".remove").forEach(link => {
        link.addEventListener("click", function (e) {
          e.preventDefault();
          this.closest("tr").remove();
          updateCart();
        });
      });

      updateCart();

      document.getElementById("place-order").addEventListener("click", () => {
        const { summaryText, total } = updateCart();
        if (summaryText.includes("empty")) {
          alert("Your cart is empty!");
          return;
        }
        document.getElementById("receipt-details").innerText = summaryText;
        document.getElementById("receipt-total").innerText = `$${total.toFixed(2)}`;
        document.getElementById("receipt").style.display = "block";
        alert("Thank you for your order!");
      });
    });
  </script>

</body>
</html>
