<?php

include '../components/connect.php';

session_start();

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);
   $row = $select_admin->fetch(PDO::FETCH_ASSOC);

   if($select_admin->rowCount() > 0){
      $_SESSION['admin_id'] = $row['id'];
      header('location:products.php');
   }else{
      $message[] = 'incorrect username or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">
   <style>
   body {
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
   }

   .form-container {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
   }

   .form-container form {
      background: rgba(255, 255, 255, 0.95);
      padding: 3rem;
      border-radius: 15px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
      text-align: center;
   }

   .logo-login {
      margin-bottom: 2rem;
   }

   .logo-login img {
      max-width: 150px;
      height: auto;
   }

   .box {
      width: 100%;
      margin: 1rem 0;
      padding: 1.2rem 1.4rem;
      border: 2px solid #eee;
      border-radius: 8px;
      background: #f9f9f9;
      font-size: 1.1rem;
      color: #333;
      transition: all 0.3s ease;
   }

   .box:focus {
      border-color: #667eea;
      background: #fff;
      box-shadow: 0 0 10px rgba(102, 126, 234, 0.1);
   }

   .password-container {
      position: relative;
      width: 100%;
   }

   .password-container .box {
      padding-right: 45px;
   }

   .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #666;
      font-size: 1.2rem;
      padding: 0;
   }

   .password-toggle:hover {
      color: #667eea;
   }

   .btn {
      width: 100%;
      padding: 1.2rem;
      background: linear-gradient(to right, #667eea, #764ba2);
      color: #fff;
      font-size: 1.2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 1rem;
   }

   .btn:hover {
      background: linear-gradient(to right, #764ba2, #667eea);
      transform: translateY(-2px);
   }

   .message {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background: #fff;
      padding: 1rem 2rem;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 2rem;
      z-index: 1000;
      animation: slideDown 0.3s ease-out;
   }

   .message span {
      color: #e74c3c;
      font-size: 1.1rem;
   }

   .message i {
      cursor: pointer;
      color: #666;
      transition: color 0.3s ease;
   }

   .message i:hover {
      color: #e74c3c;
   }

   @keyframes slideDown {
      from {
         transform: translate(-50%, -100%);
         opacity: 0;
      }
      to {
         transform: translate(-50%, 0);
         opacity: 1;
      }
   }

   @media (max-width: 480px) {
      .form-container form {
         padding: 2rem;
      }
   }
   </style>
</head>
<body>

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
?>

<section class="form-container">
    <form action="" method="post">
        <div class="logo-login">
            <img src="../images/logo.png" alt="Logo">
        </div>
        <input type="text" name="name" required placeholder="username" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <div class="password-container">
            <input type="password" name="pass" required placeholder="password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <button type="button" class="password-toggle" onclick="togglePassword()">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <input type="submit" value="login" class="btn" name="submit">
    </form>
</section>

<script>
   function togglePassword() {
    const passwordInput = document.querySelector('input[name="pass"]');
    const toggleButton = document.querySelector('.password-toggle i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.classList.remove('fa-eye');
        toggleButton.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleButton.classList.remove('fa-eye-slash');
        toggleButton.classList.add('fa-eye');
    }
   }
</script>
   
</body>
</html>