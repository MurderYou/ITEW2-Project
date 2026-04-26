<?php
session_start();

// Prevent browser from caching this response.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

// Clear session
$_SESSION = [];

// Invalidate the session cookie in the browser.
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'],
		$params['domain'],
		$params['secure'],
		$params['httponly']
	);
}

session_destroy();

// Redirect
header("Location: login.php?msg=Logged out", true, 302);
exit();
?>