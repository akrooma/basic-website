<?php
require_once("functions.php");
start_session();

$placeholderPictures=array(
  array("small"=>"img/gallery/thumbnail/filler/kindad1.jpg", "alt"=>"Katki", "desc" => "värviline"),
  array("small"=>"img/gallery/thumbnail/filler/kindad2.jpg", "alt"=>"Katki", "desc" => "minion oranž"),
  array("small"=>"img/gallery/thumbnail/filler/kindad3.jpg", "alt"=>"Katki", "desc" => "oranž zebra"),
  array("small"=>"img/gallery/thumbnail/filler/kindad4.jpg", "alt"=>"Katki", "desc" => "beež"),
  array("small"=>"img/gallery/thumbnail/filler/kindad5.jpg", "alt"=>"Katki", "desc" => "roosad roosid"),
  array("small"=>"img/gallery/thumbnail/filler/kindad6.jpg", "alt"=>"Katki", "desc" => "etno")
 );



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
        include_once "logInSuccess.html";
        include_once "views/footer.html";
        break;
    case "cart":
        display_cart();
        break;
}

?>