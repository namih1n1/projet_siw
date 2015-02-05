<?php
include("../includes/header.php");
$l_movie = ( isset($_GET['lettre']) ) ? ucwords($_GET['lettre']) : null;

$sth_films = $dbh->prepare("SELECT * FROM movies WHERE mov_titre LIKE \"".$l_movie."%\" ORDER BY mov_titre");
$sth_films->execute();
$films = $sth_films->fetchAll();

echo "<div class = 'tabletitle' id='movielist'><h2>" .count($films) . " films commencant par " . $l_movie . ".</h2></div>";
echo "<table>
	<thead>
        <tr>
			<th>Films</th><th>Informations</th><th>Acteurs</th>
		</tr>
	</thead>
	<tbody>
";

// Parcours des films

foreach ($films as $key => $tb) {
	if (substr($tb['mov_titre'],0,strpos($tb['mov_titre'],"(film")) != false)
		$traitement_titre = trim(utf8_decode(substr($tb['mov_titre'],0,strpos($tb['mov_titre'],"(film"))));
	else $traitement_titre = trim(utf8_decode($tb['mov_titre']));
	
	$image = ( utf8_decode($tb['mov_url_image']) == "") ? "Pas de photo" : "<img src=\"".utf8_decode($tb['mov_url_image'])."\" width='200px' height='auto' />";
	
	$succes = ($tb['mov_is_success'] == 1) ? 'Oui' : 'Non';
	echo "
    <tr>
        <td><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($tb['mov_resource']) . "\">" . $traitement_titre ."</td>
		<td><div id=\"show_infos_".$tb['id_mov']."\" onclick=\"show_infos(".$tb['id_mov'].")\">Voir les informations relatives</div>
			<div><a href=\"./liste_actors_for_one_movie.php?id_movie=".$tb['id_mov']."\">Cliquer ici pour voir les acteurs</a></div>
			<table id=\"montrer_infos_".$tb['id_mov']."\" style=\"display:none;\" >
				<thead><th>Image</th><th>Ann&eacute;e de sortie</th><th>Au box-office</th></thead>
				<tr>
					<td>".$image."</td>
					<td>" . $tb['mov_annee'] . "</td>
					<td>" . $succes . "</td>
				</tr>
			</table>
		</td>
		<td>
			<div id=\"show_movie_".$tb['id_mov']."\" onclick=\"show_list(".$tb['id_mov'].")\">Liens wiki des acteurs</div>
			<ul id=\"montrer_film_".$tb['id_mov']."\" style=\"display:none;\" >";
		
		
		$sth_actors = $dbh->prepare("
			SELECT 	act_resource, 
					act_nom 
			FROM 	actors a, 
					movies m, 
					link_movies_actors l 
			WHERE 	a.id_act = l.id_act
			AND		l.id_mov = m.id_mov
			AND 	m.id_mov = ".$tb['id_mov']);
		$sth_actors->execute();
		$actors = $sth_actors->fetchAll();
		foreach($actors as $key => $actor) {
				echo "<li><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($actor['act_resource']) . "\">" . utf8_decode($actor['act_nom']) ."</li>";
		}
		
	echo "</ul></td>
		
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
