<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "";
$dbname = "gshare";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection Error: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$user_id = $user['user_id'];
$name = $user['username'];

$total_storage = 500;


$used_by_images = "SELECT sum(file_size) as size FROM files WHERE file_type like '%photo%' ";
$result_image=mysqli_query($conn,$used_by_images);
if($result_image){
  $photo=mysqli_fetch_assoc($result_image);
  $photo_size= (int)$photo['size'];
  // echo "$photo_size";
}
$used_by_videos =  "SELECT sum(file_size) as size FROM files WHERE file_type like '%video%' ";
$result_video=mysqli_query($conn,$used_by_videos);
if($result_video){
  $video=mysqli_fetch_assoc($result_video);
  $video_size=(int)$video['size'];
  // echo "$video_size";
}

$used_by_music =  "SELECT sum(file_size) as size FROM files WHERE file_type like '%audio%' ";
$result_audio=mysqli_query($conn,$used_by_music);
if($result_audio){
  $audio=mysqli_fetch_assoc($result_audio);
  $audio_size=(int)$audio['size'];
  // echo "$audio_size";
}

$used_by_doc =  "SELECT sum(file_size) as size FROM files WHERE file_type like '%document%' ";
$result_doc=mysqli_query($conn,$used_by_doc);
if($result_doc){
  $document=mysqli_fetch_assoc($result_doc);
  $document_size=(int)$document['size'];
  // echo "$document_size";
}

$used_total="SELECT SUM(file_size) as sum FROM files";
$result=mysqli_query($conn,$used_total);
if($result){
  $total=mysqli_fetch_assoc($result);
  $total_size=(int)$total['sum'];
  // echo "$total_size";
}

// $used = $used_by_images + $used_by_videos + $used_by_music + $used_by_doc;
$Collections = 1;

$sql = "SELECT * FROM collections WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$collections = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $collections[] = $row;
  }
}
$stmt->close();

$sql = "SELECT file_name, file_size, is_public, uploaded_date, collection_id FROM files WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$files = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $files[] = $row;
  }
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
  session_destroy();
  header("Location: login.php");
  exit();
}

// Generate a unique token for the form
if (!isset($_SESSION['upload_token'])) {
  $_SESSION['upload_token'] = bin2hex(random_bytes(32));
}
$upload_token = $_SESSION['upload_token'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
  if (!isset($_POST['upload_token']) || $_POST['upload_token'] !== $_SESSION['upload_token'] || $_POST['upload_token'] !== $_SESSION['upload_token']) {
    echo "<script>alert('Invalid form submission.');</script>";
    exit();
  }

  // Reset the token 
  unset($_SESSION['upload_token']);

  $allowTypes = array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'mp3', 'wav', 'pdf', 'doc', 'docx', 'css', 'php', 'java', 'py', 'html', 'sql');

  foreach ($_FILES['file']['name'] as $key => $filename) {
    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $filename = basename($filename);
    $file_size = $_FILES['file']['size'][$key] / (1024 * 1024); // Convert to MB

    // if ($file_size > 5) {
    //   echo "File size exceeds 5 MB";
    //   exit();
    // }
    
    $collection_id = !empty($_POST['collection_id']) ? $_POST['collection_id'] : NULL;
    $isPublic = isset($_POST['is_public']) ? 1 : 0;
    $user_id = $user['user_id'];

    
    if (in_array($filetype, $allowTypes)) {
      // Determine the target directory and file type category
      if (in_array($filetype, array('jpg', 'jpeg', 'png', 'gif'))) {
        $fileTypeCategory = 'photo';
        $targetDir = "uploads/photos/";
      } elseif (in_array($filetype, array('mp4', 'avi', 'mov'))) {
        $fileTypeCategory = 'video';
        $targetDir = "uploads/videos/";
      } elseif (in_array($filetype, array('mp3', 'wav'))) {
        $fileTypeCategory = 'audio';
        $targetDir = "uploads/audios/";
      } elseif (in_array($filetype, array('pdf', 'doc', 'docx', 'css', 'php', 'java', 'py', 'html', 'sql'))) {
        $fileTypeCategory = 'document';
        $targetDir = "uploads/documents/";
      } else {
        continue;
      }

      // Create the target directory if needed
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
      }
      $targetFilePath = $targetDir . $filename;

      // Check if file already exists
      if (file_exists($targetFilePath)) {
        echo "<script>alert( 'Sorry, file already exists.');</script>";
        continue;
      }
      // Upload file to server
      if (move_uploaded_file($_FILES["file"]["tmp_name"][$key], $targetFilePath)) {
        // Use prepared statements to insert file info into the database
        $stmt = $conn->prepare("INSERT INTO files (file_name, file_size, is_public, user_id, collection_id, file_path, file_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdiiiss", $filename, $file_size, $isPublic, $user_id, $collection_id, $targetFilePath, $fileTypeCategory);

        if ($stmt->execute()) {
          echo "<script>alert( 'File  has been successfully inserted.');</script>";
        } else {
          echo "<script>alert (' Sorry, file  failed to upload.');</script>";
        }
        $stmt->close();
      } else {
        echo "<script>alert( 'Sorry, error uploading your file.');</script>";
      }
    } else {
      echo "<script>alert( 'Sorry, only JPG, JPEG, PNG, GIF, MP4, AVI, MOV, MP3, WAV, PDF, DOC, DOCX files are allowed to upload.');</script>";
    }
  }
}

