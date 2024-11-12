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

// Fetch all files from the database
$sql = "SELECT id, file_name, file_path,uploaded_date FROM files ORDER BY uploaded_date desc LIMIT 10";
$result = $conn->query($sql);

$files = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recently Uploaded</title>
    <!-- <link rel="stylesheet" href="access.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/css/lightgallery.min.css">
    <style>
          body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
            background-color: black;
        }
        
        #buttonid {
            align-self: start;
            margin-bottom: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: red;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        
        #gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            width: 100%;
            max-width: 1200px;
        }
        
        .gallery-item {
            position: relative;
            width: 150px;
            height: 150px;
            overflow: hidden;
            border: 2px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: start;
            text-align: center;
        }
        
        .gallery-item img,
        .gallery-item video,
        .gallery-item audio {
            max-width: 100%;
            max-height: 100%;
        }
        
        .gallery-item span {
            display: block;
            padding: 10px;
            font-size: 14px;
            color: #333;
        }
        
        .gallery-item a {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
<button type="button" id="buttonid">Go To Dashboard</button>
<div id="gallery">
    <?php foreach ($files as $file): ?>
        <div class="gallery-item" data-id="<?php echo $file['id']; ?>">
            <?php
            $file_path = htmlspecialchars($file['file_path']);
            $file_name = htmlspecialchars($file['file_name']);
            $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

            // Define the supported file types
            $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $video_extensions = ['mp4', 'webm', 'ogg','mov'];
            $audio_extensions = ['mp3', 'wav', 'ogg'];
            $document_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt','c','php','py','html','css'];

            if (in_array($file_extension, $image_extensions)): ?>
                <a href="<?php echo $file_path; ?>" data-lg-size="1920-1080" data-sub-html="<h4><?php echo $file_name; ?></h4>">
                    <img src="<?php echo $file_path; ?>" alt="<?php echo $file_name; ?>">
                </a>
            <?php elseif (in_array($file_extension, $video_extensions)): ?>
                <a href="<?php echo $file_path; ?>" data-lg-size="1920-1080" data-poster="<?php echo $file_path; ?>" data-sub-html="<h4><?php echo $file_name; ?></h4>">
                    <video src="<?php echo $file_path; ?>" controls></video>
                </a>
            <?php elseif (in_array($file_extension, $audio_extensions)): ?>
                <a href="<?php echo $file_path; ?>" data-lg-size="1920-1080" data-sub-html="<h4><?php echo $file_name; ?></h4>">
                    <audio src="<?php echo $file_path; ?>" controls></audio>
                </a>
            <?php elseif (in_array($file_extension, $document_extensions)): ?>
                <a href="<?php echo $file_path; ?>" data-lg-size="1920-1080" data-sub-html="<h4><?php echo $file_name; ?></h4>">
                    <img src="path/to/document-icon.png" alt=""<?php echo $file_name; ?>">
                    <span><?php echo $file_name; ?></span>
                </a>
            <?php else: ?>
                <span>Unsupported file type: <?php echo $file_name; ?></span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/lightgallery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/zoom/lg-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/video/lg-video.min.js"></script>

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
        fullScreen: false,
        videojs: true // Enable video.js support
    });

    // Handle right-click to delete file
    $('.gallery-item').contextmenu(function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this file?')) {
            $.ajax({
                url: 'delete_file.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response == 'success') {
                        location.reload();
                    } else {
                        alert('Failed to delete file.');
                    }
                }
            });
        }
    });
});

document.querySelector("#buttonid").addEventListener("click",()=>{
    window.location.href='index.php';
})
</script>

</body>
</html>
