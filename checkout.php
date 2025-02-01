<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
   exit;
}

$paypal_client_id = 'AShRyvPUHnPOyBZuk2-qlkdiglDjMkMTQMdM1hpcJKNWfC1YAbVYN0eof5lNvdnGigCu-eZqiPizOZwc';
$is_sandbox = true;

if(isset($_POST['order']) || isset($_POST['payment_id'])){
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $address = 'Flat No. '. $_POST['flat'] .', '. $_POST['street'] .', '. $_POST['city'] .', '. $_POST['state'] .', '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];
   $payment_id = isset($_POST['payment_id']) ? $_POST['payment_id'] : null;

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){
      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, payment_id) VALUES(?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $payment_id]);

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'Order placed successfully!';
   }else{
      $message[] = 'Your cart is empty';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   
   <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypal_client_id; ?>&currency=USD&intent=capture"></script>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">
   <form action="" method="POST" id="checkout-form">
      <h3>Your Orders</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items = [];
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].')';
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= '$'.$fetch_cart['price'].' x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">Your cart is empty!</p>';
         }
         $total_products = implode(', ', $cart_items);
      ?>
         <input type="hidden" name="total_products" value="<?= htmlspecialchars($total_products, ENT_QUOTES); ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
         <div class="grand-total">Grand Total: <span>$<?= number_format($grand_total, 2); ?></span></div>
      </div>

      <h3>Place Your Order</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Your Name:</span>
            <input type="text" name="name" placeholder="Enter your name" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>Your Number:</span>
            <input type="number" name="number" placeholder="Enter your number" class="box" min="0" max="9999999999" required>
         </div>
         <div class="inputBox">
            <span>Your Email:</span>
            <input type="email" name="email" placeholder="Enter your email" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Payment Method:</span>
            <select name="method" class="box" id="payment-method" required>
               <option value="cash on delivery">Cash on Delivery</option>
               <option value="paypal">PayPal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Address Line 01:</span>
            <input type="text" name="flat" placeholder="Flat number" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Street:</span>
            <input type="text" name="street" placeholder="Street name" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>City:</span>
            <input type="text" name="city" placeholder="City" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>State:</span>
            <input type="text" name="state" placeholder="State" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Country:</span>
            <input type="text" name="country" placeholder="Country" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Pin Code:</span>
            <input type="number" name="pin_code" placeholder="Pin Code" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" id="submit-btn" class="btn <?= ($grand_total > 0)?'':'disabled'; ?>" value="Place Order">
      
      <div class="btn-pay">
         <div id="paypal-button-container" style="display: none;"></div>
      </div>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<script>
document.getElementById('payment-method').addEventListener('change', function() {
    const paypalContainer = document.getElementById('paypal-button-container');
    const submitBtn = document.getElementById('submit-btn');

    if (this.value === 'paypal') {
        paypalContainer.style.display = 'block';
        submitBtn.style.display = 'none';
    } else {
        paypalContainer.style.display = 'none';
        submitBtn.style.display = 'block';
    }
});

const grandTotal = <?= json_encode($grand_total, JSON_NUMERIC_CHECK) ?>;
const totalProducts = <?= json_encode($total_products) ?>;

if (grandTotal > 0) {
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: { value: grandTotal.toFixed(2) },
                    description: totalProducts
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('Transaction completed: ' + details.id);

                // Append transaction ID to the form
                const form = document.getElementById('checkout-form');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'paypal_transaction_id';
                input.value = details.id;
                form.appendChild(input);

                form.submit();
            });
        }
    }).render('#paypal-button-container');
}
</script>


</body>
</html>
