<?php 
include("../includes/header.php"); 

echo "<div class ='recherche' id='liste_referer'>
	<p>Recherche de films par titre</p>";

	$tb_lettre = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	foreach ($tb_lettre as $key => $lettre) {
		$sth_lettre = $dbh->prepare("SELECT id_mov FROM movies WHERE mov_titre LIKE \"".$lettre."%\"");
		$sth_lettre->execute();
		$nb_lettre = $sth_lettre->rowCount();
		echo "<span><a href=\"../views/liste_movies_by_title.php?lettre=".lcfirst($lettre)."#movielist\">".$lettre."</a></span> (".$nb_lettre.")  - ";
		if ($key == 12) echo "<br />";
	}
echo "</div>";

include("../includes/footer.php");

?>
