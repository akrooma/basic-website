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

	if (!user_logged_in()){
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
				$u = mysqli_real_escape_string($link, $_POST['username']);
				$p = mysqli_real_escape_string($link, $_POST['password']);

				$sql = "SELECT username, county, city, address, zip, type FROM arooma_users, arooma_userType WHERE arooma_userType.id = arooma_users.type_id AND username = '$u' AND password = SHA1('$p')";
				$result = mysqli_query($link, $sql) or die( $sql. " - ". mysqli_error($link));

				while($row = mysqli_fetch_assoc($result)){
					$user = $row;
				}

				if (empty($user)) {
					$logInErrors[] = "Kasutajanimi või parool olid valed!";
					include_once "views/logIn.html";
				} else {
					$_SESSION['user'] = array(
						'username' => $user['username'],
						'county' => $user['county'],
						'city' => $user['city'],
						'address' => $user['address'],
						'zip' => $user['zip'],
						'type' => $user['type']			
						);

					header("Location: controller.php?mode=logInSuccess");

				}

			} else {
				include_once "views/logIn.html";
			}

		} else {
			include_once "views/logIn.html";
		}

		include_once "views/footer.html";
		
	} else {
		header("Location: controller.php?mode=profile");	}
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
		} else if (strlen($_POST['username'])>10) {
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
				include_once "views/logIn.html";
			}
		}

	} else {
		include_once "views/register.html";
	}

	include_once "views/footer.html";
}

function display_addPicture(){
	if (admin_rights()) {
		include_once "views/header.html";

		if(!empty($_POST)){
			$errors = array();

			if(empty($_POST['alt'])){
				$errors[] = "Pildi alt atribuut jäi sisestamata!";
			}

			if (empty($errors)) {
				//Faili ülesse laadimise näitekood: http://www.w3schools.com/php/php_file_upload.asp

				$target_dir = "img/gallery/thumbnail/filler/";
				$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
				$uploadOk = true;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				if(isset($_POST["submit"])) {
				    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
				    if($check !== false) {
				        $errors[] = "Fail ei ole pilt - " . $check["mime"] . ".";
				        $uploadOk = true;
				    } else {
				        $errors[] = "Fail ei ole pilt.";
				        $uploadOk = false;
				    }
				}
				// Check if file already exists
				if ($target_file != $target_dir && file_exists($target_file)) {
				    $errors[] = "Anna andeks, see pilt juba eksisteerib.";
				    $uploadOk = false;
				}
				// Check file size
				if ($_FILES["fileToUpload"]["size"] > 500000) {
				    $errors[] = "Anna andeks, sinu fail on liiga massiivne.";
				    $uploadOk = false;
				}
				// Allow certain file formats
				$allowedExtensions = array("jpg", "jpeg", "png");
				if (!in_array($imageFileType, $allowedExtensions)) {
				    $errors[] = "Anna andeks, ainult JPG, JPEG, PNG failid on lubatud.";
				    $uploadOk = false;
				}
				// Check if $uploadOk is set to false by an error
				if ($uploadOk == false) {
				    $errors[] = "Anna andeks, sinu fail ei laetud ülesse.";
				// if everything is ok, try to upload file
				} else {
				    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				        echo "Fail ". basename( $_FILES["fileToUpload"]["name"]). " on ülesse laetud.";
				    } else {
				        $errors[] = "Anna andeks, faili ülesselaadimisel tekkis probleem.";
				    }
				}

				if (empty($errors)) {
					global $link;

					$alt = mysqli_real_escape_string($link, $_POST['alt']);
					$dsc = mysqli_real_escape_string($link, $_POST['description']);

					$sql = "INSERT INTO arooma_pictures (url, alt, description) VALUES ('$target_file', '$alt', '$dsc')";
					$result = mysqli_query($link, $sql);

					if ($result){
						include_once "views/addPicture.html";
					}
				} else {
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

function save_picture(){
	global $link;
	$id = mysqli_real_escape_string($link, $_POST['id']);
	$alt = mysqli_real_escape_string($link, $_POST['alt']);
	$dsc = mysqli_real_escape_string($link, $_POST['description']);

	$sql = "UPDATE arooma_pictures SET alt='$alt', description='$dsc' WHERE id=$id";
	var_dump($sql);
	$result = mysqli_query($link, $sql);

	if ($result){
		header("Location: controller.php?mode=galleries");
	}
}

function display_changePicture(){
	if (admin_rights()) {
		include_once "views/header.html";
		include_once "views/changePicture.html";
		include_once "views/footer.html";
	} else {
		display_profile();
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
	header("Location: controller.php?mode=logIn");
}

function in_multidim_array($array, $item){
	foreach ($array as $key) {
		if (in_array($item, $key)) {
			return true;
		}
	}
	return false;
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