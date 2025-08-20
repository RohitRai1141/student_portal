<?php
$con=mysqli_connect("localhost","root","rohitrai","studentdb");
session_start();

// Handle file upload
if(isset($_POST['submit'])) {
    $target_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $original_filename = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $original_filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if file is selected
    if ($_FILES["file"]["size"] == 0) {
        $_SESSION['statusMsg'] = '<div class="alert alert-danger">Please select a file to upload.</div>';
        $uploadOk = 0;
    }
    
    // Check file size (limit to 5MB)
    if ($_FILES["file"]["size"] > 5000000) {
        $_SESSION['statusMsg'] = '<div class="alert alert-danger">Sorry, your file is too large. Maximum file size is 5MB.</div>';
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx") {
        $_SESSION['statusMsg'] = '<div class="alert alert-danger">Sorry, only JPG, JPEG, PNG, GIF, PDF, DOC & DOCX files are allowed.</div>';
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        // Error message already set above
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $description = mysqli_real_escape_string($con, $_POST['descc']);
            $link = mysqli_real_escape_string($con, $_POST['link']);
            $current_date = date('d-m-Y'); // Format: DD-MM-YYYY to match your existing data
            
            // Insert into scheduling table (adjust table name as needed)
            $sql = "INSERT INTO scheduling (file_name, uploaded_on, descc, link) VALUES ('$original_filename', '$current_date', '$description', '$link')";
            
            if(mysqli_query($con, $sql)) {
                $_SESSION['statusMsg'] = '<div class="alert alert-success"><strong>Success!</strong> Schedule uploaded successfully.</div>';
            } else {
                $_SESSION['statusMsg'] = '<div class="alert alert-danger">Database error: ' . mysqli_error($con) . '</div>';
            }
        } else {
            $_SESSION['statusMsg'] = '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
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
<link rel="icon" href="../Photos/Logo.png" type="image/icon type">
<title>Admin Scheduling</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Raleway:400,400i,500,500i,600,600i,700,700i,800,800i" rel="stylesheet">
<!-- Stylesheets -->
<link rel="stylesheet" href="../css/bootstrap.min.css"/>
<link rel="stylesheet" href="../css/font-awesome.min.css"/>
<link rel="stylesheet" href="../css/owl.carousel.css"/>
<link rel="stylesheet" href="../css/Student_Categories.css">
<link rel="stylesheet" href="../css/Faculty_Upload.css">

<style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }
    .alert-danger {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }
    .alert-warning {
        color: #8a6d3b;
        background-color: #fcf8e3;
        border-color: #faebcc;
    }
</style>
</head>
<body>
<div id="preloder">
<div class="loader"></div>
</div>
<!-- Header section -->
<header class="header-section">
<div class="container">
<div class="row">
<div class="col-lg-3 col-md-3" style="left:-325px;top:-10px">
<a href="../index.php">
<img src="../Photos/Logo.png" width="105px" height="100px">
</a>
<div class="site-logo"></div>
<div class="nav-switch">
<i class="fa fa-bars"></i>
</div>
</div>
<div class="col-lg-9 col-md-9">
<nav class="main-menu">
<ul style="float:left;margin-left:-50px;">
<li><a href="../About.html">About us</a></li>
<li><a href="../Contact.html">Contact</a></li>
<li><a href="./Librarian/librarian/home.php">Library</a></li>
<li><a href="Admin_News.php">News</a></li>
<li><a href="Admin_Scheduling.php">Scheduling</a></li>
<li><a href="Admin_Home.php">Home</a></li>
</ul>
</nav>
</div>
</div>
</div>
<div class="header-section-line"></div>
</header><br><br><br><br><br><br>
<!-- Header section end -->

<!-- Upload -->
<h2>Scheduling Upload</h2><br><br>

<!-- Display status message -->
<?php 
if(isset($_SESSION["statusMsg"])){
    echo $_SESSION['statusMsg'];
}
?>

<form id="file-upload-form" class="uploader" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<input id="file-upload" type="file" name="file" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">

<label for="file-upload" id="file-drag">
<img id="file-image" src="#" alt="Preview" class="hidden">
<div id="start">
<i class="fa fa-download" aria-hidden="true"></i>
<div>Select a file or drag here</div>
<div id="notimage" class="hidden">Please select an image</div>
<span id="file-upload-btn" class="btn btn-primary">Select a file</span> <br>
</div>
<div id="response" class="hidden">
<div id="messages"></div>
</div><br><br>
</label>

<label for="descc">Description:</label>
<textarea name="descc" id="descc" rows=4 cols=50 placeholder="Add Description" required></textarea>

<label for="link">Link:</label>
<textarea name="link" id="link" rows=4 cols=50 placeholder="Attach a Link (optional)"></textarea>

<br><br>
<input type="submit" name="submit" value="Upload" class="btn btn-primary">
</form>

<script>
// Auto-hide success message after 5 seconds
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.display = 'none';
    });
}, 5000);

// Simple file upload handler to prevent JavaScript errors
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-upload');
    const fileImage = document.getElementById('file-image');
    const startDiv = document.getElementById('start');
    const notImageDiv = document.getElementById('notimage');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Hide "not image" message
                if (notImageDiv) {
                    notImageDiv.style.display = 'none';
                    notImageDiv.classList.add('hidden');
                }
                
                // Show file name
                if (startDiv) {
                    const fileName = startDiv.querySelector('div');
                    if (fileName) {
                        fileName.textContent = 'Selected: ' + file.name;
                    }
                }
                
                // If it's an image, show preview
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (fileImage) {
                            fileImage.src = e.target.result;
                            fileImage.classList.remove('hidden');
                            fileImage.style.display = 'block';
                            fileImage.style.maxWidth = '200px';
                            fileImage.style.maxHeight = '200px';
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Hide image preview for non-image files
                    if (fileImage) {
                        fileImage.classList.add('hidden');
                        fileImage.style.display = 'none';
                    }
                }
            }
        });
    }
});
</script>

</body>
</html>
<?php 
unset($_SESSION["statusMsg"]);
?>
<!--====== Javascripts & Jquery ======-->
<script src="../js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/mixitup.min.js"></script>
<script src="../js/circle-progress.min.js"></script>
<script src="../js/owl.carousel.min.js"></script>
<script src="../js/main.js"></script>
<script src="../js/Faculty_Upload.js"></script>