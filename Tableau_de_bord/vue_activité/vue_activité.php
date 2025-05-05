<?php
// Démarrage de la session pour gérer les informations de l'utilisateur
session_start();
include '../../db.php';

// Gestion de l'ajout de crédits
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_credits']) && isset($_SESSION['id_U'])) {
    $credits_to_add = intval($_POST['credits_amount']);
    $id_U = $_SESSION['id_U'];
    
    if ($credits_to_add > 0) {
        $updateStmt = $conn->prepare("UPDATE compte SET Credits_C = Credits_C + ? WHERE id_U = ?");
        $updateStmt->execute([$credits_to_add, $id_U]);
        $success_message = "Crédits ajoutés avec succès !";
    }
}

// Gestion de la désinscription à une activité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['desinscription']) && isset($_SESSION['id_U'])) {
    $id_activite = intval($_POST['id_C']);
    $id_U = $_SESSION['id_U'];

    // Supprimer l'inscription
    $deleteStmt = $conn->prepare("DELETE FROM participation WHERE id_U = ? AND id_C = ?");
    $deleteStmt->execute([$id_U, $id_activite]);

    // Rembourser les crédits
    $updateStmt = $conn->prepare("UPDATE compte SET Credits_C = Credits_C + 5 WHERE id_U = ?");
    $updateStmt->execute([$id_U]);

    $success_message = "Vous avez été désinscrit de l'activité et vos crédits ont été remboursés.";
}


