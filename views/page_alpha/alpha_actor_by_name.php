<?php 
$sth_lettre = $dbh->prepare("
	SELECT LEFT(act_nom,1) as lettre, COUNT(*) as nombre FROM `actors` GROUP BY LEFT(act_nom,1)
");
$sth_lettre->execute();
$lettre = $sth_lettre->fetchAll();

echo create_liste_alpha($lettre,"actor","name");

?>
