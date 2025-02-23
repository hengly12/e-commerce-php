<?php
include '../components/connect.php';

session_start();

if(!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit();
}
$admin_id = $_SESSION['admin_id'];

if(isset($_POST['add_product'])) {
    try {
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
        $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);

        $check_product = $conn->prepare("SELECT COUNT(*) FROM `products` WHERE name = ?");
        $check_product->execute([$name]);
        
        if($check_product->fetchColumn() > 0) {
            $message[] = 'Product name already exists!';
        } else {
            $images = [];
            $max_size = 10 * 1024 * 1024; 

            for($i = 1; $i <= 3; $i++) {
                $image_key = "image_0$i";
                if(isset($_FILES[$image_key]) && $_FILES[$image_key]['error'] == 0) {
                    if($_FILES[$image_key]['size'] > $max_size) {
                        throw new Exception("Image $i size is too large! Maximum size is 10MB");
                    }
                    
                    $image_name = $_FILES[$image_key]['name'];
                    $image_tmp = $_FILES[$image_key]['tmp_name'];
                    $image_folder = "../uploaded_img/$image_name";
                    
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                    if(!in_array($ext, $allowed)) {
                        throw new Exception("Invalid file type for image $i");
                    }
                    
                    $images[$image_key] = $image_name;
                    move_uploaded_file($image_tmp, $image_folder);
                }
            }

            $insert_product = $conn->prepare("
                INSERT INTO `products` (name, details, price, image_01, image_02, image_03) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insert_product->execute([
                $name, 
                $details, 
                $price, 
                $images['image_01'] ?? '', 
                $images['image_02'] ?? '', 
                $images['image_03'] ?? ''
            ]);

            $message[] = 'New product added successfully!';
        }
    } catch(Exception $e) {
        $message[] = $e->getMessage();
    }
}

if(isset($_GET['delete'])) {
    try {
        $delete_id = $_GET['delete'];
        
        $get_images = $conn->prepare("SELECT image_01, image_02, image_03 FROM `products` WHERE id = ?");
        $get_images->execute([$delete_id]);
        $images = $get_images->fetch(PDO::FETCH_ASSOC);
        
        foreach($images as $image) {
            if($image && file_exists("../uploaded_img/$image")) {
                unlink("../uploaded_img/$image");
            }
        }
        
        $conn->beginTransaction();
        
        $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
        $delete_product->execute([$delete_id]);
        
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
        $delete_cart->execute([$delete_id]);
        
        $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
        $delete_wishlist->execute([$delete_id]);
        
        $conn->commit();
        header('location:products.php');
        exit();
    } catch(Exception $e) {
        $conn->rollBack();
        $message[] = 'Error deleting product: ' . $e->getMessage();
    }
}

try {
    $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
    $select_products->execute();
    $products = $select_products->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $message[] = 'Error fetching products: ' . $e->getMessage();
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    
    <style>
        .image-preview {
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .preview-text {
            color: #999;
        }
        .flex-dashboard{
         display: flex;
        }
        .scroll-content{
         overflow-y: auto;
         width: 100%;
         height: 100vh;
        }
    </style>
</head>
<body>

<div class="flex-dashboard">
   <?php include '../components/admin_header.php'; ?>

   <div class="scroll-content">
   <section class="add-products flex-col">
    <h1 class="heading">Add New Product</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="flex">
            <div class="inputBox">
                <span>Product Name (required)</span>
                <input type="text" class="box" required maxlength="100" placeholder="Enter product name" name="name">
            </div>
            <div class="inputBox">
                <span>Product Price (required)</span>
                <input type="number" min="0" class="box" required max="9999999999" 
                       placeholder="Enter product price" 
                       onkeypress="if(this.value.length == 10) return false;" name="price">
            </div>
            <?php for($i = 1; $i <= 3; $i++): ?>
            <div class="inputBox">
                <span>Image <?= $i ?> (required)</span>
                <input type="file" name="image_0<?= $i ?>" 
                       accept="image/jpg, image/jpeg, image/png, image/webp" 
                       class="box" required 
                       onchange="previewImage(this, <?= $i ?>)">
                <div class="image-preview" id="imagePreview_<?= $i ?>">
                    <span class="preview-text">Image preview</span>
                </div>
            </div>
            <?php endfor; ?>
            <div class="inputBox">
                <span>Product Details (required)</span>
                <textarea name="details" placeholder="Enter product details" 
                          class="box" required maxlength="500" 
                          cols="30" rows="10"></textarea>
            </div>
        </div>
        <input type="submit" value="Add Product" class="btn" name="add_product">
    </form>
</section>

<section class="show-products flex-col">
    <h1 class="heading">Products Added</h1>

    <div class="box-container">
        <?php if(!empty($products)): ?>
            <?php foreach($products as $product): ?>
            <div class="box">
                <img src="../uploaded_img/<?= htmlspecialchars($product['image_01']); ?>" alt="">
                <div class="name"><?= htmlspecialchars($product['name']); ?></div>
                <div class="price">$<?= htmlspecialchars($product['price']); ?></div>
                <div class="details"><?= htmlspecialchars($product['details']); ?></div>
                <div class="flex-btn">
                    <a href="update_product.php?update=<?= $product['id']; ?>" class="option-btn">Update</a>
                    <a href="products.php?delete=<?= $product['id']; ?>" 
                       class="delete-btn" 
                       onclick="return confirm('Delete this product?');">Delete</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty">No products added yet!</p>
        <?php endif; ?>
    </div>
</section>
   </div>
</div>


<script>
function previewImage(input, imageNumber) {
    const preview = document.getElementById(`imagePreview_${imageNumber}`);
    const previewText = preview.querySelector('.preview-text');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (previewText) {
                previewText.style.display = 'none';
            }
            
            let img = preview.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                preview.appendChild(img);
            }
            
            img.src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script src="../js/admin_script.js"></script>

</body>
</html>