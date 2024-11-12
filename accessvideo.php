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

// Handle video deletion
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

// Fetch only videos from the database
$sql = "SELECT file_name, file_path FROM files WHERE file_type = 'video'";
$result = $conn->query($sql);

$videos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Videos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/css/lightgallery.min.css">
    <link rel="stylesheet" href="access.css">
</head>
<body>
<button type="button" id="buttonid">Go To Dashboard</button>
<div id="gallery">
    <?php foreach ($videos as $video): ?>
        <div class="gallery-item">
            <a href="<?php echo htmlspecialchars($video['file_path']); ?>" data-lg-size="1920-1080">
                <video width="320" height="240" controls oncontextmenu="return confirmDelete('<?php echo htmlspecialchars($video['file_name']); ?>', '<?php echo htmlspecialchars($video['file_path']); ?>');">
                    <source src="<?php echo htmlspecialchars($video['file_path']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
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
        fullScreen: true,
    });
});

function confirmDelete(fileName, filePath) {
    if (confirm("Are you sure you want to delete '" + fileName + "'?")) {
        // User confirmed deletion
        deleteVideo(filePath);
    }
    return false; // Prevent the browser context menu from showing up
}

function deleteVideo(filePath) {
    $.ajax({
        type: 'POST',
        url: 'accessphoto.php', // Current PHP file itself
        data: { action: 'delete', file_path: filePath },
        success: function(response) {
            let jsonResponse = JSON.parse(response);
            if (jsonResponse.status === 'success') {
                // Remove the deleted video from the DOM
                $('video source[src="' + filePath + '"]').closest('.gallery-item').remove();
                alert('Video deleted successfully.');
            } else {
                alert('Failed to delete video: ' + jsonResponse.message);
            }
        },
        error: function() {
            alert('Error while deleting video. Please try again.');
        }
    });
}

document.querySelector("#buttonid").addEventListener("click",()=>{
    window.location.href="index.php";
});
</script>

</body>
</html>