// just added

// $showCreateButton = false;
// foreach ($collections as $collection) {
//   if ($collection['role'] == 'create') {
//     $showCreateButton = true;
//     break; // Stop checking once we find one "create" role
//   }
// }


// $conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>G-Share</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="//cdn.datatables.net/2.0.6/css/dataTables.dataTables.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    .total-storage {
      height: 100px;
      width: 100px;
      background-color: var(--nav-bg);
    }
  </style>
</head>

<body>
  <div id="total-Storage" data-value="<?php echo $total_storage; ?> "></div>
  <!-- profile -->
  <div class="profile">
    <p> <?php echo $name; ?></p>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display: inline;">
      <input class="button-2" type="submit" name="logout" value='Logout'>
    </form>

  </div>
  <!-- end profile -->
  <!-- upload -->
  <div class="upload-container">
    <div class="upload-header">
      <h2>Upload a File</h2>

      <span class="material-icons-outlined" onclick="createBtn()">close</span>
    </div>
    <br>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
      <label for="fileUpload" class="custom-file-upload">
        Choose File
      </label>
      <input type="file" name="file[]" id="fileUpload" accept="image/*,application/pdf,video/*,audio/*">

      <div class="collections">
        <label for="collection_id">Select Collection:</label>
        <select name="collection_id" id="collection_id">
          <option value="">None (Private)</option>
          <?php foreach ($collections as $collection) : ?>
            <option value="<?php echo $collection['id']; ?>"><?php echo $collection['role']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="public-option">
        <input type="checkbox" name="is_public" id="is_public">
        <label for="is_public">Make file public</label>
      </div>

      <input type="hidden" name="upload_token" value="<?php echo $upload_token; ?>">
      <div id="preview"></div>
      <input type="submit" value="Upload" name="submit">

    </form>
  </div>
  <!-- end Upload -->
  <!-- display storage -->

  <!-- should be modify -->
  <!-- <div class="total-storage">
  <span class="material-icons-outlined" onclick="storage()" >close</span>
  </div> -->

  <nav>
    <a href="index.php">
      <div class="logo">
        <img src="images/logo.png" alt="">
        <h2> G-Share</h2>
      </div>
    </a>




    <div class="nav-right">

      <div class="nav-mid">
        <span class="material-icons-outlined">
          search
        </span>
        <input type="text" id="search" placeholder="Search">
      </div>



      <!-- <span class="material-icons-outlined">
