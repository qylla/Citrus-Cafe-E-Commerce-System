<?php
// Creates an admin account. Run from a terminal:
// php database/create_admin.php "Full Name" admin@example.com 0123456789
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../dao/UserDAO.php';

if ($argc < 4) {
    fwrite(STDERR, "Usage: php database/create_admin.php \"Full Name\" email phone [password]\n");
    exit(1);
}

$password = $argv[4] ?? '';
if ($password === '') {
    echo 'Password: ';
    $password = trim((string)fgets(STDIN));
}

if (strlen($password) < 8) {
    fwrite(STDERR, "Password must have at least 8 characters.\n");
    exit(1);
}

$result = (new UserDAO())->register($argv[1], $argv[2], $argv[3], $password, 'admin');

if ($result === true) {
    echo "Admin account created.\n";
    exit(0);
}

fwrite(STDERR, $result === 'exists' ? "That email already has an account.\n" : "Could not create the account.\n");
exit(1);
