<?php
include("../includes/header.php");
if ( isset($_GET['lettre']) ) {
	$l_actor = $_GET['lettre'];
	$sth_acteurs = $dbh->prepare("SELECT * FROM actors WHERE act_nom LIKE \"".$l_actor."%\" ORDER BY act_nom");
	$sth_acteurs->execute();
	$acteurs = $sth_acteurs->fetchAll();

	echo "<div class = 'tabletitle' id='movielist'><h2>" .count($acteurs) . " acteurs commencant par " . $l_actor . ".</h2></div>";
	echo "<table>
		<thead>
			<tr>
				<th>Acteurs</th><th>Informations</th><th>Films</th>
			</tr>
		</thead>
		<tbody>
	";

	// Parcours des acteurs
	foreach ($acteurs as $key => $tb) {

		$traitement_nom = trim(utf8_decode($tb['act_nom']));
		$image = ( utf8_decode($tb['act_url_image']) == "") ? "Pas de photo" : "<img src=\"".utf8_decode($tb['act_url_image'])."\" width='200px' height='auto' />";
		if ($tb['act_naissance'] != "0000-00-00") {
			$tb_naiss  = date_parse($tb['act_naissance']);
			$naissance = $tb_naiss['day'] . "/" .$tb_naiss['month'] . "/" .$tb_naiss['year'];
		}
		else {
			$naissance = "-";
		}
		$succes = ($tb['act_is_success'] == 1) ? 'Oui' : 'Non';
		echo "
		<tr>
			<td><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($tb['act_resource']) . "\">" . $traitement_nom ."</td>
			<td><div id=\"show_infos_".$tb['id_act']."\" onclick=\"show_infos(".$tb['id_act'].")\">Voir les informations relatives</div>
				<table id=\"montrer_infos_".$tb['id_act']."\" style=\"display:none;\" >
					<thead><th>Image</th><th>Date de naissance</th><th>Au box-office</th></thead>
					<tr>
						<td>".$image."</td>
						<td>".$naissance."</td>
						<td>".$succes."</td>
					</tr>
				</table>
			</td>
			<td>
				<div id=\"show_movie_".$tb['id_act']."\" onclick=\"show_list(".$tb['id_act'].")\">Liens wiki des films</div>
				<ul id=\"montrer_film_".$tb['id_act']."\" style=\"display:none;\" >";
			
			
			$sth_films = $dbh->prepare("
				SELECT 	m.mov_resource, 
						m.mov_titre 
				FROM 	actors a, 
						movies m, 
						link_movies_actors l 
				WHERE 	a.id_act = l.id_act
				AND		l.id_mov = m.id_mov
				AND 	a.id_act = ".$tb['id_act']);
			$sth_films->execute();
			$movies = $sth_films->fetchAll();
			foreach($movies as $key => $movie) {
					echo "<li><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($movie['mov_resource']) . "\">" . utf8_decode($movie['mov_titre']) ."</li>";
			}
			
			echo "</ul></td>
		</tr>";   
	}

	echo "</tbody></table>";
}

include("../includes/footer.php");
?>
