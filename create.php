<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gshare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start(); // Start session

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login page if user is not logged in
    exit();
}

$email = $_SESSION['email'];

// Retrieve user's ID based on email
$sql = "SELECT user_id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'], $_POST['role'])) {
        $password = $_POST['password'];
        $role = $_POST['role'];

        $hashed_password=password_hash($password,PASSWORD_DEFAULT);

        // Insert into collections table
        $sql = "INSERT INTO collections (password, role, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $hashed_password, $role, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('Inserted Successfully')</script>";
            header("location:index.php"); // Redirect to index.php after successful insertion
            exit();
        } else {
            echo "<script>alert('Error: " . $conn->error . "')</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Password and role not set')</script>";
    }
} else {
    echo "<script>alert('User not found')</script>";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create and Join Room</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
   h2{
    color: white;
   }
   nav{
    border-bottom: 5px solid red;
   }
   #submit{
    background-color: red;
    padding: 10px 20px 10px 20px;
    border-radius: 10px;
    font-weight: bold;
    color: white;
    font-size: 18px;
   }
    .form {
        
    background-color: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 8px;
    max-width: 400px;
    width: 100%;
    text-align: center; 
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-top: 250px;
    margin-left: 800px;
    color: black;
}
h2 ,p{
    color: black;
}
label {
    margin-top: 10px;
    display: block;
    margin-top: 30px;
}
a{
    color: white;
}
#select{
  background-color: black;
  color: white;
  padding: 10px;
}
    
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-dark">
  <a class="navbar-brand text-white" href="#">G-Share(Online File Sharing System)</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <!-- <li class="nav-item active">
        <a class="nav-link" href="#">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">About</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Services</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Contact</a>
      </li> -->
      <li class="nav-item">
        <a class="btn btn-primary" href="join.php">Join Room</a>
      </li>
    </ul>
  </div>
</nav>
    <div class="form">
    <form action="create.php" method="POST">
    <h2>Create Room</h2>
        <!-- <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br> -->

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <select name="role" id="select">
          <option value="create">Create</option>
          <!-- <option value="join">Join</option> -->
        </select>

        <input type="submit" name="login" value="Login">
        <!-- <p id="p">Don't have an account? <a href="signup.php">Sign Up</a></p> -->
</div>
      
    </form>
    </div>
    <!-- suggest me the idea i am creating the website online file sharing system where an admin user create a password after uploading the contain and when other user want to access the contain uploaded by the admin he/she should matched the password that is created by the admin suggest me to achieve it step by step and also write a code in php -->
</body>
</html>