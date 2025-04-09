<?php
require_once MODELS_PATH . '/User.php';

$theme = 'light'; // Default
if (Session::isLoggedIn()) {
    $user_id = Session::getUserId();
    $userModel = new User();
    $preferences = $userModel->getUserPreferences($user_id);
    $theme = $preferences['theme'] ?? 'light';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?= ASSETS_URL ?>/img/favicon.ico" type="image/x-icon">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/main.css">
    <?php if (isset($pageStyles) && is_array($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/<?= $style ?>.css">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Page-specific CSS -->
    <?php if (strpos($_SERVER['REQUEST_URI'], '/login') !== false || strpos($_SERVER['REQUEST_URI'], '/register') !== false || strpos($_SERVER['REQUEST_URI'], '/reset-password') !== false): ?>
        <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/auth.css">
    <?php endif; ?>
    
    <!-- Meta -->
    <meta name="description" content="A simple and effective note management application">
    <meta name="author" content="Note Management App">
    
    <!-- PWA support -->
    <?php if (defined('ENABLE_OFFLINE_MODE') && ENABLE_OFFLINE_MODE): ?>
        <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
        <meta name="theme-color" content="#4a89dc">
    <?php endif; ?>
</head>
<body>
    <?php 
    // Notification for unverified accounts
    if (Session::isLoggedIn()): 
        $user = (new User())->getUserById(Session::getUserId());
        if ($user && !$user['is_activated']):
    ?>
    <div class="notification-banner">
        Your account is not verified. Please check your email to complete the activation process.
    </div>
    <?php 
        endif;
    endif;
    ?>
    
    <div class="container">