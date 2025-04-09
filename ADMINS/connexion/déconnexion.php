<?php
session_start();

// Détruire toutes les sessions
session_unset();
session_destroy();

// Délai de 2 secondes avant la redirection vers la page de connexion
header("Refresh: 2; url=connexion.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion en cours</title>
    <style>
        /* Police d'écriture et mise en page générale du corps */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #1a1a1a;
            transition: all 0.3s ease;
        }
        /* Style du conteneur du message */
        .message {
            text-align: center;
            padding: 2rem;
            background-color: #2d2d2d;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-in;
        }
        /* Style du texte dans le message */
        .message p {
            font-size: 1.3em;
            color: #ffffff;
            margin: 0;
            line-height: 1.6;
        }
        /* Style de l'animation de chargement */
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #333;
            border-top: 5px solid #4CAF50;
            border-radius: 50%;
            margin: 20px auto;
            animation: spin 1s linear infinite;
        }
        /* Animation de rotation pour le loader */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* Animation d'apparition en fondu */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="message">
        <div class="loader"></div>
        <p>Déconnexion en cours...</p>
        <p style="font-size: 0.9em; color: #888; margin-top: 10px;">Vous allez être redirigé vers la page de connexion</p>
    </div>
</body>
</html>
