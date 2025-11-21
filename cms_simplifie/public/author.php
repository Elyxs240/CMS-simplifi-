<?php
// Charge la config, la connexion BDD et le header 
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/header.php';

// Récupère le login depuis la requête et le valide 
$login = trim($_GET['login'] ?? '');
if ($login === '') { 
  include __DIR__.'/page404.php'; 
  require_once __DIR__.'/../inc/footer.php'; 
  exit; 
}

// Recherche de l'utilisateur par son login (requête préparée)
$stmt = $pdo->prepare("SELECT id, login FROM utilisateur WHERE login = :login");
$stmt->execute(['login'=>$login]);
$user = $stmt->fetch();

// Si l'utilisateur n'existe pas, message simple puis fin
if (!$user) { 
  echo "<div class='card'><p>Utilisateur introuvable.</p></div>"; 
  require_once __DIR__ . '/../inc/footer.php'; 
  exit; 
}

// Titre de page avec le login
echo '<h2>Post de ' . e($user['login']) . '</h2>';

// Récupère les articles de cet auteur (du plus récent au plus ancien)
$stmt = $pdo->prepare("
  SELECT id, titre, contenu, date_creation 
  FROM articles 
  WHERE user_id = :uid 
  ORDER BY date_creation DESC
");
$stmt->execute(['uid'=>$user['id']]);

// affichage des articles (extrait + lien "Lire la suite")
foreach ($stmt as $a) {
  echo '<article class="card">';
  echo '<h2><a href="/public/article.php?id='.(int)$a['id'].'">'.e($a['titre']).'</a></h2>';
  echo '<p class="muted">'.e(date('d/m/Y H:i', strtotime($a['date_creation']))).'</p>';
  echo '<p>'.e(excerpt($a['contenu'])).'</p>';
  echo '<p><a class="btn" href="/public/article.php?id='.(int)$a['id'].'">Lire la suite</a></p>';
  echo '</article>';
}

// Footer 
require_once __DIR__ . '/../inc/footer.php';
