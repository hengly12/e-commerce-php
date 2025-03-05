<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>quick view</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">
   <style>
.quick-view {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.quick-view .heading {
    text-align: center;
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 2rem;
    text-transform: capitalize;
}

.quick-view .box {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.quick-view .row {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    align-items: center;
}

.quick-view .row .image-container {
    flex: 1 1 40rem;
}

.quick-view .row .content {
    flex: 1 1 40rem;
}

.quick-view .image-container .main-image {
    height: 30rem;
    margin-bottom: 1rem;
}

.quick-view .image-container .main-image img {
    height: 100%;
    width: 100%;
    object-fit: cover;
    border-radius: 5px;
}

.quick-view .image-container .sub-image {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.quick-view .image-container .sub-image img {
    width: 7rem;
    height: 7rem;
    object-fit: cover;
    cursor: pointer;
    border-radius: 5px;
    transition: 0.2s linear;
}

.quick-view .image-container .sub-image img:hover {
    transform: scale(1.1);
}

.quick-view .content .name {
    font-size: 2rem;
    color: #333;
    margin-bottom: 1rem;
}

.quick-view .content .flex {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin: 1rem 0;
}

.quick-view .content .price {
    font-size: 2rem;
    color: #e74c3c;
    margin-right: 1rem;
}

.quick-view .content .price span {
    font-size: 1.5rem;
}

.quick-view .content .qty {
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 7rem;
    font-size: 1.2rem;
}

.quick-view .content .details {
    line-height: 2;
    font-size: 1.4rem;
    color: #666;
    margin: 1rem 0;
}

.quick-view .flex-btn {
    margin-top: 2rem;
}

.quick-view .btn {
    display: inline-block;
    padding: 1rem 2.5rem;
    background: #2ecc71;
    color: #fff;
    font-size: 1.2rem;
    text-transform: capitalize;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.2s linear;
    border: none;
}

.quick-view .btn:hover {
    background: #27ae60;
    transform: translateY(-2px);
}

.empty {
    text-align: center;
    font-size: 1.5rem;
    color: #666;
    padding: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .quick-view .row {
        flex-direction: column;
    }
    
    .quick-view .image-container .main-image {
        height: 25rem;
    }
    
    .quick-view .content .name {
        font-size: 1.8rem;
    }
}
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="quick-view">

   <h1 class="heading">View Product</h1>

   <?php
     $pid = $_GET['pid'];
     $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?"); 
     $select_products->execute([$pid]);
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <div class="row">
         <div class="image-container">
            <div class="main-image">
               <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
            </div>
            <div class="sub-image">
               <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
               <img src="uploaded_img/<?= $fetch_product['image_02']; ?>" alt="">
               <img src="uploaded_img/<?= $fetch_product['image_03']; ?>" alt="">
            </div>
         </div>
         <div class="content">
            <div class="name"><?= $fetch_product['name']; ?></div>
            <div class="flex">
               <div class="price"><span>$</span><?= $fetch_product['price']; ?><span>/-</span></div>
               <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
            </div>
            <div class="details"><?= $fetch_product['details']; ?></div>
            <div class="flex-btn">
               <input type="submit" value="add to cart" class="btn" name="add_to_cart">
            </div>
         </div>
      </div>
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products added yet!</p>';
   }
   ?>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>