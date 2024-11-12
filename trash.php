<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $sql = "UPDATE files SET is_trashed = TRUE, deleted_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'File moved to trash']);
    } else {
        echo json_encode(['message' => 'Error deleting file']);
    }

    $stmt->close();
}

$sql = "SELECT * FROM files WHERE is_trashed = TRUE";
$result = $conn->query($sql);

$files = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $files[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deleted Files</title>
</head>
<body>
    <h1>Deleted Files</h1>
    <ul>
        <?php foreach ($files as $file): ?>
            <li>
                Filename: <?php echo htmlspecialchars($file['filename']); ?><br>
                Filepath: <?php echo htmlspecialchars($file['filepath']); ?><br>
                Filesize: <?php echo htmlspecialchars($file['filesize_mb']); ?> MB<br>
                Deleted At: <?php echo htmlspecialchars($file['deleted_at']); ?><br>
                <!-- Optionally, add a restore button here -->
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

