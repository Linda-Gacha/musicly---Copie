<?php
// Your database connection
$host = 'localhost';
$dbname = 'data';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the search query is provided
    if (isset($_GET['query'])) {
        $searchQuery = $_GET['query'];

        // Prepare and execute the search query
        $stmt = $pdo->prepare("SELECT * FROM user WHERE fullname LIKE :query");
        $stmt->execute(['query' => '%' . $searchQuery . '%']);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display search results
        if (count($searchResults) > 0) {
            echo "<h2>Search Results:</h2>";
            echo "<table>";
            echo "<thead><tr><th>Full Name</th><th>Email</th><th>Phone Number</th><th>Membership Type</th></tr></thead>";
            echo "<tbody>";
            foreach ($searchResults as $result) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($result['fullname']) . "</td>";
                echo "<td>" . htmlspecialchars($result['email']) . "</td>";
                echo "<td>" . htmlspecialchars($result['phonenumber']) . "</td>";
                echo "<td>" . htmlspecialchars($result['membershiptype']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "No results found.";
        }
    } else {
        echo "Search query not provided.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
