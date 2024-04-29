<?php
// Database connection parameters
$host = 'localhost'; // or your server IP address
$dbname = 'data'; // your database name
$username = 'root'; // your database username
$password = ''; // your database password

// Attempt to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $addresse = $_POST['address'];
    $membershiptype = $_POST['membershiptype'];

    // Check if fullname or email already exist
    $sql_check = "SELECT COUNT(*) AS count FROM user WHERE fullname = :fullname OR email = :email";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute(['fullname' => $fullname, 'email' => $email]);
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        die("Error: Fullname or Email already exists!");
    }

    // Prepare SQL statement to insert data into database
    $sql = "INSERT INTO user (fullname, email, phonenumber, addresse, membershiptype) 
            VALUES (:fullname, :email, :phonenumber, :addresse, :membershiptype)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute the statement
    try {
        $stmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'phonenumber' => $phonenumber,
            'addresse' => $addresse,
            'membershiptype' => $membershiptype
        ]);
        echo "Data inserted successfully!";
    } catch (PDOException $e) {
        die("Error inserting data: " . $e->getMessage());
    }
} else {
    // If the form was not submitted via POST method, redirect to index.html or display an error message
    echo "Form submission error!";
}
?>