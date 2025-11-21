DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS utilisateur;

CREATE TABLE utilisateur (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  titre VARCHAR(255) NOT NULL,
  contenu TEXT NOT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_articles_user FOREIGN KEY (user_id) REFERENCES utilisateur(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO articles (titre, contenu) VALUES
('Bienvenue', 'Bienvenue sur notre CMS !'),
('Ecrire', 'Inscrivez-vous, connectez-vous et redigez vos propres posts.');

INSERT INTO utilisateur (login, email, password, role) VALUES ('admin','admin@example.com','$2y$10$rZeIxDeW.MM/BWDySZxAmuhd6X/6YHfbpawNMqQjdgAJbHEXmS6Ay', 'admin');
