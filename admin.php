<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Styles pour le corps et l'en-tête */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #111;
            color: #fff;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin: 0;
        }

        /* Styles pour la table des utilisateurs */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #444;
        }

        tr:nth-child(even) {
            background-color: #333;
        }

        tr:hover {
            background-color: #555;
        }

        /* Styles pour les boutons */
        .btn {
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .btn-delete {
            background-color: #f44336; /* Rouge */
            color: white;
        }

        .btn-stats {
            background-color: #007bff; /* Bleu */
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }

        /* Styles pour le champ de recherche et les messages */
        .search-container {
            margin-bottom: 20px;
            position: relative; /* Position relative pour le message de chargement */
        }

        .search-container input[type=text] {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 300px; /* Largeur du champ de recherche */
        }

        .no-results {
            color: #f00;
            font-weight: bold;
            display: none; /* Hide by default */
            margin-top: 10px;
        }

        .loading-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
        }

        /* Styles pour le modal de statistiques */
        .modal {
            display: none; /* Caché par défaut */
            position: fixed; /* Positionnement fixe pour la superposition */
            z-index: 1; /* Positionnement au-dessus de tout */
            left: 0;
            top: 0;
            width: 100%; /* Largeur totale */
            height: 100%; /* Hauteur totale */
            overflow: auto; /* Ajout de défilement si nécessaire */
            background-color: rgba(0, 0, 0, 0.5); /* Fond sombre avec transparence */
            padding-top: 60px; /* Espace pour l'entête */
        }

        .modal-content {
            background-color: #222;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
            color: #fff;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <?php
    // Database connection parameters
    $host = 'localhost'; // your host
    $dbname = 'data'; // your database name
    $username = 'root'; // your username
    $password = ''; // your password

    // Attempt to connect to the database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        // Set PDO to throw exceptions on error
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error connecting to database: " . $e->getMessage());
    }

    // Fetch all users from the 'user' table
    $stmt = $pdo->query("SELECT * FROM user");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get user statistics based on membership type
    $membershipCounts = array();
    foreach ($users as $user) {
        $membership = $user['membershiptype'];
        if (isset($membershipCounts[$membership])) {
            $membershipCounts[$membership]++;
        } else {
            $membershipCounts[$membership] = 1;
        }
    }
    ?>

    <!-- Search Container -->
    <div class="search-container">
        <!-- Champ de recherche -->
        <input type="text" id="searchInput" placeholder="Search by name..." oninput="searchUser()">
        <!-- Message de chargement -->
        <div id="loadingMessage" class="loading-message">Searching...</div>
    </div>

    <!-- Message "No Results" -->
    <p id="searchResultMessage" class="no-results">No users found with the given search query.</p>

    <!-- Tableau des utilisateurs -->
    <table id="userTable">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Membership Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Affichage des utilisateurs depuis la base de données -->
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phonenumber']); ?></td>
                <td><?php echo htmlspecialchars($user['membershiptype']); ?></td>
                <!-- Bouton "Delete" pour chaque utilisateur -->
                <td>
                    <button class="btn btn-delete" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Bouton "Show Statistics" -->
    <button class="btn btn-stats" onclick="showStats()">Show Statistics</button>

    <!-- Modal pour les statistiques -->
    <div id="statsModal" class="modal">
        <div class="modal-content">
            <!-- Bouton "Close" pour fermer le modal -->
            <span class="close" onclick="closeStatsModal()">&times;</span>
            <!-- Titre du modal -->
            <h2>User Statistics</h2>
            <!-- Tableau des statistiques -->
            <table>
                <thead>
                    <tr>
                        <th>Membership Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Affichage des statistiques -->
                    <?php foreach ($membershipCounts as $membership => $count): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($membership); ?></td>
                            <td><?php echo htmlspecialchars($count); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script JavaScript pour les fonctionnalités interactives -->
    <script>
        // Fonction pour supprimer un utilisateur
        function deleteUser(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
                // Requête AJAX pour supprimer l'utilisateur
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        // Actualiser la page après la suppression
                        location.reload();
                    }
                };
                xhr.open("POST", "delete.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("userId=" + userId);
            }
        }

        // Fonction pour effectuer la recherche des utilisateurs
        function searchUser() {
            var searchValue = document.getElementById("searchInput").value.trim().toUpperCase();
            var table = document.getElementById("userTable");
            var tr = table.getElementsByTagName("tr");
            var found = false; // Flag to track if any results found

            // Afficher le message de chargement
            var loadingMessage = document.getElementById("loadingMessage");
            loadingMessage.style.display = "block";

            for (var i = 0; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName("td")[0]; // Index 0 for Full Name column
                if (td) {
                    var txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(searchValue) > -1) {
                        tr[i].style.display = "";
                        found = true;
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }

            // Cacher le message de chargement après la recherche
            loadingMessage.style.display = "none";

            // Afficher tous les utilisateurs si le champ de recherche est vide
            if (searchValue === "") {
                for (var i = 0; i < tr.length; i++) {
                    tr[i].style.display = "";
                }
                found = true;
            }

            // Afficher le message "No results" si aucun utilisateur trouvé
            var message = document.getElementById("searchResultMessage");
            if (!found) {
                message.style.display = "block";
            } else {
                message.style.display = "none";
            }
        }

        // Fonction pour afficher le modal de statistiques
        function showStats() {
            var modal = document.getElementById("statsModal");
            modal.style.display = "block";
        }

        // Fonction pour fermer le modal de statistiques
        function closeStatsModal() {
            var modal = document.getElementById("statsModal");
            modal.style.display = "none";
        }

        // Focus automatique sur le champ de recherche lors du chargement de la page
        window.onload = function() {
            document.getElementById("searchInput").focus();
        };
    </script>
</body>
</html>
