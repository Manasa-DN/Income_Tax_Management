<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - ITMS</title>
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Arial, sans-serif;
    }

    body {
      background-color: #f5f8fc;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* Header */
    header {
      width: 100%;
      background-color: #0b3d91;
      color: white;
      padding: 18px 40px;
      font-size: 1.3rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /* Main title */
    h2 {
      margin-top: 40px;
      font-size: 2rem;
      color: #0b3d91;
      text-shadow: 0 0 5px rgba(11,61,145,0.1);
    }

    /* Contact Cards Container */
    .contact-container {
      margin-top: 40px;
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
      justify-content: center;
      width: 100%;
      padding: 20px;
    }

    /* Individual Contact Card */
    .contact-card {
      background: #fff;
      width: 300px;
      padding: 30px;
      border-radius: 18px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
      text-align: center;
      transition: 0.3s ease;
    }

    .contact-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .contact-card img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      margin-bottom: 15px;
      border: 3px solid #0b3d91;
      object-fit: cover;
    }

    .contact-card h3 {
      font-size: 1.2rem;
      color: #0b3d91;
      margin-bottom: 6px;
    }

    .info {
      font-size: 15px;
      color: #333;
      margin-bottom: 8px;
    }

    .contact-card a {
      color: #0b3d91;
      font-weight: 600;
      text-decoration: none;
    }

    .contact-card a:hover {
      text-decoration: underline;
    }

    /* Social icon */
    .linkedin-icon {
      width: 28px;
      margin-top: 10px;
      cursor: pointer;
    }
  </style>
</head>

<body>

  <header>
    ITMS - Income Tax Management System
  </header>

  <h2>Contact Us</h2>

  <div class="contact-container">  
      <div class="contact-card">
      <h3>Vaibhav P</h3>
      <p class="info">📧 <a href="mailto:1da23cs183.cs@drait.edu.in">1da23cs183.cs@drait.edu.in</a></p>
      <p class="usn">USN: 1DA23CS183 </p>
      <p class="info">📞 +91 76187 24668</p>
      <a href="https://linkedin.com" target="_blank">
        <img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" class="linkedin-icon">
      </a>
    </div>

    <div class="contact-card">
      <h3>Manasa D N</h3>
      <p class="info">📧 <a href="mailto:1da23cs206.cs@drait.edu.in">1da23cs206.cs@drait.edu.in</a></p>
      <p class="usn">USN: 1DA23CS206 </p>
      <p class="info">📞 +91 91083 68454</p>
      <a href="https://linkedin.com" target="_blank">
        <img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" class="linkedin-icon">
      </a>
    </div>

    <div class="contact-card">
      <h3>Kavya Moodi</h3>
      <p class="info">📧 <a href="mailto:1da23cs203.cs@drait.edu.in">1da23cs203.cs@drait.edu.in</a></p>
      <p class="usn">USN: 1DA23CS203 </p>
      <p class="info">📞 +91 86185 93326</p>
      <a href="https://linkedin.com" target="_blank">
        <img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" class="linkedin-icon">
      </a>
    </div>
  </div>


<?php include("button.php");?>
<?php include("footer.php");?>

</body>
</html>
