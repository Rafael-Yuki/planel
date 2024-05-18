<?php
session_start();
if(!$_SESSION['login']) {
	header('Location: ../views/login.php');
	exit();
}