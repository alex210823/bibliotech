<?php
    $DB_HOST = 'db';
    $DB_NAME = 'bibliotech';
    $DB_USER = 'root';
    $DB_PASS = 'rootpassword';

    // Creazione della connessione
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

    // Controllo della conneessione
    if (mysqli_connect_errno()){
        echo "Errore di connessione al database: " . mysqli_connect_error();
        exit();
    }