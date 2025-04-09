<?php
// Démarrage de la session pour gérer les données utilisateur
session_start();

// Connexion à la base de données
include '../../db.php';

// Redirection vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['id_U'])) {
    header("Location: ../connexion/connexion.php");
    exit();
}

// Récupération de l'ID utilisateur depuis la session
$id_U = $_SESSION['id_U'];

// Récupération de la liste des activités disponibles
$activities = $conn->query("SELECT DISTINCT id_C, Nom_M FROM créneau")->fetchAll();

// Récupération des créneaux pour une activité sélectionnée
$creneaux = [];
if (!empty($_POST['id_M'])) {
    $id_M = $_POST['id_M'];
    $creneaux = $conn->prepare("SELECT * FROM créneau WHERE id_C = ?");
    $creneaux->execute([$id_M]);
    $creneaux = $creneaux->fetchAll();
}

// Variables pour les messages de confirmation
$confirmationMessage = '';
$messageType = '';

// Traitement de l'inscription à une activité
if (isset($_POST['confirm']) && !empty($_POST['id_M'])) {
    $id_M = $_POST['id_M'];

    // Vérification si l'utilisateur est déjà inscrit
    $checkParticipation = $conn->prepare("SELECT COUNT(*) FROM participation WHERE id_U = ? AND id_C = ?");
    $checkParticipation->execute([$id_U, $id_M]);
    $isAlreadyRegistered = $checkParticipation->fetchColumn() > 0;

    if ($isAlreadyRegistered) {
        // Message si déjà inscrit
        $confirmationMessage = "Vous êtes déjà inscrit à cette activité !";
        $messageType = "error";
    } else {
        // Vérification de l'existence du créneau
        $checkCreneau = $conn->prepare("SELECT COUNT(*) FROM créneau WHERE id_C = ?");
        $checkCreneau->execute([$id_M]);
        $creneauExists = $checkCreneau->fetchColumn() > 0;

        if ($creneauExists) {
            // Récupération des informations du créneau
            $stmt = $conn->prepare("SELECT Nom_M, date_deb, Heure_deb, date_fin, Heure_Fin FROM créneau WHERE id_C = ?");
            $stmt->execute([$id_M]);
            $creneau = $stmt->fetch();

            // Enregistrement de la participation
            $insertParticipation = $conn->prepare("INSERT INTO participation (id_U, id_C) VALUES (?, ?)");
            $insertParticipation->execute([$id_U, $id_M]);

            // Message de confirmation de l'inscription
            $confirmationMessage = "Vous avez sélectionné l'activité : " . $creneau['Nom_M'] . 
                                   ", prévue du " . $creneau['date_deb'] . " à " . $creneau['Heure_deb'] . 
                                   " au " . $creneau['date_fin'] . " à " . $creneau['Heure_Fin'] . "." .
                                   " Votre participation a été enregistrée.";
            $messageType = "success" ;
            // Redirection automatique avec délai
            echo "<script>
                setTimeout(function() {
                    window.location.href = '../vue_activité/vue_activité.php';
                    }, 3000);
                </script>";

        } else {
            // Message si le créneau n'existe pas
            $confirmationMessage = "Erreur: Le créneau sélectionné n'existe pas.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir une Activité et un Créneau</title>
    <style>
        :root {
    --bg-primary: #1a1a1a;
    --bg-secondary: #2d2d2d;
    --text-primary: #ffffff;
    --text-secondary: #b3b3b3;
    --accent: #FF69B4;
    --accent-hover: #FF1493;
    --danger: #ff4d4d;
    --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

body {
    background: linear-gradient(135deg, #2d2d2d, #1a1a1a);
    color: var(--text-primary);
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 20px;
    min-height: 100vh;
}

h2 {
    color: var(--accent);
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.2em;
    text-shadow: 0 0 10px rgba(255, 105, 180, 0.3);
}

.container {
    max-width: 800px;
    margin: 40px auto;
    padding: 30px;
    background: rgba(45, 45, 45, 0.9);
    border-radius: 20px;
    box-shadow: var(--card-shadow);
    backdrop-filter: blur(10px);
    border: 2px solid var(--accent);
}

.back-button {
    display: inline-block;
    margin-bottom: 30px;
    padding: 12px 25px;
    background: linear-gradient(45deg, var(--accent), var(--accent-hover));
    color: var(--text-primary);
    text-decoration: none;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(255, 105, 180, 0.2);
}

.back-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 105, 180, 0.3);
}

form {
    margin: 25px 0;
    padding: 20px;
    background: rgba(61, 61, 61, 0.5);
    border-radius: 15px;
    transition: all 0.3s ease;
    border: 1px solid var(--accent);
}

form:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow);
}

label {
    display: block;
    margin-bottom: 12px;
    color: var(--accent);
    font-size: 1.1em;
    font-weight: 500;
}

select {
    width: 100%;
    padding: 15px;
    margin-bottom: 25px;
    background: rgba(30, 30, 30, 0.9);
    color: var(--text-primary);
    border: 2px solid var(--accent);
    border-radius: 10px;
    font-size: 1em;
}

select:focus {
    outline: none;
    border-color: var(--accent-hover);
    box-shadow: 0 0 10px rgba(255, 105, 180, 0.3);
    transform: scale(1.01);
}

button {
    padding: 15px 30px;
    background: linear-gradient(45deg, var(--accent), var(--accent-hover));
    color: var(--text-primary);
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.1em;
    font-weight: 500;
    width: 100%;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 105, 180, 0.3);
}

