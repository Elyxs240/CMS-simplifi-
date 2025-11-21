<?php
// Charge la config, la connexion BDD et les fonctions
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';

// Restreint l'accès au tableau de bord aux utilisateurs connectés
require_login();

// Affiche le header 
require_once __DIR__ . '/../inc/header.php';

// Calcule le nombre d'articles : tous si admin, sinon seulement ceux de l'utilisateur
if (is_admin()) {
  $countArticles = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
} else {
  $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE user_id = :uid");
  $stmtCount->execute(['uid'=>current_user_id()]);
  $countArticles = (int)$stmtCount->fetchColumn();
}
?>


<h2>Mon espace</h2>
<div class="notice">Bienvenue, <?= e(current_user_login()) ?> </div>
<p>Vos articles : <strong><?= $countArticles ?></strong></p>

<p>
  <!-- Lien pour créer un nouvel article -->
  <a class="btn primary" href="/admin/articles_create.php">Écrire un nouvel article</a>
  <!-- Lien pour revenir à la partie publique du site -->
  <a class="btn" href="/public/index.php">Retourner a l'accueil</a>
</p>

<table class="table">
  <thead>
    <tr>
      <th>ID</th><th>Titre</th><th>Date</th><th>Auteur</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php
    // Récupère la liste d'articles à afficher : tous si admin, sinon filtrés par user_id
    if (is_admin()) {
      $stmt = $pdo->query("
        SELECT a.id, a.titre, a.date_creation, u.login AS auteur
        FROM articles a
        LEFT JOIN utilisateur u ON u.id = a.user_id
        ORDER BY a.date_creation DESC
      ");
    } else {
      $stmt = $pdo->prepare("
        SELECT a.id, a.titre, a.date_creation, u.login AS auteur
        FROM articles a
        LEFT JOIN utilisateur u ON u.id = a.user_id
        WHERE a.user_id = :uid
        ORDER BY a.date_creation DESC
      ");
      $stmt->execute(['uid'=>current_user_id()]);
    }

    // Affiche chaque article dans une ligne du tableau (échappé pour éviter les attaques par xss)
    foreach ($stmt as $row): ?>
    <tr>
      <td><?= (int)$row['id'] ?></td>
      <td><?= e($row['titre']) ?></td>
      <td><?= e(date('d/m/Y H:i', strtotime($row['date_creation']))) ?></td>
      <td><?= e($row['auteur'] ?? 'Anonyme') ?></td>
      <td>
        <!-- Actions d'édition/suppression sur l'article courant -->
        <a class="btn" href="/admin/articles_edit.php?id=<?= (int)$row['id'] ?>">Modifier</a>
        <a class="btn danger" href="/admin/articles_delete.php?id=<?= (int)$row['id'] ?>">Supprimer</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php
// Footer 
require_once __DIR__ . '/../inc/footer.php';
?>