// Gestion de l'inscription à une activité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_C'], $_SESSION['id_U']) && !isset($_POST['desinscription'])) {
    $id_activite = intval($_POST['id_C']);
    $id_U = $_SESSION['id_U'];

    // Vérifier les crédits disponibles
    $stmt = $conn->prepare("SELECT Credits_C FROM compte WHERE id_U = ?");
    $stmt->execute([$id_U]);
    $user = $stmt->fetch();

    if ($user && $user['Credits_C'] >= 5) { // Vérifie si l'utilisateur a assez de crédits
        // Vérifier si l'utilisateur est déjà inscrit
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM participation WHERE id_U = ? AND id_C = ?");
        $checkStmt->execute([$id_U, $id_activite]);
        $isAlreadyRegistered = $checkStmt->fetchColumn() > 0;

        if (!$isAlreadyRegistered) {
            // Inscrire l'utilisateur
            $insertStmt = $conn->prepare("INSERT INTO participation (id_U, id_C) VALUES (?, ?)");
            $insertStmt->execute([$id_U, $id_activite]);

            // Déduire les crédits (5 crédits par activité)
            $updateStmt = $conn->prepare("UPDATE compte SET Credits_C = Credits_C - 5 WHERE id_U = ?");
            $updateStmt->execute([$id_U]);

            $success_message = "Vous êtes inscrit à l'activité avec succès !";
        } else {
            $error_message = "Vous êtes déjà inscrit à cette activité.";
        }
    } else {
        $error_message = "Crédits insuffisants pour cette inscription.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Utilisateur</title>
    <style>
        :root {
            --primary-color: #ff69b4;
            --secondary-color: #2c2c2c;
            --accent-color: #ff1493;
            --text-color: #ffffff;
            --card-bg: #3c3c3c;
            --hover-bg: #4c4c4c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--secondary-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h3 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin: 1.5rem 0;
            font-size: 1.8rem;
        }

        .btn-retour {
            background-color: var(--primary-color);
            color: var(--text-color);
            padding: 0.8rem 1.5rem;
            text-decoration: none;
            border-radius: 25px;
            display: inline-block;
            margin: 1rem 0;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .btn-retour:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .credits {
            background-color: var(--card-bg);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            border: 2px solid var(--primary-color);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .credit-form {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 10px;
            border: 2px solid var(--primary-color);
            margin: 2rem 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .credit-form h4 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
        }

        .credit-form input[type="number"] {
            padding: 0.8rem;
            border-radius: 25px;
            border: 2px solid var(--primary-color);
            background-color: var(--secondary-color);
            color: var(--text-color);
            margin-right: 1rem;
            width: 200px;
            outline: none;
            transition: all 0.3s ease;
        }

        .credit-form input[type="number"]:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 8px rgba(255,105,180,0.5);
        }

        .credit-form button {
            background-color: var(--primary-color);
            color: var(--text-color);
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .credit-form button:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.7);
            z-index: 999;
        }
        .btn-danger {
            background-color: #ff4d4d;
            margin: 0.5rem;
        }
        .btn-cancel {
            background-color: #666;
            margin: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 2rem;
            background-color: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 1rem;
            text-align: left;
        }

        th {
            background-color: var(--primary-color);
            color: var(--text-color);
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: rgba(255,255,255,0.05);
        }

        tr:hover {
            background-color: var(--hover-bg);
        }

        .btn-choisir {
            background-color: var(--primary-color);
            color: var(--text-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-choisir:hover {
            background-color: var(--accent-color);
            transform: scale(1.05);
        }

        .inscription-message {
            color: var(--primary-color);
            margin: 1.5rem 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        #liste-activites {
            list-style: none;
            padding: 0;
        }

        #liste-activites li {
            background-color: var(--card-bg);
            margin: 0.8rem 0;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        #liste-activites li:hover {
            transform: translateX(10px);
            background-color: var(--hover-bg);
        }

        .not-enough-credits {
            color: #ff4d4d;
            font-weight: 600;
        }

        .role-user {
            color: var(--primary-color);
            font-style: italic;
            margin-bottom: 1rem;
        }

        .expired-message {
            color: #ff4444;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            .credit-form input[type="number"] {
                width: 100%;
                margin-bottom: 1rem;
                margin-right: 0;
            }

            .credit-form button {
                width: 100%;
            }

            .btn-retour {
                width: 100%;
                text-align: center;
            }
        }

        .warning-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 10px;
            border: 2px solid #ff4d4d;
            z-index: 1000;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
    </style>
</head>
<!-- Corps de la page -->
<body>
    <!-- Conteneur principal -->
    <div class="container">
        <?php
        if (isset($_SESSION['id_U'])) {
            $id_U = $_SESSION['id_U'];

            // Récupération des informations de l'utilisateur
            $userStmt = $conn->prepare("SELECT Prenom_C, Nom_C, Role_C, Credits_C FROM compte WHERE id_U = ?");
            $userStmt->execute([$id_U]);
            $user = $userStmt->fetch();

            if ($user) {
                // Affichage du bouton retour et des informations de l'utilisateur
                echo "<a href='../vue_activité/Page_Accueil_U.php' class='btn btn-retour'>Retour</a>";
                //bouton de deconnexion
                echo "<a href='../../ADMINS/connexion/déconnexion.php' class='btn btn-danger' style='background-color: #ff4d4d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Déconnexion</a>";
                // Affichage des informations de l'utilisateur
                echo "<h3>Bienvenue, " . htmlspecialchars($user['Prenom_C']) . " " . htmlspecialchars($user['Nom_C']) . " !</h3>";
                if (!empty($user['Role_C'])) {
                    echo "<p class='role-user'>Rôle : " . htmlspecialchars($user['Role_C']) . "</p>";
                }
                echo "<div class='credits'>Crédits disponibles : " . htmlspecialchars($user['Credits_C']) . "</div>";

                // Formulaire d'ajout de crédits
                echo "<div class='credit-form'>
                    <h4>Ajouter des crédits</h4>
                    <form method='POST'>
                        <input type='number' name='credits_amount' min='1' placeholder='Nombre de crédits'>
                        <button type='submit' name='add_credits'>Ajouter des crédits</button>
                    </form>
                </div>";

                // Affichage des messages de succès ou d'erreur
                if (isset($success_message)) {
                    echo "<p style='color: #ff69b4;'>$success_message</p>";
                }
                if (isset($error_message)) {
                    echo "<p style='color: #ff4d4d;'>$error_message</p>";
                }

                // Récupération des activités inscrites
                $stmt = $conn->prepare("SELECT c.id_C, c.date_deb, c.Nom_M, c.Heure_deb FROM participation p JOIN créneau c ON p.id_C = c.id_C WHERE p.id_U = ?");
                $stmt->execute([$id_U]);
                $inscriptions = $stmt->fetchAll();

                // Affichage des inscriptions de l'utilisateur
                if ($inscriptions) {
                    echo "<div class='inscription-message'>Historique des précédentes inscriptions :</div>";
                    echo "<ul id='liste-activites'>";
                    foreach ($inscriptions as $inscription) {
                        echo "<li>" . htmlspecialchars($inscription['Nom_M']) . " (Début : " . htmlspecialchars($inscription['date_deb']) . " à " . htmlspecialchars($inscription['Heure_deb']) . ")";
                        echo "<form method='POST' style='display: inline;'>";
                        echo "<input type='hidden' name='id_C' value='" . $inscription['id_C'] . "'>";
                        echo "<button type='button' onclick='showWarning(this.form)' class='btn-danger'>Se désinscrire</button>";
                        echo "</form></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<div class='inscription-message'>Vous n'êtes inscrit à aucune activité pour le moment.</div>";
                }
            }
        } else {
            echo "<div class='inscription-message'>Veuillez vous connecter pour accéder à votre tableau de bord.</div>";
        }

        // Récupération de toutes les activités disponibles
        $activities = $conn->query("SELECT * FROM créneau")->fetchAll();
        ?>

        <!-- Modal de confirmation pour la désinscription -->
        <div class="modal-overlay" id="modalOverlay" style="background-color: rgba(0, 0, 0, 0.8);"></div>
        <div class="warning-modal" id="warningModal" style="background-color: #1a1a1a; color: #ff69b4; border: 2px solid #ff69b4; box-shadow: 0 0 10px rgba(255, 105, 180, 0.3);">
            <h4 style="color: #ff69b4;">⚠️ ATTENTION ⚠️</h4>
            <p style="color: #f0f0f0;">Vous ne pourrez vous désinscrire qu'une seule fois de cette activité.</p>
            <p style="color: #f0f0f0;">Êtes-vous sûr de vouloir continuer ?</p>
            <button class="btn-danger" onclick="confirmDesinscription()" style="background-color: #ff1493; color: white; border: none; padding: 8px 16px; margin: 5px; cursor: pointer;">Confirmer la désinscription</button>
            <button class="btn-cancel" onclick="hideWarning()" style="background-color: #4a4a4a; color: white; border: 1px solid #ff69b4; padding: 8px 16px; margin: 5px; cursor: pointer;">Annuler</button>
        </div>

        <!-- Titre de la section des activités -->
        <h3>Liste des Activités Disponibles</h3>

        <!-- Tableau des activités disponibles -->
        <table>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Coût</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($activities as $activity) { 
                // Vérification si l'activité est expirée
                $date_now = date('Y-m-d');
                $activity_date = $activity['date_deb'];
                $is_expired = (strtotime($activity_date) < strtotime($date_now));
                ?>
                <tr>
                    <td><?= htmlspecialchars($activity['Nom_M']) ?></td>
                    <td><?= htmlspecialchars($activity['Desc_M']) ?></td>
                    <td><?= htmlspecialchars($activity['date_deb']) ?></td>
                    <td><?= htmlspecialchars($activity['Heure_deb']) ?></td>
                    <td><?= htmlspecialchars($activity['credit_requis']) ?></td>
                    <td>
                        <?php if ($is_expired): ?>
                            <!-- Message pour les activités expirées -->
                            <div class="expired-message">
                                Activité expirée
                            </div>
                        <?php elseif (isset($user['Credits_C']) && $user['Credits_C'] >= 5): ?>
                            <!-- Formulaire d'inscription pour les utilisateurs ayant assez de crédits -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id_C" value="<?= $activity['id_C'] ?>">
                                <button type="submit" class="btn-choisir">Choisir</button>
                            </form>
                        <?php else: ?>
                            <!-- Message pour les utilisateurs n'ayant pas assez de crédits -->
                            <div class="not-enough-credits">Crédits insuffisants</div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Script JavaScript pour gérer la modal de désinscription -->
    <script>
        // Variable pour stocker le formulaire actuel
        let currentForm = null;

        // Fonction pour afficher la modal d'avertissement
        function showWarning(form) {
            currentForm = form;
            document.getElementById('modalOverlay').style.display = 'block';
            document.getElementById('warningModal').style.display = 'block';
        }

        // Fonction pour cacher la modal d'avertissement
        function hideWarning() {
            document.getElementById('modalOverlay').style.display = 'none';
            document.getElementById('warningModal').style.display = 'none';
            currentForm = null;
        }

        // Fonction pour confirmer la désinscription
        function confirmDesinscription() {
            if (currentForm) {
                // Ajout d'un champ caché pour la désinscription
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'desinscription';
                input.value = '1';
                currentForm.appendChild(input);
                currentForm.onsubmit = null;
                currentForm.submit();
            }
            hideWarning();
        }
    </script>
</body>
</html>
