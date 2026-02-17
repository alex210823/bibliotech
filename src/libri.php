<?php
    session_start();
    if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    require_once 'config.php';

    // Parametri di ricerca e filtro
    $search = $_GET['q'] ?? '';
    $solo_disponibili = isset($_GET['solo_disponibili']) ? true : false;
    $MAX_PRESTITI = 3;

    // Controllo dei prestiti attivi per gli studenti
    $prestiti_attivi = 0;
    if($_SESSION['user_ruolo'] === 'studente') {
        $sql = "SELECT COUNT(*) AS count FROM prestiti WHERE id_utente = ? AND data_fine IS NULL";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $prestiti_attivi=(int)$row['count'];
    }
    
    // Query per recuperare i libri con ricerca e filtro
    $sql = "SELECT * FROM libri WHERE (titolo LIKE ? OR autore LIKE ?)";

    if ($solo_disponibili) {
        $sql .= " AND copie_disponibili > 0";
    }
    $sql .= " ORDER BY titolo";
    $like = '%' . $search . '%';
    $search_param[] = $like;
    $search_param[] = $like;
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $search_param[0], $search_param[1]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Catalogo Libri - BiblioTech</title>
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

    <h1>Catalogo Libri</h1>

    <div class="search-form">
        <form method="get">
            <input type="text" name="q" placeholder="Cerca per titolo o autore..." value="<?php echo htmlspecialchars($search); ?>">
            <label>
                <input type="checkbox" name="solo_disponibili" <?php if ($solo_disponibili) echo 'checked'; ?>>
                Solo disponibili
            </label>
            <button type="submit">Filtra</button>
        </form>
    </div>

    <?php if ($_SESSION['user_ruolo'] === 'studente'): ?>
    <div class="prestiti-info">
        <strong>Prestiti attivi:</strong> <?php echo $prestiti_attivi; ?> / <?php echo $MAX_PRESTITI; ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Titolo</th>
                <th>Autore</th>
                <th>Anno Pubblicazione</th>
                <th>Copie Totali</th>
                <th>Copie Disponibili</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php while($libro = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <a href="libro.php?id=<?php echo $libro['id_libro']; ?>">
                            <?php echo htmlspecialchars($libro['titolo']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($libro['autore']); ?></td>
                    <td><?php echo htmlspecialchars($libro['anno_pubblicazione']); ?></td>
                    <td><?php echo (int)$libro['copie_totali']; ?></td>
                    <td><?php echo (int)$libro['copie_disponibili']; ?></td>
                    <td>
                        <?php if ($_SESSION['user_ruolo'] === 'studente' && $libro['copie_disponibili'] > 0 && $prestiti_attivi < $MAX_PRESTITI): ?>
                            <form method="post" action="presta_libro.php">
                                <input type="hidden" name="id_libro" value="<?php echo $libro['id_libro']; ?>">
                                <button type="submit" class="presta">PRENDI IN PRESTITO</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($_SESSION['user_ruolo'] === 'studente'): ?>
    <p><a href="prestiti.php">→ Visualizza i miei prestiti attivi</a></p>
    <?php endif; ?>

</body>
</html>