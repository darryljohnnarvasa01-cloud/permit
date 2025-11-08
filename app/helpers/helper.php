<?php
/**
 * Global Helper Functions
 */

function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function base_url($path = '') {
    $base = env('APP_URL', 'http://localhost/permit');
    return $base . '/' . ltrim($path, '/');
}

function asset($path) {
    return base_url('public/' . ltrim($path, '/'));
}

function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function dd(...$vars) {
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

function formatDate($date, $format = 'F d, Y') {
    return date($format, strtotime($date));
}

function formatMoney($amount) {
    return '₱' . number_format($amount, 2);
}

function generatePermitNumber($prefix = 'VC-MPR') {
    return $prefix . '-' . date('Y') . '-' . strtoupper(substr(uniqid(), -8));
}

function uploadFile($file, $directory) {
    $uploadDir = __DIR__ . '/../../public/uploads/' . $directory . '/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $directory . '/' . $fileName;
    }
    
    return false;
}

function deleteFile($path) {
    $fullPath = __DIR__ . '/../../public/uploads/' . $path;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Pending</span>',
        'approved' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Approved</span>',
        'rejected' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">Rejected</span>',
        'expired' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">Expired</span>',
        'active' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Active</span>',
        'inactive' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">Inactive</span>',
        'paid' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Paid</span>',
        'unpaid' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">Unpaid</span>',
        'partial' => '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Partial</span>',
    ];
    
    return $badges[$status] ?? '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' . ucfirst($status) . '</span>';
}

function success($message) {
    Session::flash('success', $message);
}

function error($message) {
    Session::flash('error', $message);
}

function info($message) {
    Session::flash('info', $message);
}

function csrf_token() {
    if (!Session::has('csrf_token')) {
        Session::set('csrf_token', bin2hex(random_bytes(32)));
    }
    return Session::get('csrf_token');
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals(csrf_token(), $token);
}
