<?php
// Inclusion des fichiers de config, BDD et fonctions 
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';

// Vérifie que l'utilisateur est connecté, sinon redirection
require_login();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Vérifie la validité du jeton CSRF pour éviter les attaques
  verify_csrf();

  // Récupération et nettoyage des champs du formulaire
  $titre = trim($_POST['titre'] ?? '');
  $contenu = trim($_POST['contenu'] ?? '');

  // Vérifie si les champs obligatoires sont remplis
  if ($titre === '' || $contenu === '') { 
    $errors[] = "Tous les champs sont obligatoires."; 
  }

  // Si pas d'erreurs, insertion de l'article dans la base
  if (!$errors) {
    $stmt = $pdo->prepare("INSERT INTO articles (user_id, titre, contenu, date_creation) VALUES (:uid, :titre, :contenu, NOW())");
    $stmt->execute([
      'uid'=>current_user_id(),   // ID de l'auteur connecté
      'titre'=>$titre,
      'contenu'=>$contenu
    ]);

    // Redirection vers le tableau de bord après publication
    header('Location: /admin/dashboard.php'); 
    exit;
  }
}

// Inclusion du header 
require_once __DIR__ . '/../inc/header.php';
?>

<h2>Écrire un article</h2>

<!-- Affiche les erreurs éventuelles -->
<?php if ($errors): ?>
  <div class="error">
    <?php foreach ($errors as $e): ?>
      <p><?= e($e) ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Formulaire de création d'article -->
<form method="post">
  <?= csrf_input() ?> <!-- Jeton CSRF caché -->

  <div class="form-group">
    <label>Titre</label>
    <input type="text" name="titre" value="<?= e($_POST['titre'] ?? '') ?>">
  </div>

  <div class="form-group">
    <label>Contenu</label>
    <textarea name="contenu"><?= e($_POST['contenu'] ?? '') ?></textarea>
  </div>

  <!-- Bouton publier et annuler -->
  <button class="btn primary" type="submit">Publier</button>
  <a class="btn" href="/admin/dashboard.php">Annuler</a>
</form>

<?php 
// Inclusion du footer 
require_once __DIR__ . '/../inc/footer.php'; 
?>
