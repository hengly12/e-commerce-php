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
            $messages[] = 'Product name already exists!';
        } else {
            $images = [];
            $max_size = 10 * 1024 * 1024; 
            $image_count = count($_FILES['product_images']['name']);
            
            for($i = 0; $i < min($image_count, 3); $i++) {
                if($_FILES['product_images']['error'][$i] == 0) {
                    if($_FILES['product_images']['size'][$i] > $max_size) {
                        throw new Exception("Image " . ($i+1) . " size is too large! Maximum size is 10MB");
                    }
                    
                    $image_name = $_FILES['product_images']['name'][$i];
                    $image_tmp = $_FILES['product_images']['tmp_name'][$i];
                    $image_folder = "../uploaded_img/$image_name";
                    
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                    if(!in_array($ext, $allowed)) {
                        throw new Exception("Invalid file type for image " . ($i+1));
                    }
                    
                    $image_key = "image_0" . ($i+1);
                    $images[$image_key] = $image_name;
                    move_uploaded_file($image_tmp, $image_folder);
                }
            }
            
            for($i = 1; $i <= 3; $i++) {
                $image_key = "image_0$i";
                if(!isset($images[$image_key])) {
                    $images[$image_key] = '';
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
                $images['image_01'], 
                $images['image_02'], 
                $images['image_03']
            ]);

            $messages[] = 'New product added successfully!';
        }
    } catch(Exception $e) {
        $messages[] = $e->getMessages();
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
        $messages[] = 'Error deleting product: ' . $e->getMessage();
    }
}

try {
    $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
    $select_products->execute();
    $products = $select_products->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $messages[] = 'Error fetching products: ' . $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_style.css">
    
    <style>
        body {
            font-size: 18px; 
        }
        
        .btn {
            font-size: 1.1rem; 
            padding: 1.3rem 1.2rem;
        }
        
        .form-control {
            font-size: 1.1rem; 
            padding: 0.6rem 0.8rem;
        }
        
        .form-label {
            font-size: 1.2rem; 
            font-weight: 500;
        }
        
        .modal-title {
            font-size: 1.6rem; 
        }
        
        .heading {
            font-size: 2.2rem; 
        }
        
        .box .name {
            font-size: 1.4rem; 
        }
        
        .box .price {
            font-size: 1.3rem; 
        }
        
        .box .details {
            font-size: 1.1rem; 
        }
        
        .image-preview {
            width: 180px; 
            height: 180px;
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
            font-size: 1.1rem;
        }
        .flex-dashboard{
            display: flex;
        }
        .scroll-content{
            overflow-y: auto;
            width: 100%;
            height: 100vh;
        }
        .add-product-btn {
            margin-bottom: 30px;
        }
        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .alert {
            font-size: 1.2rem;
        }

        .products-management {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .heading {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .btn-add {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            max-width: 145px;
        }

        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
        }

        .products-table th {
            background-color: #f9f9f9;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid #eee;
        }

        .products-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .product-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .actions-cell {
            display: flex;
            gap: 10px;
        }

        .btn-update {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-delete {
            background-color: #F44336;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .empty-messages {
            padding: 20px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>

<div class="flex-dashboard">
    <?php include '../components/admin_header.php'; ?>

    <div class="scroll-content">
        <?php
        if(isset($messages)){
            foreach($messages as $msg){
                echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
                    '.$msg.'
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
        ?>
    <section class="products-management">
        <div class="header-container">
            <h1 class="heading">Products Management</h1>
            <button type="button" class="btn btn-success btn-add" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus"></i> Add New Product
            </button>
        </div>
        
        <div class="table-container">
            <?php if(!empty($products)): ?>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>PRODUCT ID</th>
                            <th>IMAGE</th>
                            <th>NAME</th>
                            <th>PRICE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Sort products in descending order by ID
                        $productsDesc = $products;
                        usort($productsDesc, function($a, $b) {
                            return $b['id'] - $a['id']; // Sort by ID in descending order
                        });
                        
                        foreach($productsDesc as $product): 
                        ?>
                        <tr>
                            <td><?= $product['id']; ?></td>
                            <td>
                                <img src="../uploaded_img/<?= htmlspecialchars($product['image_01']); ?>" 
                                    alt="<?= htmlspecialchars($product['name']); ?>" 
                                    class="product-thumbnail">
                            </td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <td>$<?= htmlspecialchars($product['price']); ?></td>
                            <td class="actions-cell">
                                <a href="update_product.php?update=<?= $product['id']; ?>" 
                                class="btn btn-update">
                                    <i class="fas fa-edit"></i> Update
                                </a>
                                <a href="products.php?delete=<?= $product['id']; ?>"
                                class="btn btn-delete"
                                onclick="return confirm('Delete this product?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-messages">No products added yet!</p>
            <?php endif; ?>
        </div>
    </section>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data" id="addProductForm">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="productName" class="form-label">Product Name (required)</label>
                            <input type="text" class="form-control" id="productName" required maxlength="100" placeholder="Enter product name" name="name">
                        </div>
                        <div class="col-md-6">
                            <label for="productPrice" class="form-label">Product Price (required)</label>
                            <input type="number" min="0" class="form-control" id="productPrice" required max="9999999999" 
                                   placeholder="Enter product price" 
                                   onkeypress="if(this.value.length == 10) return false;" name="price">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="product_images" class="form-label">Product Images (required, select up to 3)</label>
                        <input type="file" name="product_images[]" 
                               accept="image/jpg, image/jpeg, image/png, image/webp" 
                               class="form-control" required id="product_images"
                               onchange="previewImages(this)" multiple>
                               
                        <div class="text-muted mt-1">You can select multiple images at once (max 3)</div>
                        
                        <div class="preview-container" id="imagesPreviewContainer">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="productDetails" class="form-label">Product Details (required)</label>
                        <textarea name="details" placeholder="Enter product details" 
                                  class="form-control" required maxlength="500" 
                                  id="productDetails" rows="5"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="addProductForm" class="btn btn-primary" name="add_product">Add Product</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
function previewImages(input) {
    const container = document.getElementById('imagesPreviewContainer');
    container.innerHTML = ''; 
    
    if (input.files) {
        const filesArray = Array.from(input.files).slice(0, 3);
        
        filesArray.forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'image-preview';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                
                previewDiv.appendChild(img);
                container.appendChild(previewDiv);
            }
            
            reader.readAsDataURL(file);
        });
        
        if (input.files.length > 3) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'alert alert-warning mt-2';
            warningDiv.textContent = 'Only the first 3 images will be uploaded.';
            container.appendChild(warningDiv);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($messages) && in_array('New product added successfully!', $messages)): ?>
    const addProductModal = document.getElementById('addProductModal');
    const modal = bootstrap.Modal.getInstance(addProductModal);
    if (modal) {
        modal.hide();
    }
    <?php endif; ?>
});
</script>

<script src="../js/admin_script.js"></script>

</body>
</html>