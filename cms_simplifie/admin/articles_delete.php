<?php
// Charge la config, la connexion BDD et les fonctions 
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';

// Accès réservé aux utilisateurs connectés
require_login();

// Récupère l'ID de l'article depuis l'URL et le valide
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /admin/dashboard.php'); exit; }

// Charge les infos de l'article (pour vérifier droits + afficher le titre)
$stmt = $pdo->prepare("SELECT id, user_id, titre FROM articles WHERE id = :id");
$stmt->execute(['id'=>$id]);
$article = $stmt->fetch();
if (!$article) { header('Location: /admin/dashboard.php'); exit; }

// Contrôle d'autorisation : admin ou auteur du post
if (!is_admin() && (int)$article['user_id'] !== current_user_id()) {
  http_response_code(403); // interdit
  echo "<div class='card error'><p>Accès refusé.</p></div>";
  exit;
}

// Traitement du formulaire de confirmation (méthode POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf(); // vérifie le jeton CSRF

  // Si l'utilisateur confirme, on supprime l'article
  if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $del = $pdo->prepare("DELETE FROM articles WHERE id = :id");
    $del->execute(['id'=>$id]);
  }

  // Retour au tableau de bord dans tous les cas
  header('Location: /admin/dashboard.php'); 
  exit;
}

// Affichage
require_once __DIR__ . '/../inc/header.php';
?>
<h2>Supprimer l'article</h2>
<div class="card">
  <!-- Rappel du titre pour confirmation -->
  <p>Supprimer <strong><?= e($article['titre']) ?></strong> ?</p>

  <!-- Formulaire de confirmation (protégé CSRF) -->
  <form method="post" style="display:inline">
    <?= csrf_input() ?> <!-- jeton CSRF -->
    <input type="hidden" name="confirm" value="yes">
    <button class="btn danger" type="submit">Oui, supprimer</button>
  </form>

  <!-- Lien d'annulation -->
  <a class="btn" href="/admin/dashboard.php">Annuler</a>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>
