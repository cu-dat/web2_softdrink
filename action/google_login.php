<?php

$client_id = "1023332032947-dhs8lm2uja5795ptn1is1u9jg32o319v.apps.googleusercontent.com";

$redirect_uri = "http://localhost/web2_softdrink/action/google-callback.php";

$url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'online',
    'prompt' => 'select_account'
]);

header("Location: $url");
exit;
