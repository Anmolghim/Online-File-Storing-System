<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gshare";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle image deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['file_path'])) {
    $filePath = $_POST['file_path'];
    $response = [];

    // Delete file from the folder
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            // Now delete the record from the database
            $stmt = $conn->prepare("DELETE FROM files WHERE file_path = ?");
            $stmt->bind_param("s", $filePath);

            if ($stmt->execute()) {
                $response['status'] = 'success';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to delete record from database.';
            }

            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to delete file from folder.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'File does not exist.';
    }

    echo json_encode($response);
    $conn->close();
    exit();
}

// Fetch only photos (images) from the database
$sql = "SELECT file_name, file_path FROM files WHERE file_type = 'photo'";
$result = $conn->query($sql);

$images = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Photos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/css/lightgallery.min.css">
    <link rel="stylesheet" href="access.css">
</head>
<body>
<button type="button" id="buttonid">Go To Dashboard</button>
<div id="gallery">
    <?php foreach ($images as $image): ?>
        <div class="gallery-item">
            <a href="<?php echo htmlspecialchars($image['file_path']); ?>" data-lg-size="1920-1080">
                <img src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="<?php echo htmlspecialchars($image['file_name']); ?>"
                     oncontextmenu="return confirmDelete('<?php echo $image['file_name']; ?>', '<?php echo $image['file_path']; ?>');">
            </a>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/lightgallery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/zoom/lg-zoom.min.js"></script>

<script>
$(document).ready(function() {
    $('#gallery').lightGallery({
        selector: '.gallery-item a',
        mode: 'lg-fade',
        download: false, 
        counter: true, 
        zoom: true, 
        enableSwipe: true,
        autoplay: true, 
        fullScreen: true ,
    });
});

function confirmDelete(fileName, filePath) {
    if (confirm("Are you sure you want to delete '" + fileName + "'?")) {
        // User confirmed deletion
        deletePhoto(filePath);
    }
    return false; // Prevent the browser context menu from showing up
}

function deletePhoto(filePath) {
    $.ajax({
        type: 'POST',
        url: 'accessphoto.php', // Current PHP file itself
        data: { action: 'delete', file_path: filePath },
        success: function(response) {
            let jsonResponse = JSON.parse(response);
            if (jsonResponse.status === 'success') {
                // Remove the deleted photo from the DOM
                $('img[src="' + filePath + '"]').closest('.gallery-item').remove();
                alert('Photo deleted successfully.');
            } else {
                alert('Failed to delete photo: ' + jsonResponse.message);
            }
        },
        error: function() {
            alert('Error while deleting photo. Please try again.');
        }
    });
}

document.querySelector("#buttonid").addEventListener("click",()=>{
    window.location.href="index.php";
});
</script>

</body>
</html>
