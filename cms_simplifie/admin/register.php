<?php
// Charge la config, la connexion BDD et les fonction
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';

$errors = []; // Collecte des messages d'erreur

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf(); // Protection CSRF du formulaire

  // Récupération et on vide les champs
  $login = trim($_POST['login'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $password2 = $_POST['password2'] ?? '';

  // Validations de base
  if ($login === '' || $email === '' || $password === '' || $password2 === '') { $errors[] = "Tous les champs sont obligatoires."; }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Email invalide."; }
  if ($password !== $password2) { $errors[] = "Les mots de passe ne correspondent pas."; }

  // Si valide, vérifie login/email puis crée l'utilisateur
  if (!$errors) {
    $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE login = :login OR email = :email LIMIT 1");
    $stmt->execute(['login'=>$login,'email'=>$email]);

    if ($stmt->fetch()) {
      $errors[] = "Login ou email déjà pris."; // Conflit
    } else {
      // Hash sécurisé du mot de passe
      $hash = password_hash($password, PASSWORD_DEFAULT);

      // Insertion de l'utilisateur en base (rôle par défaut: user)
      $ins = $pdo->prepare("INSERT INTO utilisateur (login, email, password, role, created_at) VALUES (:login, :email, :password, 'user', NOW())");
      $ins->execute(['login'=>$login,'email'=>$email,'password'=>$hash]);

      // Connexion automatique après inscription
      $_SESSION['user_id'] = (int)$pdo->lastInsertId();
      $_SESSION['login']   = $login;
      $_SESSION['role']    = 'user';
      

      // Redirection vers le tableau de bord
      header('Location: /admin/dashboard.php'); exit;
    }
  }
}

// Header 
require_once __DIR__ . '/../inc/header.php';
?>
<h2>Inscription</h2>

<!-- Affichage des erreurs éventuelles -->
<?php if ($errors): ?>
  <div class="error">
    <?php foreach ($errors as $e): ?>
      <p><?= e($e) ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Formulaire d'inscription protégé par CSRF -->
<form method="post">
  <?= csrf_input() ?> <!-- Jeton CSRF -->

  <div class="form-group">
    <label>Login</label>
    <input type="text" name="login" value="<?= e($_POST['login'] ?? '') ?>">
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>">
  </div>

  <div class="form-group">
    <label>Mot de passe</label>
    <input type="password" name="password">
  </div>

  <div class="form-group">
    <label>Confirmer le mot de passe</label>
    <input type="password" name="password2">
  </div>

  <button class="btn primary" type="submit">Créer mon compte</button>
  <a class="btn" href="/admin/login.php">J'ai déjà un compte</a>
</form>

<?php
// Footer 
require_once __DIR__ . '/../inc/footer.php';
?>
