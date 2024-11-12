<?php
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'gshare');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}


// Function to safely escape input values
function cleanInput($data) {
    global $mysqli;
    return mysqli_real_escape_string($mysqli, trim($data));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    // Clean and validate inputs
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email']);
    $password = cleanInput($_POST['password']);

    // Check if email already exists in database
    $query = "SELECT * FROM users WHERE email = ?";
    $statement = $mysqli->prepare($query);
    $statement->bind_param('s', $email);
    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows > 0) {
        // Email already exists, handle error (e.g., show message or redirect)
        echo "Error: Email already exists. Please choose a different email.";
    } else {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into database
        $insertQuery = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $insertStatement = $mysqli->prepare($insertQuery);
        $insertStatement->bind_param('sss', $username, $email, $hashed_password);

        if ($insertStatement->execute()) {
           
            $_SESSION['email'] = $email;
            header("Location: create.php");
            exit();
        } else {
            echo "Error: " . $mysqli->error;
        }

        // Close insert statement
        $insertStatement->close();
    }

    // Close select statement and result set
    $statement->close();
}

// Close database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <h2>Sign Up</h2>
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" name="signup" value="Sign Up">
       
    </form>

</body>
</html>
