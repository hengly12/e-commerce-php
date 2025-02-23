<?php
if(isset($message)) {
    echo '<div class="message-container">';
    foreach($message as $msg) {
        $type = 'info';
        if (stripos($msg, 'success') !== false) {
            $type = 'success';
            $icon = 'check-circle';
        } elseif (stripos($msg, 'error') !== false || stripos($msg, 'already exist') !== false || stripos($msg, 'not matched') !== false) {
            $type = 'error';
            $icon = 'exclamation-circle';
        } elseif (stripos($msg, 'warning') !== false) {
            $type = 'warning';
            $icon = 'exclamation-triangle';
        } else {
            $icon = 'info-circle';
        }
        
        echo '
        <div class="message message--' . $type . '">
            <div class="message__content">
                <i class="fas fa-' . $icon . ' message__icon"></i>
                <span>' . htmlspecialchars($msg) . '</span>
            </div>
            <i class="fas fa-times message__close" onclick="closeMessage(this);"></i>
        </div>';
    }
    echo '</div>';
}

$current_page = basename($_SERVER['PHP_SELF']);

try {
    $count_wishlist_items = $conn->prepare("SELECT COUNT(*) FROM `wishlist` WHERE user_id = ?");
    $count_wishlist_items->execute([$user_id]);
    $total_wishlist_counts = $count_wishlist_items->fetchColumn();

    $count_cart_items = $conn->prepare("SELECT COUNT(*) FROM `cart` WHERE user_id = ?");
    $count_cart_items->execute([$user_id]);
    $total_cart_counts = $count_cart_items->fetchColumn();

    $user_profile = null;
    if(!empty($user_id)) {
        $select_profile = $conn->prepare("SELECT name FROM `users` WHERE id = ?");
        $select_profile->execute([$user_id]);
        $user_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>

<header class="header">
    <section class="flex">
        <a href="home.php" class="logo">
            <img src="images/logo.png" alt="Shop Logo">Shop<span>.</span>
        </a>

        <nav class="navbar">
            <a href="home.php" class="<?= ($current_page == 'home.php') ? 'active' : ''; ?>">Home</a>
            <a href="shop.php" class="<?= ($current_page == 'shop.php') ? 'active' : ''; ?>">Shop</a>
            <a href="contact.php" class="<?= ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="cart.php">
                <i class="fa-solid fa-cart-plus"></i>
                <span>(<?= htmlspecialchars($total_cart_counts); ?>)</span>
            </a>
            <div id="user-btn" class="fas fa-user"></div>
        </div>

        <div class="profile">
            <?php if($user_profile): ?>
                <p><?= htmlspecialchars($user_profile["name"]); ?></p>
                <!-- <a href="update_user.php" class="btn">update profile</a> -->
                <a href="components/user_logout.php" 
                   class="delete-btn" 
                   onclick="return confirm('logout from the website?');">logout</a>
            <?php else: ?>
                <p>please login first!</p>
                <a href="user_login.php" class="option-btn">login</a>
            <?php endif; ?>
        </div>
    </section>
</header>

<style>
    .navbar a {
        text-decoration: none;
        color: black;
        padding: 10px 15px;
        margin: 5px;
        border: 2px solid transparent; 
        transition: all 0.3s ease-in-out;
    }

    .navbar a.active {
        color: #007bff !important;
        font-weight: bold;
        border-bottom: 2px solid #007bff;
        width: fit-content;
    }



</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            document.querySelectorAll(".message").forEach(message => {
                message.classList.add("hide"); 
                setTimeout(() => message.remove(), 500); 
            });
        }, 3000); 
    });
</script>
