<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beautiful Content Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: 5px solid #e8491d;
            text-align: center;
        }
        header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .content {
            background: #ffffff;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        footer {
            text-align: center;
            padding: 20px;
            margin-top: 20px;
            background: #35424a;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to My Beautiful Page</h1>
    </header>
    
    <div class="container">
        <div class="content">
            <h2>About This Page</h2>
            <p>This page is designed using HTML, CSS, and PHP to demonstrate a beautiful layout with a modern look.</p>
            <p>PHP can be used to make this page dynamic and interactive.</p>
            <?php echo "<p style='color: #e8491d; font-weight: bold;'>This is a dynamic message from PHP!</p>"; ?>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 ©Shubham Dhole. All Rights Reserved.</p>
    </footer>
</body>
</html>
