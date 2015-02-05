<?php 
$sth_lettre = $dbh->prepare("
	SELECT 	tb.titre as lettre, SUM(tb.nb) as nombre
	FROM (
		(SELECT LEFT(dm_titre,1) as titre,  COUNT(*) as nb FROM `disney_movies` GROUP BY LEFT(dm_titre,1))
		UNION
		(SELECT LEFT(mov_titre,1) as titre,  COUNT(*) as nb FROM `movies` GROUP BY LEFT(mov_titre,1))
	) AS tb
	GROUP BY tb.titre
");
$sth_lettre->execute();
$lettre = $sth_lettre->fetchAll();

echo create_liste_alpha($lettre,"actor","movie");

?>
