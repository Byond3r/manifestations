<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Activités</title>
    <style>
        /* Style pour le thème sombre */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #1a1a1a;
    color: #ffffff;
    margin: 0;
    padding: 20px;
}

/* Style pour les titres */
h2, h3 {
    color: #FF69B4;
    border-bottom: 2px solid #FF69B4;
    padding-bottom: 10px;
    margin-top: 30px;
}

/* Style pour les formulaires */
form {
    background-color: #2d2d2d;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Style pour les champs de saisie */
input[type="text"],
input[type="date"],
input[type="time"],
input[type="number"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    background-color: #3d3d3d;
    color: white;
    box-sizing: border-box;
}

/* Style pour les boutons */
button {
    background-color: #FF69B4;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s, transform 0.1s;
    margin: 5px;
}

/* Animation au survol des boutons */
button:hover {
    background-color: #FF1493;
    transform: scale(1.05);
}

/* Style pour le bouton supprimer */
button[name="delete"] {
    background-color: #FF1493;
    color: white;
}

button[name="delete"]:hover {
    background-color: #C71585;
}

/* Style pour le bouton éditer */
button[name="edit"] {
    background-color: #DDA0DD;
    color: white;
}

button[name="edit"]:hover {
    background-color: #DA70D6;
}

/* Style pour le tableau */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #2d2d2d;
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #3d3d3d;
}

th {
    background-color: #FF69B4;
    color: #ffffff;
    font-weight: bold;
}

/* Animation au survol des lignes du tableau */
tr:hover {
    background-color: #3d3d3d;
}

/* Style pour les messages de succès */
.success-message {
    background-color: #FF69B4;
    color: #ffffff;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
}

/* Style pour les messages d'erreur */
.error-message {
    background-color: #FF1493;
    color: white;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
}

/* Style pour la grille de formulaires */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

/* Style pour le bouton retour */
button[onclick] {
    background-color: #DDA0DD;
    color: white;
    font-size: 14px;
    transition: background-color 0.3s;
}

button[onclick]:hover {
    background-color: #DA70D6;
}


    </style>
    <script>
        // Fonction pour confirmer la suppression d'un créneau
        function confirmDeletion() {
            return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?');
        }
    </script>
</head>
<body>
<?php
session_start();
include '../../db.php';

// Vérifie si l'utilisateur est un administrateur
function validateSession() {
    if ($_SESSION['role'] != 'admin') {
        header("Location: dashboard_A.php");
        exit();
    }
}

// Fonction pour ajouter un nouveau créneau
function addCreneau($conn) {
    $nomM = $_POST['Nom_M'] ?? null;
    $descM = $_POST['Desc_M'] ?? null;
    $dateDeb = $_POST['date_deb'] ?? null;
    $heureDeb = $_POST['Heure_deb'] ?? null;
    $dateFin = $_POST['date_fin'] ?? null;
    $heureFin = $_POST['Heure_Fin'] ?? null;
    $resp = $_POST['Resp'] ?? null;
    $creditRequis = $_POST['credit_requis'] ?? 15;

    // Vérifie si tous les champs sont remplis
    if ($nomM && $descM && $dateDeb && $heureDeb && $dateFin && $heureFin && $resp) {
        $stmt = $conn->prepare("INSERT INTO créneau (Nom_M, Desc_M, date_deb, Heure_deb, date_fin, Heure_Fin, Resp, credit_requis) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nomM, $descM, $dateDeb, $heureDeb, $dateFin, $heureFin, $resp, $creditRequis]);
        echo "<div class='success-message'>Activité ajoutée avec succès.</div>";
    } else {
        echo "<div class='error-message'>Veuillez remplir tous les champs pour ajouter une activité.</div>";
    }
}

// Fonction pour supprimer un créneau
function deleteCreneau($conn) {
    $id_C = $_POST['id_C'];
    $stmt = $conn->prepare("DELETE FROM créneau WHERE id_C = ?");
    $stmt->execute([$id_C]);
    echo "<div class='success-message'>Activité supprimée avec succès.</div>";
}

// Fonction pour mettre à jour un créneau existant
function updateCreneau($conn) {
    $id_C = $_POST['id_C'];
    $nomM = $_POST['Nom_M'] ?? null;
    $descM = $_POST['Desc_M'] ?? null;
    $dateDeb = $_POST['date_deb'] ?? null;
    $heureDeb = $_POST['Heure_deb'] ?? null;
    $dateFin = $_POST['date_fin'] ?? null;
    $heureFin = $_POST['Heure_Fin'] ?? null;
    $resp = $_POST['Resp'] ?? null;
    $creditRequis = $_POST['credit_requis'] ?? 15;

    // Vérifie si tous les champs sont remplis
    if ($nomM && $descM && $dateDeb && $heureDeb && $dateFin && $heureFin && $resp) {
        $stmt = $conn->prepare("UPDATE créneau SET Nom_M = ?, Desc_M = ?, date_deb = ?, Heure_deb = ?, date_fin = ?, Heure_Fin = ?, Resp = ?, credit_requis = ? WHERE id_C = ?");
        $stmt->execute([$nomM, $descM, $dateDeb, $heureDeb, $dateFin, $heureFin, $resp, $creditRequis, $id_C]);
        echo "<div class='success-message'>Activité mise à jour avec succès.</div>";
    } else {
        echo "<div class='error-message'>Veuillez remplir tous les champs pour mettre à jour l'activité.</div>";
    }
}

// Vérifie les droits d'administrateur
validateSession();

// Traitement des actions POST (ajout, suppression, mise à jour)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) addCreneau($conn);
    if (isset($_POST['delete'])) deleteCreneau($conn);
    if (isset($_POST['update'])) updateCreneau($conn);
}

