<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: /dashboard.php");
        exit();
    }
}

function formatCurrency($value) {
    return number_format($value, 2, ',', '.') . ' CVE';
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function showAlert($message, $type = 'info') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<div class='alert alert-{$alert['type']}' id='alert'>
                <span>{$alert['message']}</span>
                <button onclick='closeAlert()' class='alert-close'>&times;</button>
              </div>";
        unset($_SESSION['alert']);
    }
}

function getStatusBadge($status) {
    $badges = [
        'pendente' => 'badge-warning',
        'processando' => 'badge-info',
        'concluido' => 'badge-success',
        'cancelado' => 'badge-danger'
    ];
    
    $class = $badges[$status] ?? 'badge-secondary';
    return "<span class='badge {$class}'>" . ucfirst($status) . "</span>";
}
?>