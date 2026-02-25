<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

auth_logout();
header('Location: ' . get_base_url() . '/');
exit;