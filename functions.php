<?php

function display_firstPage(){
	include_once "views/header.html";
	include_once "views/firstPage.html";
	include_once "views/footer.html";
}

function display_aboutUs(){
	include_once "views/header.html";
	include_once "views/aboutUs.html";
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

		include_once "views/galleryDirectory.html";

	} else {
		include_once "views/galleryDirectory.html";
	}

	include_once "views/footer.html";
}

function display_newsletter(){
	include_once "views/header.html";
	include_once "views/newsletter.html";
	include_once "views/footer.html";
}

function display_FAQ(){
	include_once "views/header.html";
	include_once "views/FAQ.html";
	include_once "views/footer.html";
}

function display_logIn(){
	global $link;
	global $registerErrors;

	include_once "views/header.html";

	if(!empty($_POST)){
		$users = array();
		$logInErrors = array();

		if(empty($_POST['username'])) {
			$logInErrors[]="Kasutajanimi on puudu!";		
		}
		
		if(empty($_POST['password'])) {
			$logInErrors[]="Parool on puudu!";	
		}
		
		if (empty($logInErrors)) {
			$u = $_POST['username'];
			$p = $_POST['password'];

			$sql = "SELECT username, county, city, address, zip, type FROM arooma_users, arooma_userType WHERE arooma_userType.id = arooma_users.type_id AND username = '$u' AND password = SHA1('$p')";
			$result = mysqli_query($link, $sql) or die( $sql. " - ". mysqli_error($link));

			while($row = mysqli_fetch_assoc($result)){
				$user[] = $row;
			}

			if (empty($user)) {
				$logInErrors[] = "Kasutajanimi või parool olid valed!";
				include_once "views/logIn.html";
			} else {
				foreach ($user as $key) {
					$_SESSION['user'] = array(
						'username' => $key['username'],
						'county' => $key['county'],
						'city' => $key['city'],
						'address' => $key['address'],
						'zip' => $key['zip'],
						'type' => $key['type']			
						);

					header("Location: controller.php?mode=logInSuccess");
					exit(0);
				}
				
			}

		} else {
			include_once "views/logIn.html";
		}

	} else {
		include_once "views/logIn.html";
	}

	include_once "views/footer.html";

	//var_dump($registerErrors);
	//print_r($user);
}

function display_cart(){
	include_once "views/header.html";

	if(user_logged_in()){
		include_once "views/cart.html";
	} else {
		include_once "views/logIn.html";
	}
    
    include_once "views/footer.html";
}

function display_register(){
	global $registerErrors;
	global $link;
	
	include_once "views/header.html";

	if (!empty($_POST)) {

		$registerErrors = array();
		$userNames = array();

		$sql = "SELECT username FROM arooma_users";
		$result = mysqli_query($link, $sql) or die( $sql. " - ". mysqli_error($link));

		while($row = mysqli_fetch_assoc($result)){
			$userNames[]=$row;
		}

		if(empty($_POST['username'])) {
			$registerErrors[] = "Kasutajanimi jäi sisestamata!";
		} else if (strlen($_POST['password'])>10) {
			$registerErrors[] = "Kasutajanimi on liiga pikk! Maksimum pikkus 10 märki.";
		}

		if (empty($_POST['password'])) {
			$registerErrors[] = "Parool jäi sisestamata!";
		} else if (strlen($_POST['password'])>50) {
			$registerErrors[] = "Parool on liiga pikk! Maksimum pikkus 50 märki.";
		} else {
			if (empty($_POST['passwordRe'])) {
				$registerErrors[] = "Parool jäi uuesti sisestamata.";
			} else if ($_POST['password'] != $_POST['passwordRe']) {
				$registerErrors[] = "Paroolid ei kattu!";
			}
		}

		if (in_multidim_array($userNames, $_POST['username'])) {
			$registerErrors[] = "Kasutajanimi {$_POST['username']} on juba olemas!";
		}

		if (!empty($registerErrors)) {
			include_once "views/register.html";
		} else {
			$username = mysqli_real_escape_string($link, $_POST['username']);
			$password = mysqli_real_escape_string($link, $_POST['password']);
			$type = 2;

			$sql = "INSERT INTO arooma_users (username, password, type_id) VALUES ('$username', SHA1('$password'), $type)";
			$result = mysqli_query($link, $sql);

			if ($result){
				$registerErrors[] = "Registreerimine õnnestus!";
				header("Location: controller.php?mode=logIn");
				//Kui kasutada headeri pläusti, siis url on "korrektne", aga $registerErrors jääb tühjaks. 
				//Siiski oleks vaja, et seal oleks "registreerimine õnnestus".
				//Probleem probabli selles, et nupu vajutamisel läheb inf mode=register peale ja ei saada seda mode=login peale.

				//include_once "views/logIn.html";
				//include once puhul jääb url n-ö valeks, e url mode on register, mitte log in. Samas sisuvärk on log in mant.
			}
		}

	} else {
		include_once "views/register.html";
	}

	include_once "views/footer.html";

	//var_dump(in_array($_POST['username'], $userNames));
	//var_dump(in_multidim_array($userNames, $_POST['username']));
}

