<?php
    session_start();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    require_once 'config.php';

    // Azione di restituzione
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_prestito'])) {
        $id_prestito = $_POST['id_prestito'];

        // trasazione 
        mysqli_begin_transaction($conn);

        // trova il libro
        $sql = "SELECT id_libro FROM prestiti WHERE id_prestito = ? AND data_fine IS NULL";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id_prestito);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $prestito = mysqli_fetch_assoc($result);

        if ($prestito){
            // Aggiornamento del prestito
            $sql = "UPDATE prestiti SET data_fine = NOW() WHERE id_prestito = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $id_prestito);
            mysqli_stmt_execute($stmt);

            // Aggiornamento delle copie disponibili
            $sql = "UPDATE libri SET copie_disponibili = copie_disponibili + 1 WHERE id_libro = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $prestito['id_libro']);
            mysqli_stmt_execute($stmt);     

            $message = "Libro restituito con successo.";
        }
        // Commit della transazione
        mysqli_commit($conn);
    }

    // lista prestiti attivi di tutti gli studenti
    $sql = "SELECT p.id_prestito, p.data_inizio, u.nome, u.cognome, l.titolo
            FROM prestiti p JOIN libri l ON p.id_libro = l.id_libro
            JOIN utenti u ON p.id_utente = u.id_utente
            WHERE p.data_fine IS NULL
            ORDER BY p.data_inizio DESC";
    $result = mysqli_query($conn, $sql);  
    $prestiti = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $prestiti[] = $row;
    }
 ?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Gestione Restituzioni - BiblioTech</title>
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

        <?php if(empty($prestiti)): ?>
            <div>
                Non ci sono prestiti attivi al momento.
            </div>
        <?php else: ?>
            <h1>Prestiti Attivi</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID Prestito</th>
                        <th>Data inizio</th>
                        <th>Nome Utente</th>
                        <th>Cognome Utente</th>
                        <th>Titolo libro</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prestiti as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['id_prestito']); ?></td>
                            <td><?php echo htmlspecialchars($p['data_inizio']); ?></td>
                            <td><?php echo htmlspecialchars($p['nome']); ?></td>
                            <td><?php echo htmlspecialchars($p['cognome']); ?></td> 
                            <td><?php echo htmlspecialchars($p['titolo']); ?></td>
                            <td>
                                <form method="POST" action="gestione_restituzioni.php">
                                    <input type="hidden" name="id_prestito" value="<?php echo htmlspecialchars($p['id_prestito']); ?>">
                                    <button type="submit">Restituisci</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="libri.php">Torna al catalogo libri</a></p>

    </body>
</html>