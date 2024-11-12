<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gshare";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection lost: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['password']/*, $_POST['role']*/)) {
        $password = $_POST['password'];
        // $role = $_POST['role'];

        $sql = "SELECT password from collections where password= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $password);

  
        if ($stmt->execute()) {
            echo "<script>alert('password matched: welcome');</script> ";
            header('location:index.php');
            exit();
        } else {
            echo "<script>alert('Error: " . $conn->error . "')</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('password doesnot matched')</script>";
    }
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
    <form action="join.php" method="POST">
    <h2>Join Room</h2>
        <!-- <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br> -->

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <select name="role" id="select">
          <option value="create">Join</option>
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