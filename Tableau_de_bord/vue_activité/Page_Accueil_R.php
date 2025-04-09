<?php
// Démarrage de la session
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_U'])) {
    header("Location: connexion/connexion.php");
    exit();
}

// Initialisez les variables de session s'ils sont définis, sinon définissez-les à une valeur par défaut
$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : '';
$nom = isset($_SESSION['nom']) ? $_SESSION['nom'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Responsable';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil Responsable</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: #2d2d2d;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 800px;
        }
        h1 {
            font-size: 2.8em;
            color: #FF69B4;
            border-bottom: 2px solid #FF69B4;
            padding-bottom: 15px;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #3d3d3d;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .stat-number {
            font-size: 2em;
            color: #FF69B4;
            font-weight: bold;
        }
        .stat-label {
            color: #FFB6C1;
            margin-top: 5px;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            color: white;
            background-color: #FF69B4;
            border-radius: 50px;
            font-weight: bold;
            letter-spacing: 1px;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 80%;
            max-width: 300px;
            margin: 15px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .button:hover {
            background-color: #FF1493;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        .logout-button {
            background-color: #C71585;
        }
        .logout-button:hover {
            background-color: #DB7093;
        }
        .responsable-role {
            color: #FF69B4;
            font-weight: bold;
            font-size: 1.2em;
            padding: 5px 15px;
            background-color: rgba(255, 105, 180, 0.1);
            border-radius: 20px;
            display: inline-block;
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }
            h1 {
                font-size: 2.2em;
            }
            .button {
                width: 90%;
            }
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Espace Responsable<?= !empty($prenom) && !empty($nom) ? ' - ' . htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) : '' ?></h1>
        <p>Connecté en tant que <span class="responsable-role"><?= htmlspecialchars($role) ?></span></p>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number">12</div>
                <div class="stat-label">Activités en cours</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">45</div>
                <div class="stat-label">Participants actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">8</div>
                <div class="stat-label">Événements à venir</div>
            </div>
        </div>

        <a href="gestion_activites.php" class="button">Gérer les activités</a>
        <br>
        <a href="../dashboard.php" class="button">Tableau de bord complet</a>
        <br>
        <a href="rapports.php" class="button">Rapports et statistiques</a>
        <br>
        <a href="../../connexion/déconnexion.php" class="button logout-button">Déconnexion</a>
    </div>
</body>
</html>
