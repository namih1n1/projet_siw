<?php 
include("../includes/header.php"); 

echo "<div class ='recherche'>
	<p>Recherche de films par titre</p>";

	$tb_lettre = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	foreach ($tb_lettre as $key => $lettre) {
		echo "<span><a href=\"../views/liste_movies_by_title.php?lettre=".lcfirst($lettre)."\">".$lettre."</a></span>  - ";
	}
echo "</div>";

include("../includes/footer.php");

?>
