<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
   $select_admin->execute([$name]);

   if($select_admin->rowCount() > 0){
      $message[] = 'username already exist!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         $insert_admin = $conn->prepare("INSERT INTO `admins`(name, password) VALUES(?,?)");
         $insert_admin->execute([$name, $cpass]);
         header('location:admin_accounts.php');
         $message[] = 'new admin registered successfully!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register admin</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">
   <style>
      .flex-dashboard{
         display: flex;
      }

      .form-container form {
         width: 100%;
         min-width: 450px;
         background: rgba(255, 255, 255, 0.95);
         padding: 3rem;
         border-radius: 15px;
         box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
      }

      .form-container form h3 {
         font-size: 2rem;
         color: #333;
         text-transform: capitalize;
         text-align: center;
         margin-bottom: 2rem;
         position: relative;
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
         text-transform: capitalize;
      }

      .btn:hover {
         background: linear-gradient(to right, #764ba2, #667eea);
         transform: translateY(-2px);
      }

      .message {
         position: fixed;
         top: 20px;
         right: 20px;
         background: #fff;
         padding: 1rem 2rem;
         border-radius: 8px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
         display: flex;
         align-items: center;
         justify-content: space-between;
         gap: 2rem;
         z-index: 1000;
         animation: slideIn 0.3s ease-out;
      }

      .message.success {
         border-left: 4px solid #2ecc71;
      }

      .message.error {
         border-left: 4px solid #e74c3c;
      }

      .message span {
         color: #333;
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

      @keyframes slideIn {
         from {
            transform: translateX(100%);
            opacity: 0;
         }
         to {
            transform: translateX(0);
            opacity: 1;
         }
      }

      @media (max-width: 768px) {
         .flex-dashboard {
            flex-direction: column;
         }
         
         .form-container {
            padding: 1rem;
         }
         
         .form-container form {
            padding: 2rem;
         }
      }
   </style>
</head>
<body>

<div class="flex-dashboard">
   <?php include '../components/admin_header.php'; ?>
   <section class="form-container">
        <form action="" method="post">
            <h3>register user account</h3>
            <input type="text" name="name" required placeholder="enter your username" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            
            <div class="password-container">
                <input type="password" name="pass" required placeholder="enter your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
                <button type="button" class="password-toggle" onclick="togglePassword('input[name=pass]', '#passToggle')">
                    <i id="passToggle" class="fas fa-eye"></i>
                </button>
            </div>
            
            <div class="password-container">
                <input type="password" name="cpass" required placeholder="confirm your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
                <button type="button" class="password-toggle" onclick="togglePassword('input[name=cpass]', '#cpassToggle')">
                    <i id="cpassToggle" class="fas fa-eye"></i>
                </button>
            </div>
            
            <input type="submit" value="register now" class="btn" name="submit">
        </form>
    </section>

</div>

<script>
   function togglePassword(inputId, toggleId) {
    const passwordInput = document.querySelector(inputId);
    const toggleButton = document.querySelector(toggleId);
    
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

<script src="../js/admin_script.js"></script>
   
</body>
</html>