.confirmation {
    margin-top: 30px;
    padding: 20px;
    background: rgba(61, 61, 61, 0.7);
    border-radius: 15px;
    border-left: 4px solid;
    animation: slideIn 0.5s ease-out;
}

.confirmation.success {
    border-color: var(--accent);
    background: rgba(255, 105, 180, 0.1);
}

.confirmation.error {
    border-color: var(--danger);
    background: rgba(255, 77, 77, 0.1);
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 600px) {
    .container {
        padding: 20px;
    }

    h2 {
        font-size: 1.8em;
    }

    button {
        padding: 12px 25px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Choisir une Activité et un Créneau</h2>
        
        <!-- Bouton de retour -->
        <a href="javascript:history.back()" class="back-button">← Retour au Tableau de Bord</a>

        <!-- Formulaire de sélection d'activité -->
        <form method="post">
            <label for="id_M">Sélectionnez une activité :</label>
            <select name="id_M" id="id_M" onchange="this.form.submit()" required>
                <option value="">-- Choisissez votre activité --</option>
                <?php foreach ($activities as $activity) { ?>
                    <option value="<?= $activity['id_C'] ?>" <?= isset($id_M) && $id_M == $activity['id_C'] ? 'selected' : '' ?>>
                        <?= $activity['Nom_M'] ?>
                    </option>
                <?php } ?>
            </select>
        </form>

        <!-- Formulaire de sélection de créneau -->
        <?php if (isset($creneaux) && count($creneaux) > 0) { ?>
            <form method="post">
                <input type="hidden" name="id_M" value="<?= $id_M ?>">
                <label for="creneau">Sélectionnez un créneau :</label>
                <select name="id_C" id="creneau" required>
                    <option value="">-- Choisissez votre créneau --</option>
                    <?php foreach ($creneaux as $creneau) { ?>
                        <option value="<?= $creneau['id_C'] ?>">
                            Du <?= $creneau['date_deb'] ?> à <?= $creneau['Heure_deb'] ?> au <?= $creneau['date_fin'] ?> à <?= $creneau['Heure_Fin'] ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="submit" name="confirm">Confirmer mon inscription</button>
            </form>
        <?php } ?>

        <!-- Affichage des messages de confirmation -->
        <?php if (!empty($confirmationMessage)) { ?>
            <div class="confirmation <?= $messageType ?>">
                <h3><?= $messageType === 'success' ? 'Confirmation' : 'Attention' ?></h3>
                <p><?= $confirmationMessage ?></p>
                <?php if ($messageType === 'success') { ?>
                <p>Vous allez être redirigé vers la page des activités dans 3 secondes...</p>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>