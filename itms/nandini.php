<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Guide - ITMS</title>
  <style>
    /* ---------- Global Reset ---------- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Arial, sans-serif;
    }

    /* ---------- Body ---------- */
    body {
      background-color: #f5f8fc;
      color: #1a1a1a;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }

    /* ---------- Navbar ---------- */
    header {
      background-color: #0b3d91;
      color: #fff;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 40px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      position: fixed;
      top: 0;
      z-index: 100;
    }

    header h1 {
      font-size: 1.4rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    /* ---------- Main Content ---------- */
    main {
      margin-top: 120px;
      text-align: center;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      animation: fadeIn 0.8s ease-in-out;
    }

    h2 {
      font-size: 2rem;
      color: #0b3d91;
      margin-bottom: 20px;
      text-shadow: 0 0 5px rgba(11, 61, 145, 0.1);
    }

    /* ---------- Guide Card ---------- */
    .guide-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      padding: 40px 50px;
      width: 400px;
      margin-top: 40px;
      text-align: center;
      animation: slideUp 0.6s ease forwards;
      display: block; /* visible by default */
    }

    .guide-card img {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 20px;
      box-shadow: 0 6px 20px rgba(11, 61, 145, 0.3);
      border: 3px solid #0b3d91;
    }

    .guide-details strong {
      font-size: 22px;
      color: #0b3d91;
    }

    .guide-details {
      font-size: 17px;
      line-height: 1.6;
      color: #333;
    }

    .guide-details a {
      color: #0b3d91;
      text-decoration: none;
      font-weight: 600;
    }

    .guide-details a:hover {
      text-decoration: underline;
    }

    /* ---------- Footer ---------- */
    footer {
      margin-top: 60px;
      color: #555;
      font-size: 14px;
    }

    /* ---------- Animations ---------- */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 600px) {
      .guide-card {
        width: 90%;
        padding: 30px;
      }
      h2 {
        font-size: 1.6rem;
      }
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <header>
    <h1>ITMS - Income Tax Management System</h1>
  </header>

  <!-- Main Content -->
  <main>
    <h2>Project Guide</h2>

    <div class="guide-card" id="guideCard">
      <img src="guide.jpg" alt="Guide Photo">
      <div class="guide-details">
        <strong>Dr. Nandini N</strong><br>
        Professor &amp; Head<br>
        Department of Computer Science and Engineering Programme<br>
        <a href="mailto:nandinin.cs@drait.edu.in">nandinin.cs@drait.edu.in</a><br>
        Dr. Ambedkar Institute of Technology
      </div>
    </div>
    <?php include("button.php"); ?>
  </main>

  <?php include("footer.php");?>

</body>
</html>
