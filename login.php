<?php
session_start();
include 'connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Sanitize input to prevent SQL injection
    $username = $conn->real_escape_string($username);

    // Prepare the SQL statement
    $sql = "SELECT * FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;

                // Redirect to dashboard
                header("Location: index.html");
                exit();
      // Important to stop script execution after redirect
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "Username not found.";
        }
        $stmt->close();
    } else {
        $message = "Database query error.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        .login-container button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .login-container p {
            margin-top: 10px;
        }
        .login-container p.error {
            color: red;
        }
        .login-container a {
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Login</button>
        </form>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
