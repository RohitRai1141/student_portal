<?php
require "../db_connect.php";
require "../message_display.php";
require "verify_librarian.php";
require "header_librarian.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>LMS</title>
    <link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
    <link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
    <link rel="stylesheet" href="css/insert_book_style.css">
</head>
<body>
<form class="cd-form" method="POST" action="#" enctype="multipart/form-data">
    <center><legend>Add New Book Details</legend></center>
    <div class="error-message" id="error-message">
        <p id="error"></p>
    </div>
    <div class="icon">
        <input class="b-isbn" id="b_isbn" type="number" name="b_isbn" placeholder="ISBN" required />
    </div>
    <div class="icon">
        <input class="b-title" type="text" name="b_title" placeholder="Book Title" required />
    </div>
    <div class="icon">
        <input class="b-author" type="text" name="b_author" placeholder="Author Name" required />
    </div>
    <div>
        <h4>Category</h4>
        <p class="cd-select icon">
            <select class="b-category" name="b_category">
                <option>Computer</option>
                <option>Mechanical</option>
                <option>Civil</option>
                <option>Electrical</option>
            </select>
        </p>
    </div>
    <div class="icon">
        <input id="file-upload" type="file" name="file">
    </div>
    <br />
    <input class="b-isbn" type="submit" name="b_add" value="Add book" />
</form>

<?php
if(isset($_POST['b_add'])) {
    $isbn = mysqli_real_escape_string($con, $_POST['b_isbn']);
    $title = mysqli_real_escape_string($con, $_POST['b_title']);
    $author = mysqli_real_escape_string($con, $_POST['b_author']);
    $category = mysqli_real_escape_string($con, $_POST['b_category']);
    
    // Check if book with same ISBN already exists
    $sql = "SELECT * FROM book WHERE isbn = '$isbn'";
    $result = mysqli_query($con, $sql);
    $count = mysqli_num_rows($result);
    
    if($count == 1) {
        echo error_with_field("A book with that ISBN already exists", "b_isbn");
    } else {
        // Handle file upload
        $targetDir = "../../../Photos/Library/".$category."/";
        $fileName = "";
        
        // Check if file was uploaded
        if(isset($_FILES["file"]) && !empty($_FILES["file"]["name"])) {
            $fileName = basename($_FILES["file"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            // Check if upload directory exists, create if not
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $allowTypes = array('jpg','png','jpeg','gif','pdf','PNG');
            if(in_array($fileType, $allowTypes)) {
                if(!move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                    die(error_without_field("ERROR: Failed to upload file"));
                }
            } else {
                die(error_without_field("ERROR: Invalid file type. Only JPG, PNG, JPEG, GIF, PDF files are allowed."));
            }
        } else {
            // If no file uploaded, use default or empty
            $fileName = "default.jpg"; // or leave empty based on your requirement
        }
        
        // Insert book record
        $sql = "INSERT INTO book (author, isbn, category, title, photo) VALUES ('$author', '$isbn', '$category', '$title', '$fileName')";
        $result = mysqli_query($con, $sql);
        
        if(!$result) {
            die(error_without_field("ERROR: Couldn't add book - " . mysqli_error($con)));
        } else {
            echo success("New book record has been added");
        }
    }
}
?>
</body>
</html> 