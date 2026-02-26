<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FDA Verification Portal - Maintenance</title>

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #286634, #79af60);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .maintenance-container {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 40px;
      max-width: 650px;
      width: 90%;
      text-align: center;
      box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(8px);
    }

    .logo {
      font-size: 30px;
      font-weight: bold;
      color: #1d583c;
      margin-bottom: 10px;
      text-transform: uppercase;
    }

    h1 {
      margin: 10px 0;
      font-size: 28px;
      color: #ffffff;
    }

    p {
      font-size: 16px;
      line-height: 1.6;
      color: #f0f0f0;
    }

    .notice {
      margin-top: 20px;
      padding: 15px;
      border-radius: 10px;
      background: rgba(0, 191, 99, 0.2);
      border: 1px solid #00bf63;
      font-weight: bold;
      color: #ffffff;
    }

    .footer {
      margin-top: 30px;
      font-size: 13px;
      color: #e0e0e0;
    }

    .spinner {
      margin: 25px auto;
      width: 60px;
      height: 60px;
      border: 6px solid rgba(255, 255, 255, 0.3);
      border-top: 6px solid #00bf63;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .btn {
      display: inline-block;
      margin-top: 25px;
      padding: 12px 25px;
      background: #00bf63;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      transition: 0.3s ease;
    }

    .btn:hover {
      background: #79af60;
      transform: scale(1.05);
    }
  </style>
</head>

<body>
  <div class="maintenance-container">

    <div class="logo">FDA Verification Portal</div>

    <h1>System Maintenance</h1>

    <div class="spinner"></div>

    <p>
      Our system is currently undergoing maintenance to improve security and performance.
      We will be back online shortly.
    </p>

    <div class="notice">
      Please try again later. Thank you for your understanding.
    </div>

    <a href="{{ url()->current() }}" class="btn">Refresh Page</a>

    <div class="footer">
      &copy; {{ date('Y') }} FDA Verification Portal | All Rights Reserved
    </div>

  </div>
</body>
</html>
