<?php
session_start(); // Start the PHP session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve values from the POST request
    $email = $_POST['email'];
    $pw = $_POST['password'];
    
    // Validate the email (optional)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }
    
    // Database connection parameters
    $host = 'localhost'; // Your database host (e.g., localhost)
    $dbname = 'data'; // Your database name
    $username = 'root'; // Your database username
    $password = ''; // Your database password
    
    // Attempt to create a PDO instance (connect to the database)
    try {
        // Attempt to connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error connecting to database: " . $e->getMessage());
    }
    
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = :email AND password = :password");
        $stmt->execute(['email' => $email, 'password' => $pw]);
        
        // Fetch the user as an associative array
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            
            // Redirect to interface.html upon successful login
            header("Location: interface1.html");
            exit;
        } else {
            // Incorrect password
            echo "Invalid email or password";
        }
        
    
} else {
    // Redirect to login page if accessed directly without POST request
    header("Location: login.html");
    exit;
}
?>