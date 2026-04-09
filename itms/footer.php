<!-- -------------------- FOOTER SECTION -------------------- -->
<footer>
  <div class="footer-container">
    <p>&copy; <?php echo date("Y"); ?> ITMS - Income Tax Management System. All Rights Reserved.</p>

    <div class="quick-links">
      <a href="nandini.php">Under the Guidance</a>
      <a href="contact.php">Contact</a>
    </div>
  </div>
</footer>

<style>
/* -------------------- FOOTER STYLING (MATCHED THEME) -------------------- */
footer {
  background-color: #0d3b66;
  color: #ffffff;
  text-align: center;
  padding: 15px 0;
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  font-family: 'Segoe UI', sans-serif;
  font-size: 0.95rem;
  box-shadow: 0 -3px 8px rgba(0, 0, 0, 0.1);
  z-index: 10;
}

.footer-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1000px;
  margin: 0 auto;
  padding: 0 20px;
}

.quick-links a {
  color: #ffffff;
  text-decoration: none;
  margin-left: 15px;
  font-weight: 500;
  transition: color 0.3s ease;
}

.quick-links a:hover {
  color: #ffd700; /* subtle gold hover for professional feel */
}

footer p {
  margin: 0;
  letter-spacing: 0.5px;
}

@media (max-width: 600px) {
  .footer-container {
    flex-direction: column;
    gap: 8px;
  }

  footer {
    font-size: 0.85rem;
    padding: 10px 0;
  }
}
</style>
