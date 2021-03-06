<?php
include("../includes/header.php");

// Récupération des acteurs à succès de la base
$sth = $dbh->prepare("SELECT * FROM actors WHERE act_is_success = 1 ORDER BY act_nom");
$sth->execute();
$result = $sth->fetchAll();

if( !$result ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Récupération de l'année la plus récente des films répertoriés
$sth_last = $dbh->prepare("SELECT MAX(mov_annee) as last FROM movies");
$sth_last->execute();
$last_annee = $sth_last->fetchAll();
if( !$last_annee ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

echo "<div class = 'tabletitle' id='actor_success'><h2>Les " .count($result) . " acteurs du box-office mondial, jusqu'&agrave; " . $last_annee[0]['last'] . ".</h2></div>";
echo "<table class='boxoffice_table'>
		<thead>
			<tr><th>Acteur</th><th>Date de naissance</th><th>Photo</th><th>Films au box-office</th></tr>
		</thead>
		<tbody>
";

// Parcours des acteurs
foreach($result as $key => $tb) {

	if ($tb['act_naissance'] != "0000-00-00") {
		$tb_naiss  = date_parse($tb['act_naissance']);
		$naissance = $tb_naiss['day'] . "/" .$tb_naiss['month'] . "/" .$tb_naiss['year'];
	}
	else {
		$naissance = "-";
	}
	
    echo "
			<tr>
				<td><a  class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($tb['act_resource']) . "\">" . utf8_decode($tb['act_nom']) . "</a><br />
					<a href=\"./liste_movies_for_one_actor.php?id_actor=" . $tb['id_act'] . "\">Voir ses autres films</a>
				</td>
				<td>" . $naissance . "</td>
				<td><img src=\"". utf8_decode($tb['act_url_image']) . "\" width='150px' height='150px'/></td>
				<td>
					<div id=\"show_movie_".$tb['id_act']."\" onclick=\"show_list(".$tb['id_act'].")\">Montrer ses films</div>
					<ul id=\"montrer_film_".$tb['id_act']."\" style=\"display:none;\" >";
	// Pour chaque acteur, parcours des films
	$sth_idmovies = $dbh->prepare("
		SELECT 	lma.id_mov as 'mov_id' 
		FROM 	link_movies_actors lma,
				movies m
		WHERE 	lma.id_act = ". $tb['id_act'] ."
		AND		lma.id_mov = m.id_mov
		AND		m.mov_is_success = 1
		");
	$sth_idmovies->execute();

	$movies = $sth_idmovies->fetchAll();
	foreach($movies as $key => $id_mov) {
		$sth_movie = $dbh->prepare("SELECT mov_resource, mov_titre FROM movies WHERE id_mov = ". $id_mov['mov_id']);
		$sth_movie->execute();
		$movie = $sth_movie->fetchAll();
		echo "			<li><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($movie[0]['mov_resource']) . "\">" . utf8_decode($movie[0]['mov_titre']) . "</a></li>";
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
