<?php
include("../includes/header.php");
if ( isset($_GET['lettre']) ) {
	$l_dir = $_GET['lettre'];
	$sth_reals = $dbh->prepare("SELECT * FROM directors WHERE dir_nom LIKE \"".$l_dir."%\" ORDER BY dir_nom");
	$sth_reals->execute();
	$reals = $sth_reals->fetchAll();

	echo "<div class = 'tabletitle' id='movielist'><h2>" .count($reals) . " r&eacute;alisateurs commencant par " . $l_dir . ".</h2></div>";
	echo "<table>
		<thead>
			<tr>
				<th>R&eacute;alisateurs</th><th>Informations</th><th>Films</th>
			</tr>
		</thead>
		<tbody>
	";

	// Parcours des reals
	foreach ($reals as $key => $tb) {

		$traitement_nom = trim(utf8_decode($tb['dir_nom']));
		$image = ( utf8_decode($tb['dir_url_image']) == "") ? "Pas de photo" : "<img src=\"".utf8_decode($tb['dir_url_image'])."\" width='200px' height='auto' />";
		if ($tb['dir_naissance'] != "0000-00-00") {
			$tb_naiss  = date_parse($tb['dir_naissance']);
			$naissance = $tb_naiss['day'] . "/" .$tb_naiss['month'] . "/" .$tb_naiss['year'];
		}
		else {
			$naissance = "-";
		}
		echo "
		<tr>
			<td><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($tb['dir_resource']) . "\">" . $traitement_nom ."</td>
			<td><div id=\"show_infos_".$tb['id_dir']."\" onclick=\"show_infos(".$tb['id_dir'].")\">Voir les informations relatives</div>
				<table id=\"montrer_infos_".$tb['id_dir']."\" style=\"display:none;\" >
					<thead><th>Image</th><th>Date de naissance</th></thead>
					<tr>
						<td>".$image."</td>
						<td>".$naissance."</td>
					</tr>
				</table>
			</td>
			<td>
				<div id=\"show_movie_".$tb['id_dir']."\" onclick=\"show_list(".$tb['id_dir'].")\">Liens wiki des films</div>
				<ul id=\"montrer_film_".$tb['id_dir']."\" style=\"display:none;\" >";
			
			
			$sth_films = $dbh->prepare("
				SELECT 	m.mov_resource, 
						m.mov_titre 
				FROM 	movies m,
						link_directors_movies l 
				WHERE 	l.id_mov = m.id_mov
				AND 	l.id_dir = ".$tb['id_dir']);
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
