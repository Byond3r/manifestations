<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        /* Style pour le thème sombre avec rose */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #1a1a1a;
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    animation: fadeIn 1s ease-in-out;
}

/* Container principal du formulaire de connexion */
.login-container {
    background-color: #2d2d2d;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(255, 105, 180, 0.3);
    width: 100%;
    max-width: 400px;
    transform: translateY(30px);
    opacity: 0;
    animation: slideUp 1s ease-out forwards;
}

/* Style du titre de la page de connexion */
.login-title {
    text-align: center;
    margin-bottom: 2rem;
    color: #ff69b4;
    border-bottom: 2px solid #ff69b4;
    padding-bottom: 10px;
    opacity: 0;
    animation: fadeInTitle 1s ease-in-out 0.5s forwards;
}

/* Style des groupes de champs du formulaire */
.form-group {
    margin-bottom: 1.5rem;
}

/* Style des labels des champs */
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #ff69b4;
}

/* Style des champs de saisie */
.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ff69b4;
    border-radius: 5px;
    background-color: #3d3d3d;
    color: #ffffff;
    box-sizing: border-box;
    transition: background-color 0.3s ease-in-out;
}

/* Style des champs de saisie au focus */
.form-group input:focus {
    outline: none;
    background-color: #4d4d4d;
    border-color: #ff1493;
}

/* Style du bouton de soumission */
.submit-btn {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 5px;
    background-color: #ff69b4;
    color: #ffffff;
    font-size: 1rem;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s, transform 0.1s;
}

/* Animation au survol du bouton */
.submit-btn:hover {
    background-color: #ff1493;
    transform: scale(1.05);
}

/* Animation du message d'erreur */
.error-message {
    background-color: #ff4444;
    color: white;
    padding: 10px;
    border-radius: 4px;
    text-align: center;
    margin-top: 1rem;
    opacity: 0;
    animation: fadeInError 1s ease-in-out forwards;
}

/* Style du lien d'inscription */
.signup-link {
    display: block;
    text-align: center;
    margin-top: 1rem;
    color: #ff69b4;
    text-decoration: none;
    opacity: 0;
    animation: fadeInLink 1s ease-in-out 1.5s forwards;
}

.signup-link:hover {
    text-decoration: underline;
    color: #ff1493;
}

/* Définition des animations */
@keyframes fadeIn {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

@keyframes slideUp {
    0% {
        transform: translateY(30px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeInTitle {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

@keyframes fadeInError {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

@keyframes fadeInLink {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

    </style>
</head>
<body>
<?php
// Connexion à la base de données avec MySQLi
$servername = "localhost";
$username = "root"; // Utilisez votre nom d'utilisateur MySQL ici
$password = "";     // Utilisez votre mot de passe MySQL ici
$dbname = "manifestation"; // Utilisez le nom de votre base de données ici

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}

// Démarrage de la session
session_start();

// Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = $_POST['mail'];
    $motdepasse = $_POST['motdepasse'];

    // Préparation et exécution de la requête pour vérifier les identifiants
    $stmt = $conn->prepare("SELECT * FROM compte WHERE Mail = ?");
    $stmt->bind_param("s", $mail); // "s" indique que le paramètre est une chaîne (string)
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Vérification du mot de passe et création de la session si correct
    if ($user && password_verify($motdepasse, $user['MotDePasse'])) {
        $_SESSION['id_U'] = $user['id_U'];
        $_SESSION['role'] = $user['Role_C'];

        // Rediriger en fonction du rôle
        if ($user['Role_C'] == 'admin') {
            header("Location: ../../ADMINS/dashboard_A.php");
            exit();
        } elseif ($user['Role_C'] == 'participant') {
            header("Location: ../../Tableau_de_bord/vue_activité/Page_Accueil_U.php");
            exit();
        } elseif ($user['Role_C'] == 'responsable') {
            header("Location: ../../RESPONSABLES/dashboard_R.php");
            exit();
        }
    } else {
        $error_message = "Email ou mot de passe incorrect.";
    }
}
?>

<div class="login-container">
    <h2 class="login-title">Connexion</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="mail">Email</label>
            <input type="email" id="mail" name="mail" required>
        </div>
        <div class="form-group">
            <label for="motdepasse">Mot de passe</label>
            <input type="password" id="motdepasse" name="motdepasse" required>
        </div>
        <button type="submit" class="submit-btn">Se connecter</button>
    </form>
    <div>
        <a href="../../ADMINS/inscription/inscription.php" class="signup-link">Venez vous inscrire !</a>
    </div>
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
</div>

<?php
// Fermeture de la connexion MySQLi
$conn->close();
?>
</body>
</html>
