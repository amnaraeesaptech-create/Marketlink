<?php
include "connection.php";

if (isset($_POST['register'])) {

    $full_name = $_POST['full_name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $farm_location = $_POST['farm_location'];

    $sql = "INSERT INTO farmer_tbl 
            (full_name, mobile, email, farm_location)
            VALUES 
            ('$full_name', '$mobile', '$email', '$farm_location')";

    if (mysqli_query($conn, $sql)) {
        echo "Farmer registered successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Registration</title>
</head>

<body>

<h2>Farmer Registration Form</h2>

<form method="POST">

    <label>Full Name:</label>
    <input type="text" name="full_name" required>
    <br><br>

    <label>Mobile Number:</label>
    <input type="text" name="mobile" required>
    <br><br>

    <label>Email:</label>
    <input type="email" name="email" required>
    <br><br>

    <label>Farm Location:</label>
    <input type="text" name="farm_location" required>
    <br><br>

    <button type="submit" name="register">Register Farmer</button>

</form>

</body>
</html>