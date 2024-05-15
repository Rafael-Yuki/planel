<?php
session_start();
if(!$_SESSION['login']) {
	header('Location: ../view/index.php');
	exit();
}