<?php
// Charge la config, la connexion BDD et le header 
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/header.php';

// Récupère et valide l'ID d'article depuis l'URL 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { 
  http_response_code(404); 
  include __DIR__.'/page404.php'; 
  require_once __DIR__.'/../inc/footer.php'; 
  exit; 
}

// Requête sécurisée : récupère l'article + auteur avec jointure
$stmt = $pdo->prepare("
  SELECT a.id, a.titre, a.contenu, a.date_creation, u.login AS auteur
  FROM articles a
  LEFT JOIN utilisateur u ON u.id = a.user_id
  WHERE a.id = :id
");
$stmt->execute(['id'=>$id]);
$article = $stmt->fetch();

// Si aucun article trouvé → 404 propre
if (!$article) { 
  http_response_code(404); 
  include __DIR__.'/page404.php'; 
  require_once __DIR__.'/../inc/footer.php'; 
  exit; 
}
?>
<article class="card">
  <!-- Titre échappé pour prévenir la XSS -->
  <h2><?= e($article['titre']) ?></h2>

  <!-- Métadonnées : auteur  + date  -->
  <p class="muted">
    Par <a href="/public/author.php?login=<?= urlencode($article['auteur'] ?? 'inconnu') ?>"><?= e($article['auteur'] ?? 'Anonyme') ?></a>
    — <?= e(date('d/m/Y H:i', strtotime($article['date_creation']))) ?>
  </p>

  <!-- Contenu : échappé puis nl2br pour conserver les retours à la ligne -->
  <div><?= nl2br(e($article['contenu'])) ?></div>
</article>

<!-- Lien de retour à l'accueil -->
<p><a href="/public/index.php">&larr; Retour à l'accueil</a></p>

<?php 
// Footer
require_once __DIR__ . '/../inc/footer.php'; 
?>
