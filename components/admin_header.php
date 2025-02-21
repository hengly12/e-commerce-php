<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }

   $current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <style>
      .header .flex .navbar a {
         text-decoration: none;
         color: black;
         padding: 10px;
      }

      .header .flex .navbar a.active {
         color: white;
         background: #007bff;
         border-radius: 5px;
         font-weight: bold;
      }
   </style>
</head>
<body>

<header class="header">
   <section class="flex">
      <nav class="navbar">
         <a href="products.php" class="<?= ($current_page == 'products.php') ? 'active' : ''; ?>">Products</a>
         <a href="admin_accounts.php" class="<?= ($current_page == 'admin_accounts.php') ? 'active' : ''; ?>">Admins</a>
         <a href="messages.php" class="<?= ($current_page == 'messages.php') ? 'active' : ''; ?>">Messages</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="update_profile.php" class="btn">update profile</a>
         <a href="../components/admin_logout.php" class="delete-btn" onclick="return confirm('logout from the website?');">logout</a> 
      </div>

   </section>
</header>

</body>
</html>
