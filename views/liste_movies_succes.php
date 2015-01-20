<?php
include("../includes/header.php");

// Récupération des films à succès de la base
$sth = $dbh->prepare("SELECT * FROM movies WHERE mov_is_success = 1");
$sth->execute();
$result = $sth->fetchAll();

if( !$result ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Récupération de l'année la plus récente des films répertoriés
$sth_last = $dbh->prepare("SELECT MAX(mov_annee) as last FROM movies WHERE mov_is_success = 1");
$sth_last->execute();
$last_annee = $sth_last->fetchAll();
if( !$last_annee ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

echo "<div class = 'tabletitle' id='movie_success'><h2>Les " .count($result) . " films du box-office mondial, jusqu'&agrave; " . $last_annee[0]['last'] . ".</h2></div>";
echo "<table class='boxoffice_table'>
		<thead>
			<tr><th>Film</th><th>Image</th><th>Ann&eacute;e</th><th>Acteurs</th></tr>
		</thead>
		<tbody>
";

// Parcours des films
foreach($result as $key => $tb) {
	$traited_titre = utf8_decode($tb['mov_titre']);
	if (strpos($traited_titre,"(film") != 0)
		$traited_titre = trim(substr($traited_titre,0,strpos($traited_titre,"(film")));
    echo "
			<tr>
				<td><a href=\"" . $__url_wiki . utf8_decode($tb['mov_resource']) . "\">" . $traited_titre . "</a></td>
				<td><img src=\"". utf8_decode($tb['mov_url_image']) . "\" width='150px' height='150px'/></td>
				<td>" . $tb['mov_annee'] . "</td>
				<td>
					<ul>";
	
	// Pour chaque film, parcours des acteurs
	$sth_idactors = $dbh->prepare("SELECT l.id_act as 'act_id' FROM link_movies_actors l, movies m WHERE l.id_mov = m.id_mov AND m.mov_is_success = 1 AND l.id_mov = ". $tb['id_mov']);
	$sth_idactors->execute();

	$actors = $sth_idactors->fetchAll();
	foreach($actors as $key => $id_act) {
		$sth_actor = $dbh->prepare("SELECT act_resource, act_nom FROM actors WHERE id_act = ". $id_act['act_id']);
		$sth_actor->execute();
		$actor = $sth_actor->fetchAll();
		echo "			<li><a href=\"" . $__url_wiki . utf8_decode($actor[0]['act_resource']) . "\">" . utf8_decode($actor[0]['act_nom']) . "</a></li>";
	}
	echo "
					</ul>
				</td>
			</tr>";   
}

echo "	</tbody>
	</table>";
unset($result);

include("../includes/footer.php");
?>
