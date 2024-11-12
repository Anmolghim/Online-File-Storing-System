<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mountain Template</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="styles.css"> -->
     <style>
      header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background-color: #343a40; /* Example background color */
}

.content-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  width: 100%;
}

.text-content {
  flex: 1;
  text-align: left;
  padding-right: 20px;
}

.photo {
  flex-shrink: 0;
}

.photo img {
  max-width: 100%;
  height: auto;
  display: block;
  border-radius: 10px;
  box-shadow: 3px 3px blanchedalmond;
}

.text-white {
  color: #fff;
}

     </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">G-Share(Online File Sharing System)</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item active">
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
      </li>
      <li class="nav-item">
        <a class="btn btn-primary" href="login.php">Login</a>
      </li>
    </ul>
  </div>
</nav>

<!-- Header -->
<header class="text-center py-4" >
  
  <h1 class="display-4 text-white">Share Your Stuff To Your Lovely <br>One And Faster Access To <br>Shared Items</h1>
   <!-- <p class="lead text-white">G(Group)-share is a online file sharing platform where you can share audio,video,document,photos<br>etc to you friends family and to your close circle at the time.</p> -->
  <div class="photo">
    <img src="onlineshare.jpeg" alt="anmol">
  </div>
  <!-- <a href="#" class="btn btn-outline-light mx-2">Services</a>
  <a href="#" class="btn btn-outline-light mx-2">About Us</a> -->
 
</header>

<!-- Services Section -->
<section class="py-5 text-center">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <img src="service1.jpeg" class="img-fluid mb-3" alt="Service 1">
        <h5>Keep the backup for your files and folder</h5>
        <p>
          Gshare continuously backs up your company's documents, allowing you to restore any file at any time. Instantly recover from ransomware, hardware failures, or human errors
        </p>
      </div>
      <div class="col-md-4">
        <img src="service2.jpeg" class="img-fluid mb-3" alt="Service 2">
        <h5>Share and collaborate securely with anyone</h5>
        <p>Create centralized folders that your internal team members and external collaborators can easily access. Manage permissions to keep your most important work protected at all times.</p>
      </div>
      <div class="col-md-4">
        <img src="service3.jpeg" class="img-fluid mb-3" alt="Service 3">
        <h5>Access your files anywhere, any time</h5>
        <p>Access your files instantly from all your computers, mobile devices and the web. Work from home, from the office or from the most idea-inspiring places on the planet.</p>
      </div>
    </div>
  </div>
</section>

<!-- Call To Action Section -->
<section class="py-5 bg-light text-center">
  <div class="container">
    <h2>Merits For Using These System</h2>
    <p class="lead">Users can upload and download files easily,The system stores files in the cloud, enabling access from any internet-connected device. Security measures include encryption during transfer and storage,search functionality helps users find files by name, type, date, or other criteria.File previews allow users to view documents, images, and videos without downloading them. These features together create a comprehensive, secure, and user-friendly file-sharing system.provides an easy and secure way to access, store, and collaborate on files and folders from your mobile device, tablet, or computer. With built-in protections against malware and spam, it ensures the safety of your shared filesWhether you’re collaborating with colleagues or sharing files with friends, G-Share simplifies the process and keeps everyone connected!</p>
    <!-- <a href="#" class="btn btn-outline-primary">Our History</a> -->
  </div>
</section>

<!-- Gallery Section -->
<section class="py-5 text-center">
  <h2>Thanks to G-share and say goodbye to surprise transfer fees.</h2>
  <div class="container">.
    
    <div class="row">
     
      <div class="col-md-3">
        <img src="THANKS1.jpeg" class="img-fluid mb-3" alt="Gallery Image 1">
      </div>
      <div class="col-md-3">
        <img src="THANKS2.jpeg" class="img-fluid mb-3" alt="Gallery Image 2">
      </div>
      <div class="col-md-3">
        <img src="THANKS3.jpeg" class="img-fluid mb-3" alt="Gallery Image 3">
      </div>
      <div class="col-md-3">
        <img src="THANKS4.jpeg" class="img-fluid mb-3" alt="Gallery Image 4">
      </div>
      <div class="col-md-3">
        <img src="THANKS5.jpeg" class="img-fluid mb-3" alt="Gallery Image 5">
      </div>
      <div class="col-md-3">
        <img src="THANKS6.jpeg" class="img-fluid mb-3" alt="Gallery Image 6">
      </div>
      <div class="col-md-3">
        <img src="THANKS7.jpeg" class="img-fluid mb-3" alt="Gallery Image 7">
      </div>
      <div class="col-md-3">
        <img src="THANKS8.png" class="img-fluid mb-3" alt="Gallery Image 8">
      </div>
    </div>
  </div>
</section>
<section class="py-5 bg-light text-center">
  <div class="container">
    <h2>Contact Us:</h2>
    <p class="lead">Phone no:9861856355<br>Email:Adarsharai321@gmail.com</p>
    <!-- <a href="#" class="btn btn-outline-primary">Our History</a> -->
  </div>
</section>
<!-- Footer -->
<footer class="py-4 bg-light text-center">
  <div class="container">
    <p class="mb-0">© 2024 Project Work | Mechi Multiple Campus</p>
  </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
