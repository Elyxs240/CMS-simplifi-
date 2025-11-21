# CMS Simplifié 

## Pour lancer

docker compose up -d --build

- Site : http://localhost:8080/public/index.php

## Compte admin
Identifiant :admin 
Mdp :VH515f6frrv11e651
- meme le mot de passe de l'admin est haché

## Notes
- PHP + Apache + mysql avec requete preparer 
- Sécurité basique : CSRF sur POST, password_hash/verify, PDO préparé
- Mise en place des sécurités pour eviter les attaques par XSS grace a la fonction e (échapper)
- Uniquement le dossier public est exposé : mise en place des sécurité par le fichier .htaccess
- Pas d'execution javascript en ligne ou insérer malicieusement. seul le code dans le fichier.js peut etre executer mais j'en ai pas donc le site ne devrait recevoir de code js