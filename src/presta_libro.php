<?php
    session_start();
    if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    require_once 'config.php';

    $id_libro = $_POST['id_libro'] ?? null;
    $user_id = $_SESSION['user_id'];
    $MAX_PRESTITI = 3;

    // Controllo dei prestiti attivi
    $prestiti_attivi = 0;
    $sql = "SELECT COUNT(*) AS count FROM prestiti WHERE id_utente = ? AND data_fine IS NULL";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $prestiti_attivi=(int)$row['count'];

    if ($prestiti_attivi >= $MAX_PRESTITI) {
        header('Location: libri.php');
        exit();
    }

    // Controllo disponibilità del libro
    $sql = "SELECT copie_disponibili FROM libri WHERE id_libro = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_libro);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $libro = mysqli_fetch_assoc($result);

    if (!$libro || (int)$libro['copie_disponibili'] <= 0) {
        header('Location: libri.php');
        exit();
    }

    // trasazione 
    mysqli_begin_transaction($conn);

    // Inserimento del prestito
    $sql = "INSERT INTO prestiti (data_inizio, data_fine, id_utente, id_libro) VALUES (NOW(), NULL, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $id_libro);
    mysqli_stmt_execute($stmt);

    // Aggiornamento delle copie disponibili
    $sql = "UPDATE libri SET copie_disponibili = copie_disponibili - 1 WHERE id_libro = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_libro);
    mysqli_stmt_execute($stmt);     

    // Commit della transazione
    mysqli_commit($conn);

    header('Location: libri.php');
    exit();