info
</span> -->
      <span class="material-icons-outlined">
        notifications
      </span>
      <!-- <span class="material-icons-outlined">
settings
</span> -->
      <span class="material-icons-outlined profile-icon" onclick="profileIcon()">
        account_circle
      </span>
    </div>

  </nav>

  <!-- container -->
  <div class="container1">
    <!-- Sidebar -->
    <div class="sidebar">

      <div class="overview">
        <h3 class="title-text">Overview</h3>
        <button type="button" class="button-1">Overview Storage</button>

      </div>

      <div class="file-manager">
        <h3 class="title-text">File Manager</h3>
        <h4 id="h4-storage" onclick="storage()">
          <span class="material-icons-outlined">
            chevron_right
          </span>
          <span class="material-icons-outlined">
            cloud
          </span>My Storage
        </h4>
        <h4 id="recent"> <span class="material-icons-outlined">
            chevron_right
          </span> <span class="material-icons-outlined">
            query_builder
          </span> Recent</h4>
        <!-- <h4> <span class="material-icons-outlined">
chevron_right
</span> <span class="material-icons-outlined">
bookmark
</span>Favourites</h4> -->
        <h4> <span class="material-icons-outlined">
            chevron_right
          </span> <span class="material-icons-outlined">
            delete
          </span>Trash</h4>
      </div>



      <div class="shared-file">

        <h3 class="title-text">Shared files</h3>
        <h4> <span class="material-icons-outlined">
            chevron_right
          </span><span class="material-icons-outlined">
            folder_shared
          </span>Shared Folder</h4>
        <h4 id="sharefile"> <span class="material-icons-outlined">
            chevron_right
          </span><span class="material-icons-outlined">
            description
          </span>Shared Files</h4>

      </div>

      <!-- Storage -->
      <div class="team-storage">
        <h3 class="title-text">Team Storage</h3>
        <h4> <span class="material-icons-outlined">
            chevron_right
          </span>Team 1</h4>
        <h4> <span class="material-icons-outlined">
            chevron_right
          </span>Team 2</h4>
        <button type="button" class="button-2"> <span class="material-icons-outlined">
            add
          </span>Add Storage </button>

        <div class="storage-details">
          <!-- details -->
          <div class="occupied-storage" id="image">
            <div class="occupied-bar" id="imageBar">
              <div class="fill-bar" id="fillBar" data-value="<?php echo $used; ?>"></div>
            </div>
            <div class="occupied-details" id="imageoccupied">
              <p><?php echo $used; ?> GB of <?php echo $total_storage ?> GB</p>
            </div>
          </div>
          <!-- end details -->
          <button type="button" class="button-1"> Upgrade plan </button>

        </div>
      </div>


    </div>
    <!-- end sidebar -->

    <div class="main">
      
      <div class="first-line">
        <h2>Dashboard</h2>
        <div class="first-line-right">
          <button class="button-2" id="chooseFilebtn" name="upload"><span class="material-icons-outlined">
              add</span>Upload Multiple</button>
          <form id="uploadForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" style="display: none;">
            <input type="hidden" name="upload_token" value="<?php echo $upload_token; ?>">
          </form>
          <button type="button" class="button-1" onclick="createBtn()"><span class="material-icons-outlined">
              add</span>Create</button>

        </div>
      </div>






      <h2 class="margin">Overview Storage</h2>
      <!-- second line -->
      <div class="second-line">


        <!-- box 1 -->
        <div class="box" id="accessimage">
          <div class="box-inner img-icon">
            <span class="material-icons-outlined">
              image
            </span>
            <div class="text">
              Image <div class="subtext"><?php
                                          $stml = "SELECT COUNT(*)as count FROM `files` WHERE file_path like '%photo%' ";
                                          $items = mysqli_query($conn, $stml);
                                          if ($items) {
                                            $row = mysqli_fetch_assoc($items);
                                            $count = $row['count'];
                                            echo "$count items";
                                          } else {
                                            echo "Error: " . mysqli_error($conn);
                                          }

                                          ?></div>
            </div>
          </div>

          <div class="occupied-storage" id="image">
            <div class="occupied-bar" id="imageBar">
              <div class="fill-bar" id="fillBar" data-value="<?php echo htmlspecialchars($photo_size); ?>"></div>
            </div>
            <div class="occupied-details" id="imageoccupied">
              <p class="subtext"><?php echo htmlspecialchars($photo_size); ?> MB of <?php echo htmlspecialchars($total_storage) ?> MB</p>
            </div>
          </div>

        </div>
        <!-- box 1 end -->


        <!-- box 2-->
        <div class="box" id="accessvideo">
          <div class="box-inner video-icon">
            <span class="material-icons-outlined">
              video_library
            </span>
            <div class="text">
              Video <div class="subtext"><?php
                                          $stml = "SELECT COUNT(*) as count FROM files WHERE file_path LIKE '%VIDEO%'";
                                          $items = mysqli_query($conn, $stml);
                                          if ($items) {
                                            $row = mysqli_fetch_assoc($items);
                                            $count = $row['count'];
                                            echo "$count items";
                                          } else {
                                            echo "Error:" . mysqli_error($conn);
                                          }
                                          ?></div>
            </div>
          </div>

          <div class="occupied-storage" id="image">
            <div class="occupied-bar" id="imageBar">
              <div id="fillBar" class="fill-bar video-bar" data-value="<?php echo htmlspecialchars($video_size); ?>"></div>
            </div>
            <div class="occupied-details" id="imageoccupied">
              <p class="subtext"><?php echo htmlspecialchars($video_size); ?> MB of <?php echo htmlspecialchars($total_storage) ?> MB</p>
            </div>
          </div>

        </div>
        <!-- box 2 end -->

        <!-- box 3 -->
        <div class="box" id="accessmusic">
          <div class="box-inner music-icon">
            <span class="material-icons-outlined">
              music_note
            </span>
            <div class="text">
              Music <div class="subtext"><?php
                                          $stml = "SELECT COUNT(*) as count FROM files WHERE file_path LIKE '%audio%'";
                                          $items = mysqli_query($conn, $stml);
                                          if ($items) {
                                            $row = mysqli_fetch_assoc($items);
                                            $count = $row['count'];
                                            echo "$count items";
                                          } else {
                                            echo "ERROR" . mysqli_error($conn);
                                          }
                                          ?></div>
            </div>
          </div>

          <div class="occupied-storage" id="music">
            <div class="occupied-bar" id="musicBar">
              <div class="fill-bar music-bar" id="fillBar" data-value="<?php echo htmlspecialchars($audio_size); ?>"></div>
            </div>
            <div class="occupied-details" id="musicOccupied">
              <p class="subtext"><?php echo htmlspecialchars($audio_size); ?> MB of <?php echo htmlspecialchars($total_storage )?> MB</p>
            </div>
          </div>

        </div>
        <!-- box 3 end -->

        <!-- box 4 -->
        <div class="box" id="accessdocuments">
          <div class="box-inner doc-icon">
            <span class="material-icons-outlined">
              description
            </span>
            <div class="text">
              Documents <div class="subtext">
                <?php
                $stml = "SELECT COUNT(*) as count FROM files WHERE file_path LIKE '%documents%'";
                $items = mysqli_query($conn, $stml);
                if ($items) {
                  $row = mysqli_fetch_assoc($items);
                  $count = $row['count'];
                  echo "$count items";
                } else {
                  echo "ERROR" . mysqli_error($conn);
                }
                ?>
              </div>
            </div>
          </div>

          <div class="occupied-storage" id="doc">
            <div class="occupied-bar" id="docBar">
              <div class="fill-bar doc-bar" id="fillBar" data-value="<?php echo htmlspecialchars($document_size); ?>"></div>
            </div>
            <div class="occupied-details" id="imageoccupied">
              <p class="subtext"><?php echo htmlspecialchars($document_size); ?> MB of <?php echo htmlspecialchars($total_storage) ?> MB</p>
            </div>
          </div>

        </div>
        <!-- box 4 end -->

      </div> <!-- end second line -->
      <!-- third line -->

      <!-- <h2 class="margin">Collections</h2> -->
      <div class="third-line">

        <?php
        if ($Collections == 1) {
        ?>
          <!-- preview-box 1 -->
          <div class="preview-box">
            <div class="preview-img">
              <img src="images/background.jpeg" alt="shared folder preview">
            </div>
            <div class="preview-info">
              <p class="text textElement" id="textElement">my shared
              </p><span class="material-icons-outlined text ">
                more_vert</span>
            </div>
          </div>
          <!-- end preview-box 1 -->

          <!-- preview-box 1 -->
          <div class="preview-box">
            <div class="preview-img">
              <img src="images/background.jpeg" alt="shared folder preview">
            </div>
            <div class="preview-info">
              <p class="text textElement" id="textElement">test </p><span class="material-icons-outlined text ">
                more_vert</span>
            </div>
          </div>
          <!-- end preview-box 1 -->

          <!-- preview-box 1 -->
          <div class="preview-box">
            <div class="preview-img">
              <img src="images/loginback.jpg" alt="shared folder preview">
            </div>
            <div class="preview-info">
              <p class="text textElement" id="textElement">dsj agdkajbcasdkagcssadkj </p><span class="material-icons-outlined text ">
                more_vert</span>
            </div>
          </div>
          <!-- end preview-box 1 -->

          <!-- preview-box 1 -->
          <div class="preview-box">
            <div class="preview-img">
              <img src="images/background.jpeg" alt="shared folder preview">
            </div>
            <div class="preview-info">
              <p class="text textElement" id="textElement">zzzzzasd hdkn </p><span class="material-icons-outlined text ">
                more_vert</span>
            </div>
          </div>
          <!-- end preview-box 1 -->

        <?php } else { ?>
          <p>No collections
            <button type="button" class="button-1" onclick="createCollection()"><span class="material-icons-outlined">
                add</span>Create</button>
          </p>
        <?php } ?>
      </div>

      <!-- end third line -->

      <!-- Fourth line -->

      <h2 class="margin">Information About the Uploaded items</h2>
      <div class="fourth-line">

        <table class="table" id="myTable">
          <thead>
            <tr>
              <th scope="col">Sno</th>
              <th scope="col">Name</th>
              <th scope="col">FileType</th>
              <th scope="col">uploaded_on</th>
              <th scope="col">FileSize</th>


            </tr>
            <hr>
          </thead>
          <?php
          $rowrepeat = true;
          $stmt = "SELECT * FROM `files`";
          $result = mysqli_query($conn, $stmt);
          $sno = 0;
          while ($row = mysqli_fetch_assoc($result)) {
            $sno = $sno + 1;
            echo "<tr>
      <th scope='row'>" . $sno . "</th>
      <td>" . $row['file_name'] . "</td>
      <td>" . $row['file_type'] . "</td>
      <td>" . $row['uploaded_date'] . "</td>
      <td>" . $row['file_size'] . "</td>
      
      

    </tr>";
          }
          ?>
        </table>


      </div>
      <!-- end fourth line -->



    </div> <!-- end main -->

  </div> <!-- end Container -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="//cdn.datatables.net/2.0.6/js/dataTables.min.js"></script>
  <script src="script.js"></script>

  <script>
    $(document).ready(function() {
      $('#myTable').DataTable();
    });

    document.querySelector("#h4-storage").addEventListener("click",()=>{
      alert("the total storage occupied by you is <?php echo htmlspecialchars($total_size) ?> MB");
    })
  </script>
</body>

</html>