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
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';

// Redirection après 3 secondes
$redirect_url = ($role === 'admin') ? "../../ADMINS/dashboard_A.php" : "";
$redirect_url = ($role === 'participant') ? "../dashboard_U.php" : "";
header("refresh:2;url=" . $redirect_url);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirection en cours...</title>
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
            overflow: hidden;
        }
        .container {
            text-align: center;
            position: relative;
        }
        .welcome-text {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .loading-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: relative;
            margin: 20px auto;
        }
        /* Animation pour Admin */
        .admin-animation .loading-circle {
            border: 4px solid #FFD700;
            border-top: 4px solid transparent;
            animation: spin 1s linear infinite, glow 2s ease-in-out infinite;
        }
        .admin-animation .welcome-text {
            background: linear-gradient(to right, #FFD700, #FFA500, #FFD700);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: goldShine 3s linear infinite;
        }
        /* Animation pour Utilisateur */
        .user-animation .loading-circle {
            border: 4px solid #ff1493;
            border-top: 4px solid transparent;
            animation: spin 1s linear infinite, userGlow 2s ease-in-out infinite;
        }
        .user-animation .welcome-text {
            color: #ff1493;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px #FFD700; }
            50% { box-shadow: 0 0 40px #FFD700; }
        }
        @keyframes userGlow {
            0%, 100% { box-shadow: 0 0 20px #ff1493; }
            50% { box-shadow: 0 0 40px #ff1493; }
        }
        @keyframes goldShine {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
    </style>
</head>
<body>
    <div class="container <?= $role === 'Admin' ? 'admin-animation' : 'user-animation' ?>">
        <div class="welcome-text">
            Bienvenue<?= !empty($prenom) && !empty($nom) ? ', ' . htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) : '' ?> !
        </div>
        <div class="loading-circle"></div>
    </div>
</body>
</html>
