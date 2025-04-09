<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bougé c'est Esseinti'elles</title>
    <style>
        :root {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --accent: #ff69b4;
            --accent-hover: #ff1493;
            --danger: #e53935;
            --card-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
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

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1517191434949-5e90cd67d2b6');
            background-size: cover;
            background-position: center;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
        }

        .hero-content {
            max-width: 800px;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            color: var(--accent);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 1.4rem;
            margin-bottom: 30px;
            color: var(--text-primary);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 20px;
        }

        .section-title {
            text-align: center;
            color: var(--accent);
            font-size: 2.2rem;
            margin-bottom: 40px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .feature-card {
            background: var(--bg-secondary);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-card h3 {
            color: var(--accent);
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            padding: 20px;
            position: absolute;
            top: 0;
            right: 0;
        }

        .btn {
            background-color: var(--accent);
            color: var(--text-primary);
            padding: 15px 35px;
            font-size: 1.2rem;
            font-weight: bold;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color var(--transition-speed), transform var(--transition-speed);
            text-decoration: none;
        }

        .btn:hover {
            background-color: var(--accent-hover);
            transform: scale(1.05);
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .btn-container {
                flex-direction: column;
                align-items: flex-end;
            }

            .feature-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="btn-container">
        <a href="../manifestation/ADMINS/connexion/connexion.php" class="btn">Se Connecter</a>
        <a href="../manifestation/ADMINS/inscription/inscription.php" class="btn">Rejoignez-nous</a>
    </div>

    <section class="hero">
        <div class="hero-content">
            <h1>Bouger c'est Essenti'elles</h1>
            <p>Ensemble contre le cancer du sein, pour une vie plus active et solidaire</p>
        </div>
    </section>

    <div class="container">
        <h2 class="section-title">Notre Mission</h2>
        <p style="text-align: center; margin-bottom: 50px; font-size: 1.2rem;">
            Une association engagée dans la lutte contre le cancer du sein. Nous sensibilisons, soutenons et accompagnons les femmes touchées par cette maladie à travers des activités physiques adaptées et un réseau de solidarité.
        </p>

        <h2 class="section-title">Nos Services</h2>
        <div class="features">
            <div class="feature-card">
                <h3>Sensibilisation</h3>
                <p>Campagnes d'information, ateliers de prévention et dépistage précoce. Nous organisons régulièrement des sessions d'information pour sensibiliser le public.</p>
            </div>
            <div class="feature-card">
                <h3>Activités Adaptées</h3>
                <p>Yoga, marche douce, aquagym et exercices thérapeutiques encadrés par des professionnels qualifiés pour un accompagnement personnalisé.</p>
            </div>
            <div class="feature-card">
                <h3>Soutien</h3>
                <p>Groupes de parole, accompagnement personnalisé et entraide. Un espace d'écoute et de partage pour se sentir moins seule face à la maladie.</p>
            </div>
            <div class="feature-card">
                <h3>Événements</h3>
                <p>Marches roses, conférences et rencontres entre membres. Rejoignez-nous lors de nos événements pour partager des moments de convivialité.</p>
            </div>
        </div>
    </div>
    <footer class="footer">
        © 2024 Tous droits réservés - Grégory Mutombo
    </footer>
</body>
</html>