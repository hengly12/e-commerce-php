<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';
include './chat.html';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="home-bg">

<section class="home">
   <div class="swiper home-slider">
      <div class="swiper-wrapper">
         <div class="swiper-slide slide">
            <div class="image">
               <img src="images/home-img-1.png" alt="">
            </div>
            <div class="content">
               <span>upto 50% off</span>
               <h3>latest smartphones</h3>
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="image">
               <img src="images/home-img-2.png" alt="">
            </div>
            <div class="content">
               <span>upto 50% off</span>
               <h3>latest watches</h3>
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="image">
               <img src="images/home-img-3.png" alt="">
            </div>
            <div class="content">
               <span>upto 50% off</span>
               <h3>latest headsets</h3>
            </div>
         </div>
      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

      <div class="swiper-pagination"></div>
   </div>
</section>

</div>

<!-- <section class="category">

   <h1 class="heading">shop by category</h1>

   <div class="swiper category-slider">

   <div class="swiper-wrapper">

   <a href="category.php?category=laptop" class="swiper-slide slide">
      <img src="images/icon-1.png" alt="">
      <h3>laptop</h3>
   </a>

   <a href="category.php?category=tv" class="swiper-slide slide">
      <img src="images/icon-2.png" alt="">
      <h3>tv</h3>
   </a>

   <a href="category.php?category=camera" class="swiper-slide slide">
      <img src="images/icon-3.png" alt="">
      <h3>camera</h3>
   </a>

   <a href="category.php?category=mouse" class="swiper-slide slide">
      <img src="images/icon-4.png" alt="">
      <h3>mouse</h3>
   </a>

   <a href="category.php?category=fridge" class="swiper-slide slide">
      <img src="images/icon-5.png" alt="">
      <h3>fridge</h3>
   </a>

   <a href="category.php?category=washing" class="swiper-slide slide">
      <img src="images/icon-6.png" alt="">
      <h3>washing machine</h3>
   </a>

   <a href="category.php?category=smartphone" class="swiper-slide slide">
      <img src="images/icon-7.png" alt="">
      <h3>smartphone</h3>
   </a>

   <a href="category.php?category=watch" class="swiper-slide slide">
      <img src="images/icon-8.png" alt="">
      <h3>watch</h3>
   </a>

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section> -->

<div class="container">
        <section class="about-section">
            <div class="about-image">
                <img src="./images/pc-1.png" alt="Shop showcase">
                <div class="love-bubble">❤️</div>
            </div>
            
            <div class="content">
                <h1 class="title">Who we are?</h1>
                <p class="description">
                    At our store, we provide exceptional quality products and outstanding customer service. 
                    We take pride in offering unique selections and personalized shopping experiences.
                </p>
                <p class="description">
                    We work not only to sell products but to build lasting relationships with our customers, 
                    ensuring satisfaction with every purchase.
                </p>

                <div class="stats-container">
                    <div class="stat-item">
                        <div class="stat-number">50k+</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Products Available</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Customer Satisfaction</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="reviews-section">
            <h2 class="title">Customer Reviews</h2>
            
            <div class="review-card">
                <div class="review-header">
                    <img src="./images/pic-2.png" alt="Reviewer" class="reviewer-avatar">
                    <div>
                        <h3>Sarah Johnson</h3>
                        <div class="review-rating">★★★★★</div>
                    </div>
                </div>
                <p class="review-content">
                    Amazing quality products and exceptional customer service! The team went above 
                    and beyond to help me find exactly what I was looking for. Will definitely be 
                    shopping here again!
                </p>
                <div class="review-date">Posted 2 days ago</div>
            </div>

            <div class="review-card">
                <div class="review-header">
                    <img src="./images/pic-1.png" alt="Reviewer" class="reviewer-avatar">
                    <div>
                        <h3>Mike Thompson</h3>
                        <div class="review-rating">★★★★★</div>
                    </div>
                </div>
                <p class="review-content">
                    The product selection is fantastic and the prices are very competitive. 
                    Shipping was fast and everything arrived in perfect condition. Highly recommend!
                </p>
                <div class="review-date">Posted 1 week ago</div>
            </div>
        </section>


<section class="home-products">

   <h1 class="heading">products</h1>

   <div class="products-wrap" >

   <div class="product-wrapper" style="display: grid; grid-template-columns: repeat(auto-fit, 33rem);grid-gap: 2rem;">

   <?php
     $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6"); 
     $select_products->execute();
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="swiper-slide slide">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fa-regular fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <div class="price"><span>$</span><?= $fetch_product['price']; ?><span></span></div>
         <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products added yet!</p>';
   }
   ?>

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>


<div class="container">
        <div class="product-grid">
            <div class="product-info">
                <span class="tag">FLAGSHIP</span>
                
                <h1 class="headline">Because the ultimate gaming experience demands ultimate performance.</h1>
                
                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">⚡</div>
                        <div class="feature-content">
                            <h3>Raw Power</h3>
                            <p>Equipped with the latest RTX 4090 and Intel i9, delivering unprecedented gaming performance.</p>
                        </div>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">❄️</div>
                        <div class="feature-content">
                            <h3>Advanced Cooling</h3>
                            <p>Custom liquid cooling system ensures peak performance even during intense gaming sessions.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="product-image">
                <img src="./images/pc-2.png" alt="Gaming PC">
                <div class="specs">
                    <div class="specs-grid">
                        <div class="spec-item">
                            <span class="spec-value">RTX 4090</span>
                            <span class="spec-label">Graphics</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-value">Intel i9-13900K</span>
                            <span class="spec-label">Processor</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-value">64GB DDR5</span>
                            <span class="spec-label">RAM</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-value">4TB NVMe</span>
                            <span class="spec-label">Storage</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>







<?php include 'components/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".home-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
    },
});

 var swiper = new Swiper(".category-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
       },
      650: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 4,
      },
      1024: {
        slidesPerView: 5,
      },
   },
});

var swiper = new Swiper(".products-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      550: {
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      },
   },
});



document.addEventListener("DOMContentLoaded", function () {
      var swiperContainer = document.querySelector('.home-slider');

      var swiper = new Swiper('.home-slider', {
         loop: true,
         autoplay: {
            delay: 3000, 
            disableOnInteraction: false 
         },
         navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
         },
         pagination: {
            el: '.swiper-pagination',
            clickable: true,
         },
      });

      document.querySelector('.toggle-btn').addEventListener('click', function () {
         if (swiperContainer.style.display === 'none') {
            swiperContainer.style.display = 'block';
            swiper.autoplay.start();
         } else {
            swiperContainer.style.display = 'none';
            swiper.autoplay.stop(); 
         }
      });
   });
</script>



</body>
</html>