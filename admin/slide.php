<?php
include '../components/connect.php';

session_start();

if(!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit();
}
$admin_id = $_SESSION['admin_id'];

class SliderManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function addSlide($title, $subtitle, $image) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO slides (title, subtitle, image) VALUES (:title, :subtitle, :image)");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':subtitle', $subtitle);
            $stmt->bindParam(':image', $image);
            $stmt->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function getSlides() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM slides ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
    
    public function deleteSlide($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM slides WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function updateSlide($id, $title, $subtitle, $image = '') {
        try {
            if($image != '') {
                $stmt = $this->conn->prepare("UPDATE slides SET title = :title, subtitle = :subtitle, image = :image WHERE id = :id");
                $stmt->bindParam(':image', $image);
            } else {
                $stmt = $this->conn->prepare("UPDATE slides SET title = :title, subtitle = :subtitle WHERE id = :id");
            }
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':subtitle', $subtitle);
            $stmt->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function getSlideById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM slides WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
}

$sliderManager = new SliderManager($conn);
$message = '';

if(isset($_POST['add_slide'])) {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/'.$image;
    
    if(move_uploaded_file($image_tmp_name, $image_folder)) {
        if($sliderManager->addSlide($title, $subtitle, $image)) {
            $message = 'New slide added successfully!';
        } else {
            $message = 'Failed to add slide to database!';
        }
    } else {
        $message = 'Failed to upload image!';
    }
}

if(isset($_POST['update_slide'])) {
    $id = $_POST['slide_id'];
    $title = $_POST['edit_title'];
    $subtitle = $_POST['edit_subtitle'];
    
    if(!empty($_FILES['edit_image']['name'])) {
        $image = $_FILES['edit_image']['name'];
        $image_tmp_name = $_FILES['edit_image']['tmp_name'];
        $image_folder = '../uploaded_img/'.$image;
        
        if(move_uploaded_file($image_tmp_name, $image_folder)) {
            if($sliderManager->updateSlide($id, $title, $subtitle, $image)) {
                $message = 'Slide updated successfully!';
            } else {
                $message = 'Failed to update slide in database!';
            }
        } else {
            $message = 'Failed to upload new image!';
        }
    } else {
        if($sliderManager->updateSlide($id, $title, $subtitle)) {
            $message = 'Slide updated successfully!';
        } else {
            $message = 'Failed to update slide in database!';
        }
    }
}

if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if($sliderManager->deleteSlide($id)) {
        header('location:slide.php');
        exit();
    }
}

