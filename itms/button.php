<!-- Go Back Button -->
<div class="go-back-container">
    <button class="go-back-btn" onclick="history.back()">Go Back</button>
</div>

<style>
/* ---------- Go Back Button Styling ---------- */
.go-back-container {
    display:flex;
    justify-content: left;
    left: 20px;
    position: fixed;
    bottom: 60px;
    width: 100%;
    text-align: center;
    right: 30px;
    z-index: 1000;
}

.go-back-btn {
    background-color: #007bff; /* Blue color */
    color: white; /* White text */
    border: none;
    padding: 12px 28px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

/* Hover Effect - Pop Animation */
.go-back-btn:hover {
    transform: scale(1.1);
    background-color: #0056b3; /* Darker blue on hover */
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
}
</style>
