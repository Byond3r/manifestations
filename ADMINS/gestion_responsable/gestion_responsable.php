<?php
session_start();
include '../../db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../connexion/connexion.php");
    exit();
}

// Récupérer les activités et les utilisateurs
$activities = $conn->query("SELECT * FROM créneau")->fetchAll();
$users = $conn->query("SELECT id_U, Nom_C, Prenom_C FROM compte WHERE Role_C = 'Responsable'")->fetchAll();

// Affecter un responsable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activity_id'], $_POST['responsable_id'])) {
    $activityId = $_POST['activity_id'];
    $responsableId = $_POST['responsable_id'];

    // Vérifier si un responsable existe déjà pour cette activité
    $existing = $conn->prepare("SELECT * FROM responsable WHERE id_C = ?");
    $existing->execute([$activityId]);

    if ($existing->rowCount() > 0) {
        // Mettre à jour le responsable
        $update = $conn->prepare("UPDATE responsable SET id_U = ? WHERE id_C = ?");
        $update->execute([$responsableId, $activityId]);
    } else {
        // Ajouter un nouveau responsable
        $insert = $conn->prepare("INSERT INTO responsable (id_U, id_C) VALUES (?, ?)");
        $insert->execute([$responsableId, $activityId]);
    }

    header("Location: ../../gestion_responsable/gestion_responsables.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Responsables</title>
    <style>
        :root {
            --primary-color: #ff69b4;
            --background-dark: #2b2b2b;
            --background-light: #383838;
            --text-color: #fff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--background-dark);
            color: var(--text-color);
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        h2, h3 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        h3 {
            font-size: 1.5rem;
            margin-top: 3rem;
        }

        form {
            background-color: var(--background-light);
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem 0;
            border: 2px solid var(--primary-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            align-items: center;
            justify-content: center;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 250px;
        }

        label {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.1rem;
        }

        select {
            background-color: var(--background-dark);
            color: var(--text-color);
            padding: 0.8rem;
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        select:hover, select:focus {
            border-color: #ff1493;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 105, 180, 0.3);
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 250px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button:hover {
            background-color: #ff1493;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        ul {
            background-color: var(--background-light);
            padding: 2rem;
            border-radius: 15px;
            border: 2px solid var(--primary-color);
            list-style-type: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        li {
            margin: 1rem 0;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 105, 180, 0.3);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        li:last-child {
            border-bottom: none;
        }

        li:hover {
            background-color: rgba(255, 105, 180, 0.1);
            transform: translateX(10px);
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            form {
                padding: 1.5rem;
            }

            .form-group {
                width: 100%;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Gérer les Responsables des Activités</h2>

    <form method="POST" action="">
        <div class="form-group">
            <label for="activity_id">Activité :</label>
            <select name="activity_id" required>
                <?php foreach ($activities as $activity) { ?>
                    <option value="<?= $activity['id_C'] ?>"><?= htmlspecialchars($activity['Nom_M']) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="responsable_id">Responsable :</label>
            <select name="responsable_id" required>
                <?php foreach ($users as $user) { ?>
                    <option value="<?= $user['id_U'] ?>"><?= htmlspecialchars($user['Prenom_C'] . " " . $user['Nom_C']) ?></option>
                <?php } ?>
            </select>
        </div>

        <button type="submit">Affecter / Modifier</button>
    </form>

    <button onclick="window.location.href='../dashboard_A.php'" style="margin: 20px auto; display: block;">Retour au tableau de bord</button>

    <h3>Responsables existants</h3>
    <ul>
        <?php
        $responsables = $conn->query("
            SELECT c.Nom_C, c.Prenom_C, cr.Nom_M 
            FROM responsable r
            INNER JOIN compte c ON r.id_U = c.id_U
            INNER JOIN créneau cr ON r.id_C = cr.id_C
        ")->fetchAll();

        foreach ($responsables as $responsable) {
            echo "<li>" . htmlspecialchars($responsable['Prenom_C'] . " " . $responsable['Nom_C']) . " - Activité : " . htmlspecialchars($responsable['Nom_M']) . "</li>";
        }
        ?>
    </ul>
</div>
</body>
</html>
