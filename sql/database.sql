CREATE DATABASE IF NOT EXISTS bibliotech;
USE bibliotech;

CREATE TABLE utenti (
id_utente INT AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(50) NOT NULL,
cognome VARCHAR(50) NOT NULL,
email VARCHAR(100) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
ruolo ENUM('studente','bibliotecario') NOT NULL
);

CREATE TABLE libri (
id_libro INT AUTO_INCREMENT PRIMARY KEY,
titolo VARCHAR(100) NOT NULL,
autore VARCHAR(100) NOT NULL,
anno_pubblicazione INT,
copie_totali INT NOT NULL,
copie_disponibili INT NOT NULL
);

CREATE TABLE prestiti (
id_prestito INT AUTO_INCREMENT PRIMARY KEY,
data_inizio DATE NOT NULL,
data_fine DATE,
id_utente INT NOT NULL,
id_libro INT NOT NULL,
FOREIGN KEY (id_utente) REFERENCES utenti(id_utente),
FOREIGN KEY (id_libro) REFERENCES libri(id_libro)
);

INSERT INTO utenti (nome, cognome, email, password_hash, ruolo) VALUES
('Mario', 'Rossi', 'mario.rossi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'studente'),
('Luca', 'Bianchi', 'luca.bianchi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'studente'),
('Anna', 'Verdi', 'anna.verdi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'studente'),
('Paola', 'Neri', 'paola.neri@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bibliotecario');

INSERT INTO libri (titolo, autore, anno_pubblicazione, copie_totali, copie_disponibili) VALUES
('1984', 'George Orwell', 1949, 10, 8),
('Il Signore degli Anelli', 'J.R.R. Tolkien', 1954, 5, 5),
('Il nome della rosa', 'Umberto Eco', 1980, 3, 2),
('Harry Potter e la pietra filosofale', 'J.K. Rowling', 1997, 7, 7),
('I promessi sposi', 'Alessandro Manzoni', 1840, 6, 4);
