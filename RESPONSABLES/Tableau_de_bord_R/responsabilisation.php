<?php
session_start();
require_once '../config/database.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

// Vérifier si l'utilisateur est connecté et est un responsable
if (!isset($_SESSION['id_U']) || $_SESSION['role'] !== 'responsable') {
    header('Location: ../ADMINS/connexion/connexion.php');
    exit();
}

$id_responsable = $_SESSION['id_U'];
$message = '';

// Récupérer les activités dont l'utilisateur est responsable
$stmt = $conn->prepare("
    SELECT r.id_R, c.id_C, c.Nom_M, c.Desc_M, c.date_deb, c.Heure_deb, c.date_fin, c.Heure_Fin, c.credit_requis, 
           r.date_attribution, COUNT(p.id_P) as nombre_participants
    FROM responsable r
    INNER JOIN créneau c ON r.id_C = c.id_C
    LEFT JOIN participation p ON c.id_C = p.id_C
    WHERE r.id_U = ?
    GROUP BY r.id_R, c.id_C
    ORDER BY c.date_deb ASC
");
$stmt->execute([$id_responsable]);
$activites = $stmt->fetchAll();

// Traitement de la demande de modification d'affectation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demande_modification'])) {
    $id_activite = $_POST['id_activite'];
    $motif = $_POST['motif'];
    
    // Insérer la demande dans la table des notifications
    $stmt = $conn->prepare("
        INSERT INTO notifications (id_U, message, type_notification) 
        VALUES (?, ?, 'demande_modification')
    ");
    
    $message_notification = "Le responsable " . $_SESSION['prenom'] . " " . $_SESSION['nom'] . 
                           " demande à être remplacé pour l'activité #" . $id_activite . 
                           ". Motif: " . $motif;
    
    $stmt->execute([$id_responsable, $message_notification]);
    
    $message = "Votre demande de modification d'affectation a été envoyée à l'administration.";
}

// Récupérer les statistiques des activités
$stmt = $conn->prepare("
    SELECT c.id_C, 
           COUNT(DISTINCT p.id_U) as total_participants,
           SUM(CASE WHEN p.statut = 'présent' THEN 1 ELSE 0 END) as participants_presents
    FROM responsable r
    INNER JOIN créneau c ON r.id_C = c.id_C
    LEFT JOIN participation p ON c.id_C = p.id_C
    WHERE r.id_U = ?
    GROUP BY c.id_C
");
$stmt->execute([$id_responsable]);
$statistiques = [];
while ($row = $stmt->fetch()) {
    $statistiques[$row['id_C']] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Activités - Responsable</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            --success: #69f0ae;
            --warning: #ffeb3b;
            --card-shadow: 0 4px 6px rgba(255, 64, 129, 0.2);
            --border-radius: 12px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1, h2, h3 {
            color: var(--accent);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            background-color: var(--accent);
            color: var(--text-primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: bold;
            transition: background-color var(--transition-speed);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background-color: var(--accent-hover);
        }

        .btn-danger {
            background-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: var(--danger-hover);
        }

        .activities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .activity-card {
            background-color: var(--bg-secondary);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            transition: transform var(--transition-speed);
            position: relative;
            overflow: hidden;
        }

        .activity-card:hover {
            transform: translateY(-5px);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .activity-title {
            font-size: 1.4rem;
            margin: 0;
            color: var(--accent);
        }

        .activity-date {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .activity-description {
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
        }

        .activity-stats {
            display: flex;
            justify-content: space-between;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--accent);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .activity-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--bg-secondary);
            border-radius: var(--border-radius);
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--card-shadow);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            margin: 0;
            color: var(--accent);
        }

        .close-modal {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        .form-group textarea, .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #444;
            border-radius: var(--border-radius);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            resize: vertical;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: rgba(105, 240, 174, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .badge-warning {
            background-color: var(--warning);
            color: #333;
        }

        .badge-success {
            background-color: var(--success);
            color: #333;
        }

        @media (max-width: 768px) {
            .activities-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Mes Activités</h1>
            <div class="action-buttons">
                <a href="../RESPONSABLES/dashboard_R.php" class="btn">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="../ADMINS/connexion/déconnexion.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($activites)): ?>
            <div style="text-align: center; margin-top: 3rem;">
                <i class="fas fa-calendar-times" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                <h2>Aucune activité assignée</h2>
                <p>Vous n'avez pas encore d'activités dont vous êtes responsable.</p>
            </div>
        <?php else: ?>
            <div class="activities-grid">
                <?php foreach ($activites as $activite): ?>
                    <?php 
                    $date_debut = new DateTime($activite['date_deb'] . ' ' . $activite['Heure_deb']);
                    $date_fin = new DateTime($activite['date_fin'] . ' ' . $activite['Heure_Fin']);
                    $aujourd_hui = new DateTime();
                    
                    $statut = '';
                    $statut_class = '';
                    
                    if ($date_debut > $aujourd_hui) {
                        $statut = 'À venir';
                        $statut_class = 'badge-warning';
                    } elseif ($date_fin < $aujourd_hui) {
                        $statut = 'Terminée';
                        $statut_class = 'badge-success';
                    } else {
                        $statut = 'En cours';
                        $statut_class = 'badge-warning';
                    }
                    ?>
                    <div class="activity-card">
                        <div class="activity-header">
                            <h3 class="activity-title"><?php echo htmlspecialchars($activite['Nom_M']); ?></h3>
                            <span class="badge <?php echo $statut_class; ?>"><?php echo $statut; ?></span>
                        </div>
                        <div class="activity-date">
                            <i class="far fa-calendar-alt"></i> 
                            <?php echo date('d/m/Y', strtotime($activite['date_deb'])); ?> 
                            de <?php echo date('H:i', strtotime($activite['Heure_deb'])); ?> 
                            à <?php echo date('H:i', strtotime($activite['Heure_Fin'])); ?>
                        </div>
                        <p class="activity-description"><?php echo htmlspecialchars($activite['Desc_M']); ?></p>
                        
                        <div class="activity-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo isset($statistiques[$activite['id_C']]) ? $statistiques[$activite['id_C']]['total_participants'] : 0; ?></div>
                                <div class="stat-label">Participants</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $activite['credit_requis']; ?></div>
                                <div class="stat-label">Crédits requis</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo isset($statistiques[$activite['id_C']]) ? $statistiques[$activite['id_C']]['participants_presents'] : 0; ?></div>
                                <div class="stat-label">Présents</div>
                            </div>
                        </div>
                        <div class="activity-actions">
                            <button class="btn" onclick="window.location.href='liste_participants.php?id_activite=<?php echo $activite['id_C']; ?>'">
                                <i class="fas fa-users"></i> Voir participants
                            </button>
                            <button class="btn btn-danger" onclick="openModal('<?php echo $activite['id_C']; ?>', '<?php echo htmlspecialchars($activite['Nom_M'], ENT_QUOTES); ?>')">
                                <i class="fas fa-exchange-alt"></i> Demander remplacement
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal pour demander un remplacement -->
    <div id="remplacementModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Demande de remplacement</h3>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>
            <form method="post" action="">
                <input type="hidden" id="id_activite" name="id_activite" value="">
                <div class="form-group">
                    <label for="activite_nom">Activité:</label>
                    <input type="text" id="activite_nom" readonly>
                </div>
                <div class="form-group">
                    <label for="motif">Motif de la demande:</label>
                    <textarea id="motif" name="motif" rows="4" required placeholder="Veuillez expliquer pourquoi vous souhaitez être remplacé pour cette activité..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="closeModal()">Annuler</button>
                    <button type="submit" name="demande_modification" class="btn">Envoyer la demande</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, nom) {
            document.getElementById('id_activite').value = id;
            document.getElementById('activite_nom').value = nom;
            document.getElementById('remplacementModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('remplacementModal').style.display = 'none';
        }
        
        // Fermer la modal si on clique en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('remplacementModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
