<?php
// Charge la config, la connexion BDD et les fonctions 
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';

$errors = []; // Contiendra les messages d'erreur à afficher
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf(); // Sécurise le formulaire contre les attaques CSRF

  // Récupère et nettoie les identifiants saisis
  $loginOrEmail = trim($_POST['login'] ?? '');
  $password = $_POST['password'] ?? '';

  // Validation des champs requis
  if ($loginOrEmail === '' || $password === '') {
    $errors[] = "Veuillez renseigner vos identifiants.";
  } else {
    // Recherche de l'utilisateur par login ou email (requête préparée sécurisée)
    $stmt = $pdo->prepare("SELECT id, login, password, role FROM utilisateur WHERE login = :v OR email = :v LIMIT 1");
    $stmt->execute(['v'=>$loginOrEmail]);
    $user = $stmt->fetch();

    // Vérifie le mot de passe 
    if ($user && password_verify($password, $user['password'])) {
      

      // Sauvegarde l'état de connexion en session
      $_SESSION['user_id'] = (int)$user['id'];
      $_SESSION['login']   = $user['login'];
      $_SESSION['role']    = $user['role'] ?: 'user';

      // Redirige vers le tableau de bord une fois connecté
      header('Location: /admin/dashboard.php'); exit;
    } else {
      // Message générique pour ne pas révéler si login/email existe
      $errors[] = "Identifiants incorrects.";
    }
  }
}

// header
require_once __DIR__ . '/../inc/header.php';
?>
<h2>Connexion</h2>

<?php if ($errors): ?>
  <div class="error">
    <?php foreach ($errors as $e): ?>
      <p><?= e($e) ?></p> <!-- Affiche les erreurs de manière échappée -->
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Formulaire de connexion protégé par CSRF -->
<form method="post">
  <?= csrf_input() ?> <!-- Champ caché contenant le jeton CSRF -->

  <div class="form-group">
    <label>Login ou Email</label>
    <!-- Conserve la saisie utilisateur en cas d'erreur -->
    <input type="text" name="login" value="<?= e($_POST['login'] ?? '') ?>">
  </div>

  <div class="form-group">
    <label>Mot de passe</label>
    <input type="password" name="password">
  </div>

  <button class="btn primary" type="submit">Se connecter</button>
  <a class="btn" href="/admin/register.php">Créer un compte</a>
</form>

<?php
// footer
require_once __DIR__ . '/../inc/footer.php';
?>
