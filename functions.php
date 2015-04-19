<?php

function display_firstPage(){
	include_once "views/header.html";
	include_once "firstPage.html";
	include_once "views/footer.html";
}

function display_aboutUs(){
	include_once "views/header.html";
	include_once "aboutUs.html";
	include_once "views/footer.html";
}

function display_galleries(){
	global $placeholderPictures;
	$missing = false;

	include_once "views/header.html";

	if (!empty($_POST)) {
		$find = $_POST['picture'];
		$info = array();

		if (empty($find)) {
			$missing = true;
		} else {
			foreach($placeholderPictures as $key => $picture){
				if (strpos($picture["desc"], $find) !== false) {
    				$info[]=$picture["small"];
				} 
			}
		}

		include_once "galleryDirectory.html";

	} else {
		include_once "galleryDirectory.html";
	}

	include_once "views/footer.html";
}

function display_newsletter(){
	include_once "views/header.html";
	include_once "newsletter.html";
	include_once "views/footer.html";
}

function display_FAQ(){
	include_once "views/header.html";
	include_once "FAQ.html";
	include_once "views/footer.html";
}

function display_logIn(){
	include_once "views/header.html";

	if(!empty($_POST)){
		$testName = "kasutaja";
		$testPW = "parool";

		$logInErrors = array();
		
		if(!empty($_POST['username'])) {
			$username = $_POST['username'];
		} else {
			$logInErrors[]="Kasutaja nimi on puudu!";		
		}
		
		if(!empty($_POST['password'])) {
			$password = $_POST['password'];
		} else {
			$logInErrors[]="Parool on puudu!";	
		}
		
		if (empty($logInErrors)) {
			if($username == $testName && $password == $testPW) {
				$_SESSION['user'] = array(
					'username' => $username,
					'userID' => 0			
				);

				header("Location: controller.php?mode=logInSuccess");
				exit(0);
			} else {
				$logInErrors[] = "Kasutajanimi või parool olid valed!";
				include_once "logIn.html";
			}	
		} else {
			include_once "logIn.html";
		}

	} else {
		include_once "logIn.html";
	}

	include_once "views/footer.html";

	//var_dump($_SESSION['user']);
}

function display_cart(){
	include_once "views/header.html";
    include_once "cart.html";
    include_once "views/footer.html";
}

function logOut(){
	destroy_session();
	include_once "views/header.html";
	include_once "logOutSuccess.html";
	include_once "views/footer.html";
}

function user_logged_in(){
	if(isset($_SESSION['user'])){
		return true;
	} else {
		return false;
	}
}

function start_session(){
	session_start();
}

function destroy_session(){
	$_SESSION = array();

	if (isset($_COOKIE['user'])) {
 		setcookie('user', '', time()-42000, '/');
	}

	session_destroy();
}




?>