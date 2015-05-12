<?php
require_once("database_connection.php");
require_once("functions.php");

start_session();

$mode="";

If(!empty($_GET["mode"])) {
	$mode=$_GET["mode"];
}

switch ($mode) {
    default:
        display_firstPage();
        break;
    case "aboutUs":
    	display_aboutUs();
        break;
    case "galleries":
    	display_galleries();
        break;
    case "newsletter":
    	display_newsletter();
        break;
    case "FAQ":
    	display_FAQ();
        break;
    case "logIn":
    	display_logIn();
        break;
    case "logOut":
        logOut();
        break;
    case "logInSuccess":
        include_once "views/header.html";
        include_once "views/logInSuccess.html";
        include_once "views/footer.html";
        break;
    case "cart":
        display_cart();
        break;
    case "register":
        display_register();
        break;
    case "addPicture":
        display_addPicture();
        break;
    case "userList";
        display_userList();
        break;
    case "profile":
        display_profile();
        break;
    case "changePicture":
        display_changePicture();
        break;
    case "savePicture":
        save_picture();
        break;
}

?>