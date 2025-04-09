<?php
// Démarrer la session
session_start();

// Connexion à la base de données avec MySQLi
$servername = "localhost";
$username = "root"; // Utilisateur MySQL
$password = "";     // Mot de passe MySQL
$dbname = "manifestation"; // Nom de la base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer tous les créneaux et leurs responsables
$creneaux = [];
$query = "SELECT c.*, r.id_U AS Responsable_ID, cp.Nom_C AS Nom_Resp, cp.Prenom_C AS Prenom_Resp 
          FROM créneau c 
          LEFT JOIN responsable r ON c.id_C = r.id_C 
          LEFT JOIN compte cp ON r.id_U = cp.id_U";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $creneaux[] = $row;
    }
} else {
    echo "Erreur lors de la récupération des créneaux : " . $conn->error;
    exit();
}

// Ajouter ou retirer un responsable
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['id_C'])) {
    $id_C = intval($_POST['id_C']); // ID du créneau
    $id_U = $_SESSION['id_U'];     // ID de l'utilisateur (responsable)

    if ($_POST['action'] == 'ajouter') {
        // Vérifier si ce responsable est déjà affecté à un créneau
        $query_check = "SELECT * FROM responsable WHERE id_U = ?";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param('i', $id_U);
        $stmt_check->execute();
        $stmt_check->store_result();
        $isAlreadyAssigned = $stmt_check->num_rows > 0;
        $stmt_check->close();

        if ($isAlreadyAssigned) {
            echo "Vous êtes déjà responsable d'un autre créneau.";
        } else {
            // Affecter le responsable au créneau
            $query_add = "INSERT INTO responsable (id_U, id_C) VALUES (?, ?)";
            $stmt_add = $conn->prepare($query_add);
            $stmt_add->bind_param('ii', $id_U, $id_C);
            if ($stmt_add->execute()) {
                echo "Vous avez été affecté à ce créneau.";
            } else {
                echo "Erreur lors de l'affectation : " . $conn->error;
            }
            $stmt_add->close();
        }
    } elseif ($_POST['action'] == 'retirer') {
        // Retirer le responsable du créneau
        $query_remove = "DELETE FROM responsable WHERE id_U = ? AND id_C = ?";
        $stmt_remove = $conn->prepare($query_remove);
        $stmt_remove->bind_param('ii', $id_U, $id_C);
        if ($stmt_remove->execute()) {
            echo "Vous avez été retiré de ce créneau.";
        } else {
            echo "Erreur lors de la suppression : " . $conn->error;
        }
        $stmt_remove->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Responsable</title>
    <style>
        :root {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #ffcdd2;
            --accent: #ff4081;
            --accent-hover: #f50057;
            --danger: #ff80ab;
            --danger-hover: #ff4081;
            --card-shadow: 0 4px 6px rgba(255, 64, 129, 0.2);
            --gold: #ff80ab;
            --border-radius: 12px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-primary);
            margin: 0;
            padding: 40px;
            color: var(--text-primary);
            background-image: linear-gradient(45deg, #1a1a1a 25%, #2d2d2d 25%, #2d2d2d 50%, #1a1a1a 50%, #1a1a1a 75%, #2d2d2d 75%, #2d2d2d 100%);
            background-size: 56.57px 56.57px;
            min-height: 100vh;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: var(--bg-secondary);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: 1px solid var(--accent);
        }

        h1, h2 {
            text-align: center;
            color: var(--accent);
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-shadow: 2px 2px 4px rgba(255, 64, 129, 0.3);
        }

        h1 {
            font-size: 3rem;
            margin-top: 20px;
        }

        h2 {
            font-size: 2.2rem;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin: 20px 0;
            font-size: 1.1rem;
        }

        .table th, .table td {
            padding: 18px;
            text-align: left;
        }

        .table th {
            background: var(--bg-secondary);
            font-weight: bold;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            border-bottom: 2px solid var(--accent);
        }

        .table tr {
            background: rgba(45, 45, 45, 0.8);
            margin-bottom: 8px;
            border-radius: var(--border-radius);
            transition: all var(--transition-speed);
        }

        .table tr:hover {
            background: rgba(255, 64, 129, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 64, 129, 0.2);
        }

        .table td {
            color: var(--text-secondary);
            border-top: 1px solid var(--accent);
        }

        .table td:first-child {
            border-top-left-radius: var(--border-radius);
            border-bottom-left-radius: var(--border-radius);
        }

        .table td:last-child {
            border-top-right-radius: var(--border-radius);
            border-bottom-right-radius: var(--border-radius);
        }

        button {
            background-color: var(--accent);
            color: var(--text-primary);
            padding: 12px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: all var(--transition-speed);
            margin: 0 5px;
        }

        button:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 64, 129, 0.3);
        }

        button[name="action"][value="retirer"] {
            background-color: var(--danger);
        }

        button[name="action"][value="retirer"]:hover {
            background-color: var(--danger-hover);
        }

        .logout-btn {
            background-color: var(--danger);
            color: var(--text-primary);
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(255, 64, 129, 0.3);
            transition: all var(--transition-speed);
            z-index: 1000;
        }

        .logout-btn:hover {
            background-color: var(--danger-hover);
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(255, 64, 129, 0.4);
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .container {
                padding: 20px;
                margin: 20px auto;
            }

            h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.8rem;
            }

            .table {
                font-size: 0.9rem;
            }

            .table th, .table td {
                padding: 12px;
            }

            button {
                padding: 10px 16px;
                font-size: 0.9rem;
            }

            .logout-btn {
                position: relative;
                top: auto;
                right: auto;
                display: block;
                margin: 20px auto;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <a href="../../manifestation/ADMINS/connexion/déconnexion.php" class="logout-btn">Déconnexion</a>
    <h1>Tableau de Bord - Responsable</h1>
    <div class="container">
        <h2>Liste des Activités</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Heure de début</th>
                    <th>Responsable</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($creneaux as $creneau): ?>
                    <tr>
                        <td><?= htmlspecialchars($creneau['Nom_M']); ?></td>
                        <td><?= htmlspecialchars($creneau['Desc_M']); ?></td>
                        <td><?= htmlspecialchars($creneau['date_deb']); ?></td>
                        <td><?= htmlspecialchars($creneau['Heure_deb']); ?></td>
                        <td><?= htmlspecialchars($creneau['Nom_Resp'] ?? 'Aucun'); ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="id_C" value="<?= $creneau['id_C']; ?>">
                                <button name="action" value="ajouter">S'inscrire</button>
                                <button name="action" value="retirer">Se retirer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>