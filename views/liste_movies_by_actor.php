<?php
include("../includes/header.php");
$actor_id = ( isset($_GET['id_actor']) ) ? ucwords($_GET['id_actor']) : null;
// TO DO : si actor_id = null, redirection vers liste des acteurs

// Récupération acteur
$act = $dbh->prepare("SELECT act_nom FROM actors WHERE id_act = ".$actor_id);
$act->execute();
$acteur = $act->fetchAll();

// Recherche des films de l'acteur
$mov_of_act = $dbh->prepare("
	SELECT 	m.mov_resource 	as mov_resource, 
			m.mov_titre		as mov_titre,
			m.mov_annee		as mov_annee,
			m.mov_url_image	as mov_url_image
	FROM 	link_movies_actors lma, 
			movies m
	WHERE 	lma.id_act = " .$actor_id."
	AND		lma.id_mov = m.id_mov
	ORDER BY m.mov_annee DESC
");
$mov_of_act->execute();
$list_mov = $mov_of_act->fetchAll();

// Parcours
if( !$list_mov ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

echo "<div class = 'tabletitle' id='movie_actor'><h2>Les " .count($list_mov) . " films de " . utf8_decode($acteur[0]['act_nom']) . ".</h2></div>";
echo "<table class='mov_act_table'>
		<thead>
			<tr><th>Film</th><th>Image</th><th>Ann&eacute;e</th></tr>
		</thead>
		<tbody>
";

// Parcours des films
foreach($list_mov as $key => $tb) {
	$traited_titre = utf8_decode($tb['mov_titre']);
	if (strpos($traited_titre,"(film") != 0)
		$traited_titre = trim(substr($traited_titre,0,strpos($traited_titre,"(film")));
    echo "
			<tr>
				<td><a href=\"" . $__url_wiki . utf8_decode($tb['mov_resource']) . "\">" . $traited_titre . "</a></td>
				<td><img src=\"". utf8_decode($tb['mov_url_image']) . "\" width='150px' height='150px'/></td>
				<td>" . $tb['mov_annee'] . "</td>
			</tr>";   
}

echo "	</tbody>
	</table>";
unset($list_mov);

include("../includes/footer.php");
?>
