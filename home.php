<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';
// include './chat.html';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">
   <style>
.home-products {
   padding: 4rem 2rem;
   max-width: 1200px;
   margin: 0 auto;
}

.section-heading {
   text-align: center;
   font-size: 2.5rem;
   margin-bottom: 3rem;
   color: #333;
   text-transform: capitalize;
}

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
.home{
   height: 400px;
}
</style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="home-bg">

<section class="home">
   <div class="swiper home-slider">
      <div class="swiper-wrapper">
      <?php
            $select_slides = $conn->prepare("SELECT * FROM `slides`");
            $select_slides->execute();
            if ($select_slides->rowCount() > 0) {
               while ($fetch_slides = $select_slides->fetch(PDO::FETCH_ASSOC)) {
         ?>

            <div class="swiper-slide slide">
               <div class="image">
                  <img src="uploaded_img/<?= $fetch_slides['image']; ?>" >
               </div>
               <div class="content">
                  <h3><?= $fetch_slides['title']; ?></h3>
                  <span>$<?= $fetch_slides['subtitle']; ?>
               </div>
            </div>
         <?php
               }
            } else {
               echo "<p class='empty-message'>No products available</p>";
            }
         ?>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

      <!-- <div class="swiper-pagination"></div> -->
   </div>
</section>

</div>

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
   <h1 class="section-heading">Our Products</h1>
   <div class="products-container">
      <?php
      $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="product-card">
         <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
         
         <div class="product-images">
            <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>">
               <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="<?= $fetch_product['name']; ?>">
            </a>
         </div>
         
         <div class="product-details">
            <h3 class="product-name"><?= $fetch_product['name']; ?></h3>
            <div class="product-price">
               <span class="currency">$</span>
               <span class="amount"><?= $fetch_product['price']; ?></span>
            </div>
            
            <div class="product-actions">
               <div class="quantity-wrapper">
                  <input type="number" name="qty" class="quantity-input" min="1" max="99" 
                     onkeypress="if(this.value.length == 2) return false;" value="1">
               </div>
               <button type="submit" class="add-to-cart-btn" name="add_to_cart">
                  Add to Cart
               </button>
            </div>
         </div>
      </form>
      <?php
         }
      } else {
         echo '<p class="empty-message">No products added yet!</p>';
      }
      ?>
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

      document.addEventListener("DOMContentLoaded", function () {
         const toggleBtn = document.querySelector('.toggle-btn');
         const swiperContainer = document.querySelector('.swiper-container');

         if (toggleBtn && swiperContainer) {
            toggleBtn.addEventListener('click', function () {
                  if (swiperContainer.style.display === 'none') {
                     swiperContainer.style.display = 'block';
                     swiper.autoplay.start();
                  } else {
                     swiperContainer.style.display = 'none';
                     swiper.autoplay.stop(); 
                  }
            });
         }
      });

   });
</script>



</body>
</html>