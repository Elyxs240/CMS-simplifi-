<?php
// Charge la configuration, la connexion BDD et les fonctions
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';

// Restreint l'accès : uniquement pour utilisateurs connectés
require_login();

// Récupère et valide l'ID de l'article passé en GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /admin/dashboard.php'); exit; }

// Récupère l'article ciblé 
$stmt = $pdo->prepare("SELECT id, user_id, titre, contenu FROM articles WHERE id = :id");
$stmt->execute(['id'=>$id]);
$article = $stmt->fetch();
if (!$article) { header('Location: /admin/dashboard.php'); exit; }

// Vérifie les droits : admin ou auteur de l'article
if (!is_admin() && (int)$article['user_id'] !== current_user_id()) {
  http_response_code(403); // interdit
  echo "<div class='card error'><p>Accès refusé.</p></div>";
  exit;
}

// Gestion du formulaire d'édition
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf(); // protège contre les attaques CSRF

  // Nettoyage et validation des champs
  $titre = trim($_POST['titre'] ?? '');
  $contenu = trim($_POST['contenu'] ?? '');
  if ($titre === '' || $contenu === '') { $errors[] = "Tous les champs sont obligatoires."; }

  // Si ok, mise à jour de l'article en BDD
  if (!$errors) {
    $stmt = $pdo->prepare("UPDATE articles SET titre=:titre, contenu=:contenu WHERE id=:id");
    $stmt->execute(['titre'=>$titre,'contenu'=>$contenu,'id'=>$id]);

    // Retour au tableau de bord après sauvegarde
    header('Location: /admin/dashboard.php'); 
    exit;
  }
}

// Affichage de la page (header + formulaire + footer)
require_once __DIR__ . '/../inc/header.php';
?>
<h2>Modifier l'article #<?= (int)$article['id'] ?></h2>

<?php if ($errors): ?>
  <div class="error">
    <?php foreach ($errors as $e): ?>
      <p><?= e($e) ?></p> <!-- messages d'erreur échappés -->
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="post">
  <?= csrf_input() ?> <!-- champ caché CSRF -->

  <div class="form-group">
    <label>Titre</label>
    <!-- Pré-remplit avec la valeur existante -->
    <input type="text" name="titre" value="<?= e($_POST['titre'] ?? $article['titre']) ?>">
  </div>

  <div class="form-group">
    <label>Contenu</label>
    <!-- Pré-remplit avec la saisie en cours ou la valeur existante -->
    <textarea name="contenu"><?= e($_POST['contenu'] ?? $article['contenu']) ?></textarea>
  </div>

  <!-- Actions -->
  <button class="btn primary" type="submit">Enregistrer</button>
  <a class="btn" href="/admin/dashboard.php">Annuler</a>
</form>


<?php require_once __DIR__ . '/../inc/footer.php';  ?>