$slides = $sliderManager->getSlides();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Slides</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        .flex-dashboard{
            display: flex;
        }
        
        .preview-container {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        
        .preview-image {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            background: #eee;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .preview-image img {
            max-width: 100%;
            max-height: 300px;
        }
        
        .preview-title {
            font-size: 24px;
            margin-top: 10px;
            font-weight: bold;
        }
        
        .preview-subtitle {
            font-size: 16px;
            color: #666;
        }
        
        .modal-lg {
            max-width: 900px;
        }
        
        .current-image {
            max-width: 100%;
            max-height: 200px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            padding: 3px;
        }
    </style>
</head>
<body>
<div class="flex-dashboard">
    <?php include '../components/admin_header.php'; ?>
    
    <div class="container mt-4">
        <h1>Manage Slider</h1>
        
        <?php if(!empty($message)): ?>
            <div class="alert alert-success"><?= $message; ?></div>
        <?php endif; ?>
        
        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addSlideModal">
            Add New Slide
        </button>
        
        <!-- Display Slides -->
        <div class="row">
            <?php if(count($slides) > 0): ?>
                <?php foreach($slides as $slide): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="../uploaded_img/<?= $slide['image']; ?>" class="card-img-top" alt="Slide Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= $slide['title']; ?></h5>
                                <p class="card-text"><?= $slide['subtitle']; ?></p>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-warning edit-btn" 
                                            data-id="<?= $slide['id']; ?>"
                                            data-title="<?= $slide['title']; ?>"
                                            data-subtitle="<?= $slide['subtitle']; ?>"
                                            data-image="<?= $slide['image']; ?>">
                                        Edit
                                    </button>
                                    <a href="slide.php?delete=<?= $slide['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this slide?');">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No slides added yet!</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="addSlideModal" tabindex="-1" aria-labelledby="addSlideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSlideModalLabel">Add New Slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Title (required)</label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="Enter slide title">
                        </div>
                        <div class="col-md-6">
                            <label for="subtitle" class="form-label">Subtitle (required)</label>
                            <input type="text" class="form-control" id="subtitle" name="subtitle" required placeholder="Enter slide subtitle">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image (required)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" required>
                    </div>
                    
                    <div class="preview-container">
                        <h4>Preview</h4>
                        <div class="preview-image">
                            <p id="noImageText">Image preview will appear here</p>
                            <img id="imagePreview" src="" style="display: none;">
                        </div>
                        <div class="preview-title" id="titlePreview">Title Preview</div>
                        <div class="preview-subtitle" id="subtitlePreview">Subtitle Preview</div>
                    </div>
                    
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_slide">Save Slide</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editSlideModal" tabindex="-1" aria-labelledby="editSlideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSlideModalLabel">Edit Slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="slide_id" id="edit_slide_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_title" class="form-label">Title (required)</label>
                            <input type="text" class="form-control" id="edit_title" name="edit_title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_subtitle" class="form-label">Subtitle (required)</label>
                            <input type="text" class="form-control" id="edit_subtitle" name="edit_subtitle" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="text-center">
                            <img id="current_image_preview" src="" class="current-image">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">New Image (optional)</label>
                        <input type="file" class="form-control" id="edit_image" name="edit_image" accept="image/jpg, image/jpeg, image/png, image/webp">
                    </div>
                    
                    <div class="preview-container">
                        <h4>Preview</h4>
                        <div class="preview-image">
                            <img id="edit_imagePreview" src="" style="display: block;">
                        </div>
                        <div class="preview-title" id="edit_titlePreview"></div>
                        <div class="preview-subtitle" id="edit_subtitlePreview"></div>
                    </div>
                    
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_slide">Update Slide</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const subtitleInput = document.getElementById('subtitle');
        const imageInput = document.getElementById('image');
        const titlePreview = document.getElementById('titlePreview');
        const subtitlePreview = document.getElementById('subtitlePreview');
        const imagePreview = document.getElementById('imagePreview');
        const noImageText = document.getElementById('noImageText');
        
        titleInput.addEventListener('input', function() {
            titlePreview.textContent = this.value || 'Title Preview';
        });
        
        subtitleInput.addEventListener('input', function() {
            subtitlePreview.textContent = this.value || 'Subtitle Preview';
        });
        
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    noImageText.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
                noImageText.style.display = 'block';
            }
        });
        
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editSlideModal'));
        const editTitleInput = document.getElementById('edit_title');
        const editSubtitleInput = document.getElementById('edit_subtitle');
        const editImageInput = document.getElementById('edit_image');
        const editTitlePreview = document.getElementById('edit_titlePreview');
        const editSubtitlePreview = document.getElementById('edit_subtitlePreview');
        const editImagePreview = document.getElementById('edit_imagePreview');
        const currentImagePreview = document.getElementById('current_image_preview');
        const slideIdInput = document.getElementById('edit_slide_id');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const subtitle = this.getAttribute('data-subtitle');
                const image = this.getAttribute('data-image');
                
                slideIdInput.value = id;
                editTitleInput.value = title;
                editSubtitleInput.value = subtitle;
                
                editTitlePreview.textContent = title;
                editSubtitlePreview.textContent = subtitle;
                
                currentImagePreview.src = '../uploaded_img/' + image;
                editImagePreview.src = '../uploaded_img/' + image;
                
                editModal.show();
            });
        });
        
        editTitleInput.addEventListener('input', function() {
            editTitlePreview.textContent = this.value || 'Title Preview';
        });
        
        editSubtitleInput.addEventListener('input', function() {
            editSubtitlePreview.textContent = this.value || 'Subtitle Preview';
        });
        
        editImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    editImagePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>

</body>
</html>