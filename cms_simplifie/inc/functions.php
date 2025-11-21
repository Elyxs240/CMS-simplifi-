<?php
// Démarre la session si elle n'est pas déjà active 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Vrai si un utilisateur est connecté
function is_logged_in(): bool { return !empty($_SESSION['user_id']); }

// Renvoie l'ID de l'utilisateur connecté (ou null)
function current_user_id(): ?int { return $_SESSION['user_id'] ?? null; }

// Renvoie le login/pseudo de l'utilisateur (ou null)
function current_user_login(): ?string { return $_SESSION['login'] ?? null; }

// Renvoie le rôle courant (par défaut 'user')
function current_user_role(): string { return $_SESSION['role'] ?? 'user'; }

// Vrai si l'utilisateur courant est admin
function is_admin(): bool { return current_user_role() === 'admin'; }

// Protège une page : redirige vers /admin/login.php si non connecté
function require_login(): void { if (!is_logged_in()) { header('Location: /admin/login.php'); exit; } }

// Échappe le HTML pour éviter les attaque xss
function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Retourne un extrait limité à 150 caractères
function excerpt(string $c, int $l=150): string { $c=strip_tags($c); return mb_strlen($c)<= $l? $c: mb_substr($c,0,$l).'…'; }

// Génère/récupère le jeton CSRF stocké en session
function csrf_token(): string { if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token']=bin2hex(random_bytes(32)); } return $_SESSION['csrf_token']; }

// Champ caché à insérer dans les formulaires avec le jeton CSRF
function csrf_input(): string { return '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">'; }

// Vérifie le jeton CSRF sur les requêtes POST, sinon bloque avec 400
function verify_csrf(): void {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = $_POST['csrf'] ?? '';
    if (!$t || !hash_equals($_SESSION['csrf_token'] ?? '', $t)) {
      http_response_code(400);
      echo "<h1>Requête invalide</h1><p>Jeton CSRF manquant ou invalide.</p>";
      exit;
    }
  }
}
