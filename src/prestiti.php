<?php
    session_start();
    if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    require_once 'config.php';

    $sql = "SELECT p.id_prestito, p.data_inizio, l.titolo
            FROM prestiti p JOIN libri l ON p.id_libro = l.id_libro
            WHERE p.id_utente = ? AND p.data_fine IS NULL
            ORDER BY p.data_inizio DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);        
    $prestiti = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $prestiti[] = $row;
    }   
?>
<!DOCTYPE html>
<html lang="it">
    <head></head>
        <meta charset="UTF-8">
        <title> Miei Prestiti - BiblioTech</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            h1 {
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            table th, table td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }
            a {
                color: #007bff;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
    <div>
        <strong>Utente:</strong> <?php echo htmlspecialchars($_SESSION['user_nome'] . ' ' . $_SESSION['user_cognome']); ?>
        (<?php echo htmlspecialchars($_SESSION['user_ruolo']); ?>)
    </div>
    <div>
        <a href="logout.php">Logout</a>
    </div>

    <h1>I miei prestiti attivi</h1>

    <?php if (empty($prestiti)): ?>
        <div>
            Non hai prestiti attivi al momento.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Prestito</th>
                    <th>Data inizio</th>
                    <th>Titolo libro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestiti as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['id_prestito']); ?></td>
                        <td><?php echo htmlspecialchars($p['data_inizio']); ?></td>
                        <td><?php echo htmlspecialchars($p['titolo']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="libri.php">← Torna al catalogo</a></p>

</body>
</html>