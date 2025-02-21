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

// Rest of the PHP logic remains the same...
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
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <style>
      .checkout-container {
         max-width: 1200px;
         margin: 2rem auto;
         padding: 0 20px;
      }

      .checkout-grid {
         display: grid;
         grid-template-columns: 2fr 1fr;
         gap: 2rem;
      }

      .checkout-form {
         background: #fff;
         border-radius: 10px;
         box-shadow: 0 0 20px rgba(0,0,0,0.05);
         padding: 2rem;
      }

      .order-summary {
         background: #fff;
         border-radius: 10px;
         box-shadow: 0 0 20px rgba(0,0,0,0.05);
         padding: 2rem;
         height: fit-content;
      }

      .section-title {
         font-size: 1.5rem;
         color: #333;
         margin-bottom: 1.5rem;
         padding-bottom: 1rem;
         border-bottom: 2px solid #f0f0f0;
      }

      .form-grid {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 1.5rem;
      }

      .form-group {
         margin-bottom: 1.5rem;
      }

      .form-label {
         display: block;
         font-size: 0.9rem;
         color: #666;
         margin-bottom: 0.5rem;
      }

      .form-input {
         width: 100%;
         padding: 0.8rem;
         border: 1px solid #ddd;
         border-radius: 6px;
         font-size: 1rem;
         transition: border-color 0.3s;
      }

      .form-input:focus {
         border-color: #4A90E2;
         outline: none;
      }

      .form-select {
         width: 100%;
         padding: 0.8rem;
         border: 1px solid #ddd;
         border-radius: 6px;
         font-size: 1rem;
         background: #fff;
         cursor: pointer;
      }

      .order-item {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 1rem 0;
         border-bottom: 1px solid #f0f0f0;
      }

      .order-item:last-child {
         border-bottom: none;
      }

      .item-name {
         color: #333;
         font-weight: 500;
      }

      .item-price {
         color: #666;
      }

      .grand-total {
         margin-top: 1.5rem;
         padding-top: 1.5rem;
         border-top: 2px solid #f0f0f0;
         display: flex;
         justify-content: space-between;
         align-items: center;
         font-size: 1.2rem;
         font-weight: 600;
      }

      .total-amount {
         color: #2ECC71;
      }

      .btn-submit {
         background: #2ECC71;
         color: white;
         border: none;
         padding: 1rem 2rem;
         border-radius: 6px;
         font-size: 1rem;
         cursor: pointer;
         width: 100%;
         margin-top: 1.5rem;
         transition: background 0.3s;
      }

      .btn-submit:hover {
         background: #27AE60;
      }

      .btn-submit.disabled {
         background: #ccc;
         cursor: not-allowed;
      }

      #paypal-button-container {
         margin-top: 1.5rem;
      }

      @media (max-width: 768px) {
         .checkout-grid {
            grid-template-columns: 1fr;
         }

         .form-grid {
            grid-template-columns: 1fr;
         }
      }
   </style>

   <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypal_client_id; ?>&currency=USD&intent=capture"></script>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="checkout-container">
   <form action="" method="POST" id="checkout-form">
      <div class="checkout-grid">
         <div class="checkout-form">
            <h2 class="section-title">Billing Details</h2>
            
            <div class="form-grid">
               <div class="form-group">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="name" class="form-input" placeholder="Enter your full name" maxlength="20" required>
               </div>
               
               <div class="form-group">
                  <label class="form-label">Phone Number</label>
                  <input type="number" name="number" class="form-input" placeholder="Enter your phone number" required>
               </div>
               
               <div class="form-group">
                  <label class="form-label">Email Address</label>
                  <input type="email" name="email" class="form-input" placeholder="Enter your email" maxlength="50" required>
               </div>
               
               <div class="form-group">
                  <label class="form-label">Payment Method</label>
                  <select name="method" class="form-select" id="payment-method" required>
                     <option value="cash on delivery">Cash on Delivery</option>
                     <option value="paypal">PayPal</option>
                  </select>
               </div>
            </div>

            <h2 class="section-title">Shipping Address</h2>
            
            <div class="form-grid">
               <div class="form-group">
                  <label class="form-label">Address Line 1</label>
                  <input type="text" name="flat" class="form-input" placeholder="Apartment, suite, unit, etc." required>
               </div>
               
               <div class="form-group">
                  <label class="form-label">Street</label>
                  <input type="text" name="street" class="form-input" placeholder="Street address" required>
               </div>
               
               <div class="form-group">
                  <label class="form-label">City</label>
                  <input type="text" name="city" class="form-input" placeholder="City" required>
               </div>
               
               <div class="form-group">
                  <label class="form-label">State</label>
                  <input type="text" name="state" class="form-input" placeholder="State/Province" required>
               </div>
               
               <div class="form-group">
                  <label class="form-label">Country</label>
                  <input type="text" name="country" class="form-input" placeholder="Country" required>
               </div>
               
               <div class="form-group">
                  <label class="form-label">ZIP Code</label>
                  <input type="number" name="pin_code" class="form-input" placeholder="ZIP/Postal code" required>
               </div>
            </div>
         </div>

         <div class="order-summary">
            <h2 class="section-title">Order Summary</h2>
            
            <?php
            $grand_total = 0;
            $cart_items = [];
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if($select_cart->rowCount() > 0){
               while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                  $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].')';
                  $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
                  $grand_total += $sub_total;
            ?>
               <div class="order-item">
                  <span class="item-name"><?= $fetch_cart['name']; ?> (x<?= $fetch_cart['quantity']; ?>)</span>
                  <span class="item-price">$<?= number_format($sub_total, 2); ?></span>
               </div>
            <?php
               }
            }else{
               echo '<p class="empty">Your cart is empty!</p>';
            }
            ?>

            <input type="hidden" name="total_products" value="<?= htmlspecialchars(implode(', ', $cart_items), ENT_QUOTES); ?>">
            <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
            
            <div class="grand-total">
               <span>Total Amount</span>
               <span class="total-amount">$<?= number_format($grand_total, 2); ?></span>
            </div>

            <button type="submit" name="order" id="submit-btn" class="btn-submit <?= ($grand_total > 0)?'':'disabled'; ?>">
               Place Order
            </button>
            
            <div id="paypal-button-container" style="display: none;"></div>
         </div>
      </div>
   </form>
</div>

<?php include 'components/footer.php'; ?>

<script>
// PayPal and payment method toggle logic remains the same...
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

const grandTotal = <?php echo json_encode($grand_total, JSON_NUMERIC_CHECK); ?>;
const totalProducts = <?php echo json_encode($total_products ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

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
                // Show success message using SweetAlert or custom alert
                Swal.fire({
                    title: 'Payment Successful!',
                    text: 'Your order has been placed successfully',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    // Submit form with payment details
                    const form = document.getElementById('checkout-form');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'payment_id';
                    input.value = details.id;
                    form.appendChild(input);

                    // Add success message to form
                    const messageInput = document.createElement('input');
                    messageInput.type = 'hidden';
                    messageInput.name = 'success_message';
                    messageInput.value = 'Payment completed successfully!';
                    form.appendChild(messageInput);

                    form.submit();
                });
            });
        }
    }).render('#paypal-button-container');
}
</script>

</body>
</html>