<?php
    session_start();
    if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    require_once 'config.php';

    $id_libro = $_GET['id'] ?? null;

    // carico libro
    $sql = "SELECT * FROM libri WHERE id_libro = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_libro);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $libro = mysqli_fetch_assoc($result);

    if (!$libro) {
        echo "Libro non trovato.";
        exit();
    }

    // Controllo dei prestiti attivi
    $MAX_PRESTITI = 3;
    $prestiti_attivi = 0;
    if($_SESSION['user_ruolo'] === 'studente') {
        $sql = "SELECT COUNT(*) AS count FROM prestiti WHERE id_utente = ? AND data_fine IS NULL";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $prestiti_attivi=(int)$row['count'];
        if ($prestiti_attivi >= $MAX_PRESTITI) {
            echo "Hai raggiunto il limite massimo di prestiti attivi.";
            exit();
        }
    }
?>
<!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Dettagli Libro - BiblioTech</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            h1 {
                margin-bottom: 20px;
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
        <div>
            <strong>Utente:</strong> <?php echo htmlspecialchars($_SESSION['user_nome'] . ' ' . $_SESSION['user_cognome']); ?>
            (<?php echo htmlspecialchars($_SESSION['user_ruolo']); ?>)
        </div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div>
        <h1><?php echo htmlspecialchars($libro['titolo']); ?></h1>
        <p><strong>Autore:</strong> <?php echo htmlspecialchars($libro['autore']); ?></p>
        <p><strong>Anno pubblicazione:</strong> <?php echo htmlspecialchars($libro['anno_pubblicazione']); ?></p>
        <p><strong>Copie totali:</strong> <?php echo (int)$libro['copie_totali']; ?></p>
        <p><strong>Copie disponibili:</strong> <?php echo (int)$libro['copie_disponibili']; ?></p>

        <?php if ($_SESSION['user_ruolo'] === 'studente' && $libro['copie_disponibili'] > 0 && $prestiti_attivi < $MAX_PRESTITI): ?>
            <form method="post" action="presta_libro.php">
                <input type="hidden" name="id_libro" value="<?php echo $libro['id_libro']; ?>">
                <button type="submit" class="presta">PRENDI IN PRESTITO</button>
            </form>
        <?php elseif ($_SESSION['user_ruolo'] === 'studente'): ?>
            <div>
                Non disponibile al prestito o limite prestiti raggiunto (<?php echo $prestiti_attivi; ?>/<?php echo $MAX_PRESTITI; ?>).
            </div>
        <?php endif; ?>
    </div>

    <p><a href="libri.php">← Torna al catalogo</a></p>

</body>
</html>
