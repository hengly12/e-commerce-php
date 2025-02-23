<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['send'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg'];
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);

   $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $select_message->execute([$name, $email, $number, $msg]);

   if($select_message->rowCount() > 0){
      $message[] = 'already sent message!';
   }else{

      $insert_message = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$user_id, $name, $email, $number, $msg]);

      $message[] = 'sent message successfully!';

   }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">
   <style>
      .contact {
    min-height: 80vh;
    background: #f7f7f7;
    padding: 2rem;
   }

   .contact form {
      max-width: 60rem;
      margin: 0 auto;
      background: #fff;
      padding: 3rem;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
   }

   .contact form h3 {
      text-align: center;
      font-size: 2.5rem;
      color: #333;
      margin-bottom: 2rem;
      text-transform: capitalize;
      position: relative;
   }

   .contact form h3::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 50px;
      height: 3px;
      background: #3498db;
   }

   .contact form .box {
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

   .contact form .box:focus {
      border-color: #3498db;
      background: #fff;
      box-shadow: 0 0 10px rgba(52, 152, 219, 0.1);
   }

   .contact form textarea.box {
      height: 15rem;
      resize: none;
   }

   .contact form .btn {
      display: block;
      width: 100%;
      margin-top: 2rem;
      padding: 1.2rem;
      background: #3498db;
      color: #fff;
      font-size: 1.2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
   }

   .contact form .btn:hover {
      background: #2980b9;
      transform: translateY(-2px);
   }

   .contact form input[type="number"]::-webkit-inner-spin-button,
   .contact form input[type="number"]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
   }

   .contact form .box::placeholder {
      color: #999;
      font-size: 1rem;
   }

   .message {
      position: sticky;
      top: 0;
      margin: 0 auto;
      width: 100%;
      background: #fff;
      padding: 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      max-width: 1200px;
   }

   .message.success {
      background: #d4edda;
      color: #155724;
   }

   .message.error {
      background: #f8d7da;
      color: #721c24;
   }

   .fl-con{
      display: flex;
      gap: 2rem;
   }

   .map {
      width: 100%;
      height: 76vh;
      margin-top: 24px;
      margin-right: 22px;
      box-shadow: 0px 2px 5px 0px rgba(0, 0, 0, 0.1);
      border-radius: 15px;
   }

   iframe{
      border-radius: 15px;
   }

   @media (max-width: 768px) {
      .contact form {
         padding: 2rem;
         margin: 1rem;
      }
      
      .contact form h3 {
         font-size: 2rem;
      }
      
      .contact form .box {
         padding: 1rem;
      }

      .fl-con{
         flex-direction: column;
      }
      .map{
         margin-top: 0;
         margin-right: 0;
      }
   }
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="fl-con">
<section class="contact">

<form action="" method="post">
   <h3>Contact</h3>
   <input type="text" name="name" placeholder="enter your name" required maxlength="20" class="box">
   <input type="email" name="email" placeholder="enter your email" required maxlength="50" class="box">
   <input type="number" name="number" min="0" max="9999999999" placeholder="enter your number" required onkeypress="if(this.value.length == 10) return false;" class="box">
   <textarea name="msg" class="box" placeholder="enter your message" cols="30" rows="10"></textarea>
   <input type="submit" value="send message" name="send" class="btn">
</form>

</section>

   <div class="map">
   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.7652231711254!2d104.88816677452691!3d11.568681244082185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3109519fe4077d69%3A0x20138e822e434660!2sRoyal%20University%20of%20Phnom%20Penh!5e0!3m2!1sen!2skh!4v1740283436338!5m2!1sen!2skh" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
   </div>
</div>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>