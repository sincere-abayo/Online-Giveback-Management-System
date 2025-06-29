<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, shrink-to-fit=no">
    <title>GMS - Registration Success</title>
    <link rel="stylesheet" type="text/css" href="inc/.css" media="all">
    <link rel="shortcut icon" href="gms1.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #222;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card {
            background-color: #fff;
            color: #222;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background-color: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .success-icon i {
            font-size: 60px;
            color: #28a745;
        }

        .success-title {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
        }

        .success-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .progress-fill {
            height: 100%;
            background-color: #28a745;
            width: 0%;
            animation: progress 3s linear forwards;
        }

        @keyframes progress {
            to {
                width: 100%;
            }
        }

        .redirect-message {
            font-size: 14px;
            color: #888;
            margin-bottom: 30px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-custom {
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary-custom {
            background-color: #2c5aa0;
            color: #fff;
        }

        .btn-primary-custom:hover {
            background-color: #24487a;
        }

        .btn-secondary-custom {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
            color: white;
            text-decoration: none;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #f8f9fa;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }

        .security-badge i {
            color: #28a745;
        }
    </style>
</head>

<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="success-title">Registration Successful!</h1>

        <p class="success-message">
            Thank you for joining our volunteer community! Your registration has been received and is being processed.
        </p>

        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>

        <p class="redirect-message">
            Redirecting to homepage in <span id="countdown">3</span> seconds...
        </p>

        <div class="action-buttons">
            <a href="./index.php" class="btn-custom btn-primary-custom">
                <i class="fas fa-home"></i> Go to Homepage
            </a>
            <a href="./registration.php" class="btn-custom btn-secondary-custom">
                <i class="fas fa-plus"></i> Register Another
            </a>
        </div>

        <div class="security-badge">
            <i class="fas fa-shield-alt"></i>
            Your information is secure and encrypted
        </div>
    </div>

    <script>
        // Countdown timer
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');

        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = './index.php';
            }
        }, 1000);
    </script>

    <?php
    // Redirect after 3 seconds
    header("refresh:3;URL=./index.php");
    ?>
</body>

</html>