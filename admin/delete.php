<?php
if ((isset($_GET['u']) && !empty($_GET['u'])) || (isset($_GET['i']) && !empty($_GET['i']))) {
	require '../connection.php';
	if (isset($_GET['u'])) {
		$uid = $_GET['u'];
		$sql = "DELETE FROM `users` WHERE `uid` = '$uid'";
		if ($conn->query($sql)) {
			header('Location: home.php');
			die();
		} else {
			header('Location: home.php');
			die();
		}
	}

	if (isset($_GET['i'])) {
		$pid = $_GET['i'];
		$sql = "DELETE FROM `prospects` WHERE `pid` = '$pid'";
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
