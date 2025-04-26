<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - FabLab</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 800px;
            width: 90%;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 700;
            font-size: 2.5rem;
        }

        .subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-weight: 300;
        }

        .button-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .login-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #fff;
            background-color: #3498db;
            padding: 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 150px;
        }

        .admin-btn { background-color: #e74c3c; }
        .user-btn { background-color: #2ecc71; }
        .tech-btn { background-color: #f39c12; }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.1);
        }

        .login-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .login-btn span {
            font-weight: 500;
        }

        .footer {
            margin-top: 40px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue au FabLab</h1>
        <p class="subtitle">Choisissez votre profil pour vous connecter</p>
        
        <div class="button-container">
            <a href="/admin" class="login-btn admin-btn">
                <i class="fas fa-user-shield"></i>
                <span>Admin</span>
            </a>
            <a href="/user/login" class="login-btn user-btn">
                <i class="fas fa-user"></i>
                <span>Utilisateur</span>
            </a>
            <a href="/technicien/login" class="login-btn tech-btn">
                <i class="fas fa-tools"></i>
                <span>Technicien</span>
            </a>
        </div>
        
       
    </div>
</body>
</html>