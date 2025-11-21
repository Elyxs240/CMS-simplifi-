<?php
// Charge la config, la connexion BDD et le header
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/header.php';

// Récupère les 10 derniers articles avec leur auteur (avec jointure)
$stmt = $pdo->query("
  SELECT a.id, a.titre, a.contenu, a.date_creation, u.login AS auteur
  FROM articles a
  LEFT JOIN utilisateur u ON u.id = a.user_id
  ORDER BY a.date_creation DESC
  LIMIT 10
");
$articles = $stmt->fetchAll(); // Tableau d'articles pour l'affichage
?>
<h2>Derniers posts</h2>

<?php if (!$articles): ?>
  <!-- Message si aucun article n'est disponible -->
  <p>Aucun article pour l'instant.</p>
<?php endif; ?>

<?php foreach ($articles as $a): ?>
  <article class="card">
    <!-- Titre + lien vers la page de l'article -->
    <h2><a href="/public/article.php?id=<?= (int)$a['id'] ?>"><?= e($a['titre']) ?></a></h2>

    <!-- Métadonnées : auteur + date formatée -->
    <p class="muted">
      Par <a href="/public/author.php?login=<?= urlencode($a['auteur'] ?? 'inconnu') ?>"><?= e($a['auteur'] ?? 'Anonyme') ?></a>
      — <?= e(date('d/m/Y H:i', strtotime($a['date_creation']))) ?>
    </p>

    <!-- Extrait -->
    <p><?= e(excerpt($a['contenu'], 150)) ?></p>

    <!-- Lien pour lire l'article complet -->
    <p><a class="btn" href="/public/article.php?id=<?= (int)$a['id'] ?>">Lire la suite</a></p>
  </article>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../inc/footer.php';  ?>
