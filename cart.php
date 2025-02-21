<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header('location:user_login.php');
    exit();
}

if(isset($_POST['delete'])) {
    try {
        $cart_id = filter_var($_POST['cart_id'], FILTER_SANITIZE_NUMBER_INT);
        $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ? AND user_id = ?");
        $delete_cart_item->execute([$cart_id, $user_id]);
    } catch(PDOException $e) {
        $message[] = 'Error deleting item from cart';
        error_log($e->getMessage());
    }
}

if(isset($_GET['delete_all'])) {
    try {
        $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart_item->execute([$user_id]);
        header('location:cart.php');
        exit();
    } catch(PDOException $e) {
        $message[] = 'Error clearing cart';
        error_log($e->getMessage());
    }
}

if(isset($_POST['update_qty'])) {
    try {
        $cart_id = filter_var($_POST['cart_id'], FILTER_SANITIZE_NUMBER_INT);
        $qty = filter_var($_POST['qty'], FILTER_SANITIZE_NUMBER_INT);
        
        if($qty > 0 && $qty <= 99) {
            $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ? AND user_id = ?");
            $update_qty->execute([$qty, $cart_id, $user_id]);
            $message[] = 'Cart quantity updated';
        }
    } catch(PDOException $e) {
        $message[] = 'Error updating quantity';
        error_log($e->getMessage());
    }
}

try {
    $grand_total = 0;
    $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $select_cart->execute([$user_id]);
    $cart_items = $select_cart->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $message[] = 'Error fetching cart items';
    error_log($e->getMessage());
    $cart_items = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .shopping-cart { padding: 2rem; }
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        .cart-table th { background: #f5f5f5; padding: 1rem; text-align: left; }
        .cart-table td { padding: 1rem; border-bottom: 1px solid #eee; vertical-align: middle; }
        .cart-table img { width: 80px; height: 80px; object-fit: cover; }
        .qty-input { width: 60px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
        .update-btn { background: none; border: none; color: #666; cursor: pointer; padding: 0.5rem; }
        .delete-btn { background: #ff4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; }
        .cart-total { text-align: right; padding: 1rem; background: #f9f9f9; border-radius: 4px; }
        .fa-edit { height: unset !important; margin-left: 5px; }
        .cart-total p { font-size: 1.2rem; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: 0.8rem 1.5rem; border-radius: 4px; text-decoration: none; }
        .empty { text-align: center; padding: 2rem; font-size: 1.2rem; color: #666; }
        .message { background: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
    </style>
</head>
<body>
    
    <?php include 'components/user_header.php'; ?>
    
    <section class="products shopping-cart">
        <h3 class="heading">Shopping Cart</h3>
        
        <?php
  
        
        if(!empty($cart_items)){
        ?>
        
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cart_items as $item): 
                    $sub_total = ($item['price'] * $item['quantity']);
                    $grand_total += $sub_total;
                ?>
                <tr>
                    <td>
                        <img src="uploaded_img/<?= htmlspecialchars($item['image']); ?>" alt="Product Image">
                        <a href="quick_view.php?pid=<?= htmlspecialchars($item['pid']); ?>" class="fa-regular fa-eye"></a>
                    </td>
                    <td><?= htmlspecialchars($item['name']); ?></td>
                    <td>$<?= htmlspecialchars($item['price']); ?></td>
                    <td>
                        <form action="" method="post" style="display: flex; align-items: center;">
                            <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['id']); ?>">
                            <input type="number" name="qty" class="qty-input" min="1" max="99" 
                                   onkeypress="if(this.value.length == 2) return false;" 
                                   value="<?= htmlspecialchars($item['quantity']); ?>">
                            <button type="submit" class="update-btn fas fa-edit" name="update_qty"></button>
                        </form>
                    </td>
                    <td>$<?= htmlspecialchars($sub_total); ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['id']); ?>">
                            <input type="submit" value="Delete" class="delete-btn" 
                                   onclick="return confirm('Delete this from cart?');" name="delete">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="cart-total">
            <p>Grand total : <span>$<?= htmlspecialchars($grand_total); ?>/-</span></p>
            <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" 
               onclick="return confirm('Delete all from cart?');">Remove all Products</a>
            <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">Checkout</a>
        </div>
        
        <?php
        } else {
            echo '<p class="empty">Your cart is empty</p>';
        }
        ?>
        
    </section>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="js/script.js"></script>
    
</body>
</html>