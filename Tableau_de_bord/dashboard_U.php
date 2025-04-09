<?php
// Démarrage de la session et connexion à la base de données
session_start();
include '../db.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['id_U']) || !isset($_SESSION['role'])) {
    header("Location: ../connexion/connexion.php");
    exit();
}

// Redirection si l'utilisateur n'est pas administrateur
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../Tableau_de_bord/vue_activité/vue_activité.php");
    exit();
}

// Récupération de toutes les activités
$activities = $conn->query("SELECT * FROM créneau")->fetchAll();

// Calcul des statistiques globales
$totalParticipants = $conn->query("SELECT COUNT(DISTINCT id_U) FROM participation")->fetchColumn();
$totalActivities = $conn->query("SELECT COUNT(*) FROM créneau")->fetchColumn();
$avgParticipantsPerActivity = $conn->query("SELECT AVG(participant_count) FROM (SELECT COUNT(*) as participant_count FROM participation GROUP BY id_C) as counts")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #b3b3b3;
            --accent: #4CAF50;
            --accent-hover: #45a049;
            --danger: #ff4444;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            --gold: #ffd700;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-primary);
            margin: 0;
            padding: 20px;
            color: var(--text-primary);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h2, h3 {
            text-align: center;
            color: var(--text-primary);
            margin-bottom: 30px;
            font-weight: 600;
        }

        h2 span {
            color: var(--gold);
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background-color: var(--bg-secondary);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(76, 175, 80, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 3em;
            margin-bottom: 1.5rem;
            color: var(--accent);
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin: 0.5rem 0;
            color: var(--accent);
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1.2em;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin: 2.5rem 0;
        }

        .quick-action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            padding: 1.2rem;
            background-color: var(--accent);
            border-radius: 15px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .quick-action-btn:hover {
            background-color: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: var(--card-shadow);
        }

        .activity-card {
            background-color: var(--bg-secondary);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .activity-card h4 {
            color: var(--accent);
            margin-top: 0;
        }

        .activity-description {
            color: var(--text-secondary);
            font-style: italic;
            margin-bottom: 1.5rem;
        }

        .activity-card ul {
            list-style: none;
            padding: 0;
        }

        .activity-card li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .activity-card li:last-child {
            border-bottom: none;
        }

        .no-participants {
            color: var(--text-secondary);
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Tableau de Bord <span>Administrateur</span></h2>
    
    <!-- Affichage des statistiques principales -->
    <div class="stats-container">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div class="stat-number"><?= $totalParticipants ?></div>
            <div class="stat-label">Participants Totaux</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-alt"></i>
            <div class="stat-number"><?= $totalActivities ?></div>
            <div class="stat-label">Activités</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-chart-line"></i>
            <div class="stat-number"><?= round($avgParticipantsPerActivity, 1) ?></div>
            <div class="stat-label">Moyenne Participants/Activité</div>
        </div>
    </div>

    <!-- Menu d'actions rapides -->
    <div class="quick-actions">
        <a href="../ADMINS/gestion_activité/gestion_activité.php" class="quick-action-btn">
            <i class="fas fa-cog"></i> Gérer les Activités
        </a>
        <a href="../ADMINS/ges" class="quick-action-btn">
            <i class="fas fa-tasks"></i> Choix des activité
        </a>
        <a href="../Tableau_de_bord/gestion_comptes/gestion_comptes.php" class="quick-action-btn">
            <i class="fas fa-cog"></i> Création des comptes et attribution des Roles
        </a>
        <a href="../Tableau_de_bord/création_rôles/création_rôles.php" class="quick-action-btn">
            <i class="fas fa-cog"></i> Création des Roles
        </a>
        <a href="javascript:history.back()" class="quick-action-btn">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <a href="../ADMINS/connexion/déconnexion.php" class="quick-action-btn" style="background-color: var(--danger);">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>

    <h3>Liste des Participants par Activité</h3>

    <!-- Affichage des activités et leurs participants -->
    <?php foreach ($activities as $activity) { 
        // Compte le nombre de participants pour cette activité
        $stmt = $conn->prepare("
            SELECT COUNT(*) as participant_count 
            FROM participation 
            WHERE id_C = ?
        ");
        //MISE A JOUR : J'ai mis les propriété de Créneau a la place de Activité car les information ne vont pas dans la table "Activité" Bizarre ???
        $stmt->execute([$activity['id_C']]);
        $participantCount = $stmt->fetch()['participant_count'];
    ?>
        <div class="activity-card">
        <!--MISE A JOUR : J'ai mis les propriété de Créneau a la place de Activité car les information ne vont pas dans la table "Activité" Bizarre ???-->
            <h4><?= htmlspecialchars($activity['Nom_M']) ?></h4>
            <!--MISE A JOUR : J'ai mis les propriété de Créneau a la place de Activité car les information ne vont pas dans la table "Activité" Bizarre ???-->
            <p class="activity-description"><?= htmlspecialchars($activity['Desc_M']) ?></p>
            <div style="text-align: right; margin-bottom: 1rem;">
                <span style="background: rgba(76, 175, 80, 0.2); padding: 0.5rem 1rem; border-radius: 20px;">
                    <i class="fas fa-user"></i> <?= $participantCount ?> participants
                </span>
            </div>
            
            <?php
            // Récupère la liste des participants pour cette activité
            $stmt = $conn->prepare("
                SELECT compte.Nom_C, compte.Prenom_C 
                FROM compte 
                INNER JOIN participation ON compte.id_U = participation.id_U 
                WHERE participation.id_C = ?
            ");
            //MISE A JOUR : J'ai mis les propriété de Créneau a la place de Activité car les information ne vont pas dans la table "Activité" Bizarre ???
            $stmt->execute([$activity['id_C']]);
            $participants = $stmt->fetchAll();
            ?>

            <?php if (count($participants) > 0) { ?>
                <ul>
                    <?php foreach ($participants as $participant) { ?>
                        <li><i class="fas fa-user-circle"></i> <?= htmlspecialchars($participant['Prenom_C']) . " " . htmlspecialchars($participant['Nom_C']) ?></li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p class="no-participants"><i class="fas fa-info-circle"></i> Aucun participant pour cette activité.</p>
            <?php } ?>
        </div>
    <?php } ?>
</div>
</body>
</html>
