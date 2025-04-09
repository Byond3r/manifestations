<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des comptes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style pour le corps de la page */
body {
    background-color: #1a1a1a;
    color: #fff;
    padding: 20px;
    min-height: 100vh;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Style pour le conteneur */
.container {
    background-color: #2a2a2a;
    border-radius: 15px;
    padding: 30px;
    margin-top: 20px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    border: 1px solid #ff69b4;
    animation: fadeIn 0.5s ease;
}

/* Style pour les champs de formulaire */
.form-control {
    background-color: #333333;
    border: 1px solid #ff69b4;
    color: #fff;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    background-color: #404040;
    border-color: #ff1493;
    color: #ffffff;
    box-shadow: 0 0 0 0.2rem rgba(255,105,180,.25);
    transform: scale(1.05);
}

/* Style pour le bouton primaire */
.btn-primary {
    background-color: #ff69b4;
    border: none;
    padding: 12px 30px;
    transition: all 0.3s ease;
    font-weight: 600;
    letter-spacing: 0.5px;
    border-radius: 8px;
}

.btn-primary:hover {
    background-color: #ff1493;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,105,180,0.3);
}

/* Style pour le titre h2 */
h2 {
    color: #ff69b4;
    margin-bottom: 30px;
    text-align: center;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    animation: slideIn 0.7s ease;
}

/* Style pour les labels */
label {
    color: #ffc0cb;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 0.95rem;
}

/* Style pour le champ de sélection */
select.form-control {
    cursor: pointer;
    appearance: none;
    padding-right: 30px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath d='M8 11l-7-7h14l-7 7z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    transition: all 0.3s ease;
}

/* Animation des alertes */
.alert {
    border-radius: 10px;
    margin-bottom: 25px;
    border: none;
    animation: fadeIn 0.5s ease;
}

.alert-success {
    background-color: #4a2749;
    color: #ffc0cb;
}

.alert-danger {
    background-color: #4a1f2f;
    color: #ffb6c1;
}

/* Style des éléments flottants */
.form-floating {
    margin-bottom: 20px;
}

/* Bouton pour masquer/montrer le mot de passe */
.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #ff69b4;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: #ff1493;
}

/* Animation de l'apparition */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animation de l'apparition du titre */
@keyframes slideIn {
    from {
        transform: translateX(-100%);
    }
    to {
        transform: translateX(0);
    }
}

/* Effet de focus sur les champs */
.form-control:focus {
    background-color: #404040;
    border-color: #ff1493;
    box-shadow: 0 0 0 0.2rem rgba(255,105,180,.25);
    transform: scale(1.05);
}

    </style>
</head>
<body>
<?php
session_start();
include '../../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        $sql = "INSERT INTO compte (Nom_C, Prenom_C, Mail, MotDePasse, Role_C) VALUES (:nom, :prenom, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role
        ]);
        
        $_SESSION['message'] = "Compte créé avec succès !";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch(PDOException $e) {
        if($e->getCode() == 23000) {
            $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la création du compte.";
        }
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<div class="container">
    <a href="../dashboard_A.php" class="btn btn-primary mb-3">Retour</a>
    
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <h2>Création de compte utilisateur</h2>
    <form method="POST" action="" class="needs-validation" novalidate>
        <div class="form-floating mb-3">
            <input type="text" name="nom" class="form-control" id="floatingNom" placeholder="Nom" required>
            <label for="floatingNom">Nom</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" name="prenom" class="form-control" id="floatingPrenom" placeholder="Prénom" required>
            <label for="floatingPrenom">Prénom</label>
        </div>
        <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="Email" required>
            <label for="floatingEmail">Email</label>
        </div>
        <div class="form-floating mb-3 position-relative">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Mot de passe" required>
            <label for="floatingPassword">Mot de passe</label>
            <span class="password-toggle" onclick="togglePassword()">👁️</span>
        </div>
        <div class="form-floating mb-4">
            <select name="role" class="form-control" id="floatingRole" required>
                <option value="admin">Administrateur</option>
                <option value="responsable">Responsable</option>
                <option value="participant">Participant</option>
            </select>
            <label for="floatingRole">Rôle</label>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Créer le compte</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const passwordInput = document.getElementById('floatingPassword');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
    } else {
        passwordInput.type = 'password';
    }
}

// Validation des formulaires Bootstrap
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>
</body>
</html>