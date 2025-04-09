<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'manifestation';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Traitement du formulaire de création
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $role = trim($_POST['Role_C']);
            if (!empty($role) && in_array($role, ['admin', 'responsable', 'participant'])) {
                $sql = "INSERT INTO compte (Role_C) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $role);
                if ($stmt->execute()) {
                    $message = "Rôle créé avec succès.";
                } else {
                    $message = "Erreur lors de la création du rôle : " . $conn->error;
                }
                $stmt->close();
            } else {
                $message = "Le rôle doit être 'admin', 'responsable' ou 'participant'.";
            }
        } elseif ($_POST['action'] === 'update') {
            $userId = $_POST['user_id'];
            $newRole = $_POST['new_role'];
            if (in_array($newRole, ['admin', 'responsable', 'participant'])) {
                $sql = "UPDATE compte SET Role_C = ? WHERE Id_U = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $newRole, $userId);
                if ($stmt->execute()) {
                    $message = "Rôle mis à jour avec succès.";
                } else {
                    $message = "Erreur lors de la mise à jour du rôle : " . $conn->error;
                }
                $stmt->close();
            } else {
                $message = "Rôle invalide sélectionné.";
            }
        } elseif ($_POST['action'] === 'delete') {
            $userId = $_POST['user_id'];
            $sql = "UPDATE compte SET Role_C = 'participant' WHERE Id_U = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            if ($stmt->execute()) {
                $message = "Rôle réinitialisé à 'participant' avec succès.";
            } else {
                $message = "Erreur lors de la réinitialisation du rôle : " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Récupération des utilisateurs et leurs rôles
$sql_users = "SELECT Id_U, Nom_C, Prenom_C, Role_C FROM compte WHERE Nom_C IS NOT NULL AND Prenom_C IS NOT NULL";
$result_users = $conn->query($sql_users);

// Liste des rôles disponibles
$available_roles = ['admin', 'responsable', 'participant'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rôles</title>
    <style>
        :root {
    --primary-color: #FF69B4;
    --background-color: #1a1a1a;
    --secondary-background: #2d2d2d;
    --text-color: #FF69B4;
    --border-color: #FF69B4;
    --success-color: #FF1493;
    --danger-color: #C71585;
    --hover-color: #FFB6C1;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--background-color);
    color: var(--text-color);
    margin: 0;
    padding: 0;
    line-height: 1.6;
    animation: fadeInBody 1s ease-in-out;
}

.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 25px;
    background: var(--secondary-background);
    box-shadow: 0 4px 12px rgba(255, 105, 180, 0.2);
    border-radius: 12px;
    animation: slideUp 0.5s ease-in-out;
}

h1, h2 {
    text-align: center;
    margin-bottom: 25px;
    color: var(--text-color);
    font-weight: 600;
    animation: fadeInUp 0.7s ease-out;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
}

label {
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--text-color);
    animation: fadeInUp 0.6s ease-out;
}

input[type="text"], select {
    padding: 12px;
    background-color: var(--background-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    color: var(--text-color);
    transition: border-color 0.3s ease;
    animation: fadeInUp 0.6s ease-out;
}

input[type="text"]:focus, select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(255, 105, 180, 0.3);
}

button {
    padding: 12px 20px;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.3s ease;
    animation: fadeInUp 0.8s ease-out;
}

button:hover {
    background-color: var(--hover-color);
}

.delete-btn {
    background-color: var(--danger-color);
}

.delete-btn:hover {
    background-color: #DB7093;
}

.message {
    margin: 20px 0;
    padding: 12px;
    border-radius: 6px;
    background-color: var(--background-color);
    color: var(--text-color);
    text-align: center;
    animation: fadeInMessage 1s ease-out;
}

.message.success {
    background-color: var(--success-color);
    color: white;
}

.message.error {
    background-color: var(--danger-color);
    color: white;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 25px;
    background-color: var(--background-color);
    border-radius: 8px;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
}

th, td {
    padding: 15px;
    border: 1px solid var(--border-color);
    color: var(--text-color);
}

th {
    background-color: var(--secondary-background);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9em;
}

tr:hover {
    background-color: rgba(255, 105, 180, 0.1);
}

.roles-list {
    margin: 20px 0;
    padding: 15px;
    background-color: var(--background-color);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    animation: fadeInUp 0.5s ease-out;
}

.roles-list ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.roles-list li {
    padding: 8px 15px;
    background-color: var(--primary-color);
    border-radius: 20px;
    font-size: 0.9em;
    color: white;
    animation: bounceIn 0.6s ease-out;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-buttons form {
    margin: 0;
    flex: 1;
}

.role-highlight {
    background-color: var(--primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInBody {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInMessage {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes bounceIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); }
}

select {
    min-width: 150px;
}

.back-btn {
    margin-top: 20px;
    display: block;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
    animation: fadeInUp 0.9s ease-out;
}

@media (max-width: 768px) {
    .container {
        margin: 15px;
        padding: 15px;
    }

    .action-buttons {
        flex-direction: column;
    }

    table {
        font-size: 0.9em;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Rôles</h1>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <label for="Role_C">Nouveau Rôle :</label>
            <select id="Role_C" name="Role_C" required>
                <option value="">Sélectionner un rôle</option>
                <?php foreach($available_roles as $role): ?>
                    <option value="<?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Créer</button>
        </form>
        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <h2>Rôles disponibles</h2>
        <div class="roles-list">
            <ul>
                <?php foreach($available_roles as $role): ?>
                    <li><?= htmlspecialchars($role) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <h2>Liste des utilisateurs et leurs rôles</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Rôle actuel</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $result_users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['Nom_C']) ?></td>
                        <td><?= htmlspecialchars($user['Prenom_C']) ?></td>
                        <td><span class="role-highlight"><?= htmlspecialchars($user['Role_C'] ?? 'participant') ?></span></td>
                        <td class="action-buttons">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="user_id" value="<?= $user['Id_U'] ?>">
                                <select name="new_role" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <?php foreach($available_roles as $role): ?>
                                        <option value="<?= htmlspecialchars($role) ?>"
                                            <?= ($user['Role_C'] === $role) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($role) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit">Modifier</button>
                            </form>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?= $user['Id_U'] ?>">
                                <button type="submit" class="delete-btn">Réinitialiser le rôle</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button onclick="window.location.href='../dashboard_A.php'" class="back-btn">Retour</button>
    </div>
</body>
</html>
