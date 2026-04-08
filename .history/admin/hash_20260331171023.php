var_dump($user['password']);
var_dump(password_verify('admin123', $user['password']));
exit;