<?php
include("commons.php");

function userAuthentication($mail, $pass, &$name) {
    $query = "SELECT users.* FROM users WHERE users.mail='$mail'";
    $row = doQuery($query)[0];
    $name = $row['name'];
	return (password_verify($pass, $row['pass']))? true:false;
}

function sessionClose($txt=null) {
	$_SESSION = array();
	session_destroy();
	global $url;
	global $msg;
	$url = '../index.php';
	$msg = ($txt)? $txt:'session_closed';
}

function createSession($id, $name) {
	session_regenerate_id(true);
	$_SESSION['userId'] = $id;
	$_SESSION['name'] = explode(" ", ucfirst($name))[0];
	$_SESSION['lastActivity'] = time();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

session_start();

$url = null;
$msg = null;
$op = sanitize($_REQUEST['op']);
$mail = sanitize($_POST['mail'], 'mail');
$pass = sanitize($_POST['pass'], 'pass');

switch($op) {
	case 'ss':
		$name = null;
		if (isset($_SESSION['userId'])) sessionClose("session_duplicity");
		elseif(userAuthentication($mail, $pass, $name)) {
			$url = '../main.php';
			$msg = "login";
			createSession($mail, $name);
		} else {
			$url = '../index.php';
			$msg = 'auth_error';
		}		
		break;
	
	case 'sc':
		if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
			// Aquí se podría generar una alerta al administrador
		    sessionClose("session_token");
		}
		sessionClose();
		break;

	case 'ur':
		$name = sanitize($_POST['name']);
	    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (mail, pass, name) VALUES ('$mail', '$hashed_pass', '$name')";
	    $result = doQuery($query);
		$url = '../index.php';
		$msg = ($result > 0)? 'reg_success':'reg_error';
		break;
		
	default:
		sessionClose("unexpected_error");
		break;
}

gotoURL($url, $msg);
?>
