<?php
session_start();
if(!$_SESSION['login']) {
	header('Location: ../view/login.php');
	exit();
}