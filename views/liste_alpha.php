<?php 
include("../includes/header.php"); 
// Gestion des posts
if ( isset($_GET['what']) && isset($_GET['by']) )
	include("./page_alpha/alpha_".$_GET['what']."_by_".$_GET['by'].".php");

include("../includes/footer.php");

?>
