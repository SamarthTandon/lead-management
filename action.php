<?php
if ((isset($_GET['action']) && !empty($_GET['action'])) && (isset($_GET['i']) && !empty($_GET['i']))) {
	require 'connection.php';
	$action = $_GET['action'];
	$pid = $_GET['i'];
	if ($action == "convert") {
		$sql = "UPDATE `prospects` SET `status`  = 'converted' WHERE `pid` = '$pid'";
		if ($conn->query($sql)) {
			header('Location: home.php');
			die();
		} else {
			header('Location: home.php');
			die();
		}
	}

	if ($action == "close") {
		$sql = "UPDATE `prospects` SET `status`  = 'closed' WHERE `pid` = '$pid'";
		if ($conn->query($sql)) {
			header('Location: home.php');
			die();
		} else {
			header('Location: home.php');
			die();
		}
	}

	if ($action == "open") {
		$sql = "UPDATE `prospects` SET `status`  = 'open' WHERE `pid` = '$pid'";
		if ($conn->query($sql)) {
			header('Location: home.php');
			die();
		} else {
			header('Location: home.php');
			die();
		}
	}
} else {
	header('Location: home.php');
	die();
}
