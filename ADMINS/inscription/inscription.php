<?php
// Inclusion de la connexion à la base de données
include '../../db.php';

// Traitement du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mail = $_POST['mail'];
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);
    $role = "Utilisateur";

    // Vérification si l'email existe déjà
    $stmt = $conn->prepare("SELECT * FROM compte WHERE Mail = ?");
    $stmt->execute([$mail]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        echo "<div class='alert alert-danger'>Un compte avec cet e-mail existe déjà. Veuillez utiliser un autre e-mail.</div>";
    } else {
        // Insertion du nouvel utilisateur dans la base de données
        $stmt = $conn->prepare("INSERT INTO compte (Nom_C, Prenom_C, Mail, MotDePasse, Role_C) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $mail, $motdepasse, $role]);
        echo "<div class='alert alert-success'>Compte créé avec succès.<br>Redirection vers la page de connexion en cours...</div>";
        header("Refresh: 3; url=../connexion/connexion.php");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        :root {
            --bg-primary: #1a1a1a; /* Fond sombre */
            --bg-secondary: #2d2d2d; /* Fond secondaire sombre */
            --text-primary: #ffffff; /* Texte clair */
            --text-secondary: #ff69b4; /* Rose pour le texte secondaire */
            --accent: #ff69b4; /* Rose vif */
            --accent-hover: #ff1493; /* Rose foncé au survol */
            --danger: #ff4d6d; /* Rouge rosé */
            --card-shadow: 0 10px 40px rgba(255, 105, 180, 0.2); /* Ombre rosée */
            --border-radius: 12px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            line-height: 1.6;
            background-image: url('data:image/svg+xml,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" fill="%23ff69b4" opacity="0.1"/></svg>');
        }

        .inscription-container {
            width: 100%;
            max-width: 500px;
            margin: 20px;
            padding: 40px;
            background-color: var(--bg-secondary);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-speed) ease;
            border: 2px solid rgba(255, 105, 180, 0.2);
        }

        .inscription-container:hover {
            transform: translateY(-5px);
        }

        .form-title {
            text-align: center;
            margin-bottom: 35px;
            color: var(--accent);
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: 1px;
            border-bottom: 3px solid var(--accent);
            padding-bottom: 15px;
        }

        .form-floating {
            position: relative;
            margin-bottom: 25px;
        }

        .form-floating input {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 105, 180, 0.3);
            color: var(--text-primary);
            padding: 15px;
            font-size: 16px;
            border-radius: 10px;
            transition: all var(--transition-speed) ease;
        }

        .form-floating input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 10px rgba(255, 105, 180, 0.3);
            outline: none;
        }

        .form-floating label {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            transition: all var(--transition-speed) ease;
            pointer-events: none;
            font-size: 16px;
        }

        .form-floating input:focus ~ label,
        .form-floating input:not(:placeholder-shown) ~ label {
            top: 0;
            left: 10px;
            font-size: 14px;
            padding: 0 5px;
            background-color: var(--bg-secondary);
            color: var(--accent);
        }

        .btn-inscription {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            margin-top: 20px;
        }

        .btn-inscription:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 105, 180, 0.4);
        }

        .btn-inscription:active {
            transform: translateY(1px);
        }

        .text-center {
            margin-top: 25px;
            text-align: center;
        }

        .text-center a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
            border-bottom: 1px solid transparent;
        }

        .text-center a:hover {
            border-bottom-color: var(--accent);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            animation: slideIn 0.5s ease;
        }

        .alert-danger {
            background-color: rgba(255, 77, 109, 0.1);
            border: 1px solid #ff4d6d;
            color: #ff4d6d;
        }

        .alert-success {
            background-color: rgba(255, 105, 180, 0.1);
            border: 1px solid #ff69b4;
            color: #ff69b4;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 576px) {
            .inscription-container {
                margin: 15px;
                padding: 25px;
            }

            .form-title {
                font-size: 1.7rem;
                margin-bottom: 25px;
            }

            .form-floating input {
                font-size: 15px;
            }

            .btn-inscription {
                font-size: 16px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="inscription-container">
            <h2 class="form-title">Créer un compte</h2>
            <form method="post" action="">
                <div class="form-floating">
                    <input type="text" class="form-control" id="nom" name="nom" placeholder=" " required>
                    <label for="nom">Nom</label>
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" id="prenom" name="prenom" placeholder=" " required>
                    <label for="prenom">Prénom</label>
                </div>
                <div class="form-floating">
                    <input type="email" class="form-control" id="mail" name="mail" placeholder=" " required>
                    <label for="mail">Email</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" id="motdepasse" name="motdepasse" placeholder=" " required>
                    <label for="motdepasse">Mot de passe</label>
                </div>
                <button type="submit" class="btn btn-primary btn-inscription">S'inscrire</button>
            </form>
            <div class="text-center mt-4">
                <p>Déjà inscrit ? <a href="../connexion/connexion.php">Connectez-vous ici</a></p>
            </div>
        </div>
    </div>
</body>
</html>
