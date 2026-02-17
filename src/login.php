<?php
    session_start();
    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $sql = "SELECT * FROM utenti WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if($user && password_verify($password, $user['password_hash'])){
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id_utente'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_cognome'] = $user['cognome'];
            $_SESSION['user_ruolo'] = $user['ruolo'];
            
            if ($user['ruolo'] === 'bibliotecario') {
                header('Location: gestione_restituzioni.php');
            } else {
                header('Location: libri.php');
            }
        } else {
            $error = "Email o password errati.";
        }

    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BiblioTech</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style
</head>
<body>
    <h1> Login Bibliotech</h1>

    <form method="POST">
        <label for="email"> Email: </label>
        <input type="email" id="email" name="email" required>

        <label for="password"> Password: </label>
        <input type="password" id="password" name="password" required>

        <button type="submit"> Accedi </button>
        
    </form>
</body>
</html>