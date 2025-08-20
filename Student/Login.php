<?php
session_start();
$con = mysqli_connect("localhost", "root", "rohitrai", "studentdb");

$Enrollment = $_POST['Enrollment'];
$password = $_POST['password'];
$error = "Username/Password Is Incorrect";

$Enrollment = stripcslashes($Enrollment);  
$password = stripcslashes($password);  
$Enrollment = mysqli_real_escape_string($con, $Enrollment);  
$password = mysqli_real_escape_string($con, $password);  

$sql = "SELECT * FROM student_ragistration WHERE enrollment='$Enrollment' AND password='$password'"; // <-- FIXED
$result = mysqli_query($con, $sql);  
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
$count = mysqli_num_rows($result);  

if ($count == 1) {  
    header('location: Student_Home.php');
    $_SESSION['id'] = $row['id'];
} else {
    $_SESSION["error"] = $error;
    header("location: Student_Login.php"); 
}
?>