function display_addPicture(){
	if (admin_rights()) {
		include_once "views/header.html";

		if(!empty($_POST)){
			$errors = array();

			if(empty($_POST['source'])){
				$errors[] = "Pildi url jäi sisestamata!";
			}

			if(empty($_POST['alternative'])){
				$errors[] = "Pildi alt atribuut jäi sisestamata!";
			}

			if (empty($errors)) {
				$source = $_POST['source'];

				$allowedExtensions = array("jpg", "jpeg", "png");
				$extension = end(explode(".", $source));

				if (in_array($extension, $allowedExtensions)) {
					global $link;

					$src = mysqli_real_escape_string($link, $_POST['source']);
					$alt = mysqli_real_escape_string($link, $_POST['alternative']);
					$dsc = mysqli_real_escape_string($link, $_POST['description']);

					$sql = "INSERT INTO arooma_pictures (url, alt, description) VALUES ('$src', '$alt', '$dsc')";
					$result = mysqli_query($link, $sql);

					if ($result){
						include_once "views/galleryDirectory.html";
					}

				} else {
					$errors[] = "Lubatud on ainult jpg, jpeg ja png faililaiendiga pildid!";
					include_once "views/addPicture.html";
				}

			} else {
				include_once "views/addPicture.html";
			}

			

		} else {
			include_once "views/addPicture.html";	
		}

		include_once "views/footer.html";

	} else {
		display_logIn();
	}
}

function display_userList(){
	if (admin_rights()) {
		include_once "views/header.html";
		include_once "views/userList.html";
		include_once "views/footer.html";
	} else {
		display_profile();
	}
}

function display_profile(){
	if (user_logged_in()) {
		include_once "views/header.html";
		include_once "views/profile.html";
		include_once "views/footer.html";
	} else {
		display_logIn();
	}
}

function logOut(){
	destroy_session();
	include_once "views/header.html";
	include_once "views/logOutSuccess.html";
	include_once "views/footer.html";
}



function in_multidim_array($array, $item){
	foreach ($array as $key) {
		if (in_array($item, $key)) {
			return true;
		}
	}
	return false;
}

function db_connection(){
	global $link;
	$user = "test";
	$pass = "t3st3r123";
	$db = "test";
	$host = "localhost";

	$link = mysqli_connect($host, $user, $pass, $db) or die("ei saanud ühendatud - ");
	mysqli_query($link, "SET CHARACTER SET UTF8")or die( $sql. " - ". mysqli_error($link));
}

function user_logged_in(){
	if(isset($_SESSION['user'])){
		return true;
	} else {
		return false;
	}
}

function admin_rights(){
	if (user_logged_in() && $_SESSION['user']['type'] == 'admin') {
		return true;
	}
	return false;
}

function start_session(){
	session_start();
	db_connection();
}

function destroy_session(){
	$_SESSION = array();

	if (isset($_COOKIE['user'])) {
 		setcookie('user', '', time()-42000, '/');
	}

	session_destroy();
}




?>