<?php
include("../includes/header.php");
$id_actor = ( isset($_GET['id_actor']) ) ? $_GET['id_actor'] : -1;

/* TO DO : traiter la nullité du paramètre

FIN TO DO */

// Récupération des informations de l'acteur
$sth_act = $dbh->prepare("
	SELECT 	sa_resource
			,sa_nom
			,sa_naissance
	FROM 	success_actors 
	WHERE	id_success_a = ".$id_actor);
$sth_act->execute();
$actor = $sth_act->fetchAll();
if( !$actor ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Récupération des films de l'acteur
$sth_movies = $dbh->prepare("
	SELECT 		mov_resource
				,mov_titre
				,mov_annee
				,mov_is_success 
	FROM 		movies 
	WHERE		mov_id_actor = " .$id_actor ."
	ORDER BY	mov_annee DESC");
$sth_movies->execute();
$movies = $sth_movies->fetchAll();
if( !$movies ) { print_r($dbh->errorInfo()); echo "\n"; exit; }



echo "<h2>" .count($movies) . " films avec " . $actor[0]['sa_nom'] . ".</h2>";
echo "<table class='movies_table'>
        <tr><th>Films</th><th>Ann&eacute;e de sortie</th><th>Acteurs principaux</th></tr>
";

foreach($movies as $key => $tb) {
    echo "
    <tr>
        <td><a href=\"" . $__url_wiki . utf8_decode($tb['mov_resource']) . "\">";
		if ($tb['mov_is_success'] == 1) echo "<span style='color:red;font-weight:bold;'>";
		echo utf8_decode($tb['mov_titre']);
		if ($tb['mov_is_success'] == 1) echo "</span>";
		echo "</td>
        <td>" . $tb['mov_annee'] . "</td>
        <td><ul>";
		/*
		foreach($tb['acteurs'] as $act) {
			echo "<li> ". str_replace("_"," ",$act) ."</li>";
		}
		echo "
		</ul></td>
		*/
    echo "</tr>";   
}

echo "</table>";

include("../includes/footer.php");
?>