// Récupère tous les créneaux de la base de données
$creneaux = $conn->query("SELECT * FROM créneau")->fetchAll();
$creneauToEdit = null;

// Récupère les informations du créneau à modifier
if (isset($_POST['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM créneau WHERE id_C = ?");
    $stmt->execute([$_POST['id_C']]);
    $creneauToEdit = $stmt->fetch();
}
?>

<button onclick="javascript:history.back()" style="margin-bottom: 20px;">Retour</button>

<h2>Gérer les Activités</h2>

<div class="form-grid">
    <!-- Formulaire d'ajout de créneau -->
    <div>
        <h3>Ajouter une Activité</h3>
        <form method="post">
            <input type="text" name="Nom_M" placeholder="Nom de l'activité" required>
            <input type="text" name="Desc_M" placeholder="Description" required>
            <input type="date" name="date_deb" required>
            <input type="time" name="Heure_deb" required>
            <input type="date" name="date_fin" required>
            <input type="time" name="Heure_Fin" required>
            <input type="text" name="Resp" placeholder="Responsable" required>
            <input type="number" name="credit_requis" placeholder="Crédits requis" value="15" required>
            <button type="submit" name="add">Ajouter</button>
        </form>
    </div>

    <!-- Formulaire de modification de créneau -->
    <?php if ($creneauToEdit) { ?>
    <div>
        <h3>Modifier l'Activité</h3>
        <form method="post">
            <input type="hidden" name="id_C" value="<?= $creneauToEdit['id_C'] ?>">
            <input type="text" name="Nom_M" value="<?= $creneauToEdit['Nom_M'] ?>" required>
            <input type="text" name="Desc_M" value="<?= $creneauToEdit['Desc_M'] ?>" required>
            <input type="date" name="date_deb" value="<?= $creneauToEdit['date_deb'] ?>" required>
            <input type="time" name="Heure_deb" value="<?= $creneauToEdit['Heure_deb'] ?>" required>
            <input type="date" name="date_fin" value="<?= $creneauToEdit['date_fin'] ?>" required>
            <input type="time" name="Heure_Fin" value="<?= $creneauToEdit['Heure_Fin'] ?>" required>
            <input type="text" name="Resp" value="<?= $creneauToEdit['Resp'] ?>" required>
            <input type="number" name="credit_requis" value="<?= $creneauToEdit['credit_requis'] ?>" required>
            <button type="submit" name="update">Mettre à jour</button>
        </form>
    </div>
    <?php } ?>
</div>

<!-- Tableau d'affichage des créneaux -->
<h3>Liste des Activités</h3>
<div style="overflow-x: auto;">
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Date Début</th>
            <th>Heure Début</th>
            <th>Date Fin</th>
            <th>Heure Fin</th>
            <th>Responsable</th>
            <th>Crédits requis</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($creneaux as $creneau) { ?>
        <tr>
            <td><?= $creneau['id_C'] ?></td>
            <td><?= $creneau['Nom_M'] ?></td>
            <td><?= $creneau['Desc_M'] ?></td>
            <td><?= $creneau['date_deb'] ?></td>
            <td><?= $creneau['Heure_deb'] ?></td>
            <td><?= $creneau['date_fin'] ?></td>
            <td><?= $creneau['Heure_Fin'] ?></td>
            <td><?= $creneau['Resp'] ?></td>
            <td><?= $creneau['credit_requis'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id_C" value="<?= $creneau['id_C'] ?>">
                    <button type="submit" name="edit">Éditer</button>
                </form>
                <form method="post" style="display:inline;" onsubmit="return confirmDeletion();">
                    <input type="hidden" name="id_C" value="<?= $creneau['id_C'] ?>">
                    <button type="submit" name="delete">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>