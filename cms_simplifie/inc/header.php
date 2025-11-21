<?php
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e(APP_NAME) ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container">
    <h1 class="logo"><a href="/public/index.php"><?= e(APP_NAME) ?></a></h1>
    <nav>
      <a href="/public/index.php">Accueil</a>
      <?php if (is_logged_in()): ?>
        <a href="/admin/dashboard.php">Mon espace</a>
        <a href="/admin/logout.php">Déconnexion (<?= e(current_user_login()) ?>)</a>
      <?php else: ?>
        <a href="/admin/login.php">Connexion</a>
        <a href="/admin/register.php">Inscription</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
