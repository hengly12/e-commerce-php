<?php
include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

include 'components/wishlist_cart.php';

try {
    $select_products = $conn->prepare("SELECT * FROM `products`");
    $select_products->execute();
    $products = $select_products->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error_message = "Unable to load products at this time.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/style.css">

    <style>
        .products-container {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
   gap: 2rem;
   padding: 1rem;
}

.product-card {
   background: #fff;
   border-radius: 10px;
   box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
   transition: transform 0.3s ease, box-shadow 0.3s ease;
   overflow: hidden;
   padding: 10px;
}

.product-card:hover {
   transform: translateY(-5px);
   box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.product-images {
   position: relative;
   padding-top: 75%;
   overflow: hidden;
   aspect-ratio: 8 / 6 !important;
   background: #fff !important;
}

.product-images img {
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   object-fit: contain;
   transition: transform 0.3s ease;
   background: #fff;
}

.product-card:hover .product-image img {
   transform: scale(1.05);
}

.product-details {
   padding: 1.5rem;
}

.product-name {
   font-size: 1.2rem;
   color: #333;
   margin-bottom: 0.5rem;
   height: 2.8em;
   overflow: hidden;
   display: -webkit-box;
   -webkit-line-clamp: 2;
   -webkit-box-orient: vertical;
}

.product-price {
   font-size: 1.5rem;
   color: #2c3e50;
   font-weight: bold;
   margin-bottom: 1rem;
}

.currency {
   font-size: 1rem;
   vertical-align: top;
}

.product-actions {
   display: flex;
   gap: 1rem;
   align-items: center;
}

.quantity-wrapper {
   flex: 0 0 80px;
}

.quantity-input {
   width: 100%;
   padding: 0.5rem;
   border: 1px solid #ddd;
   border-radius: 5px;
   text-align: center;
}

.add-to-cart-btn {
   flex: 1;
   background: #3498db;
   color: white;
   border: none;
   padding: 0.8rem 1.5rem;
   border-radius: 5px;
   cursor: pointer;
   transition: background 0.3s ease;
   text-transform: capitalize;
}

.add-to-cart-btn:hover {
   background: #2980b9;
}

.empty-message {
   text-align: center;
   grid-column: 1 / -1;
   padding: 2rem;
   color: #666;
   font-size: 1.2rem;
}

@media (max-width: 768px) {
   .home-products {
      padding: 2rem 1rem;
   }
   
   .section-heading {
      font-size: 2rem;
   }
   
   .products-container {
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
   }
   
   .product-details {
      padding: 1rem;
   }
}
    </style>
</head>
<body>
    <?php include 'components/user_header.php'; ?>

    <section class="products">
        
        <div class="products-container">
            <?php 
            if (isset($error_message)): ?>
                <p class="error"><?= htmlspecialchars($error_message) ?></p>
            <?php 
            elseif (!empty($products)): 
                foreach($products as $product): ?>
                    <form action="" method="post" class="product-card">
                        <input type="hidden" name="pid" value="<?= htmlspecialchars($product['id']) ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                        <input type="hidden" name="price" value="<?= htmlspecialchars($product['price']) ?>">
                        <input type="hidden" name="image" value="<?= htmlspecialchars($product['image_01']) ?>">
                        <div class="product-images">
                            <a href="quick_view.php?pid=<?= htmlspecialchars($product['id']) ?>">
                                <img src="uploaded_img/<?= htmlspecialchars($product['image_01']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            </a>
                        </div>

                        <div class="product-details"></div>
                        
                        
                        <div class="product-price">
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount"><?= htmlspecialchars($product['price'])  ?></span>
                            </div>
                        </div>
                        <div class="product-actions">
                            <div class="quantity-wrapper">
                                <input type="number" 
                                   name="qty" 
                                   class="quantity-input" 
                                   min="1" 
                                   max="99" 
                                   onkeypress="if(this.value.length == 2) return false;" 
                                   value="1">
                            </div>
                        
                            <button type="submit" class="add-to-cart-btn" name="add_to_cart">
                                Add to Cart
                            </button>
                        </div>
                    </form>
                    
                <?php endforeach; 
            else: ?>
                <p class="empty">No products found!</p>
            <?php endif; ?>
        </div>
    </section>
    

    <?php include 'components/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>