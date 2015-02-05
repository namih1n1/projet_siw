<?php 
$sth_lettre = $dbh->prepare("
	SELECT LEFT(dir_nom,1) as lettre, COUNT(*) as nombre FROM `directors` GROUP BY LEFT(dir_nom,1)
");
$sth_lettre->execute();
$lettre = $sth_lettre->fetchAll();

echo create_liste_alpha($lettre,"movie","director");

?>
