<?php
include("../includes/header.php");
$l_director = ( isset($_GET['lettre']) ) ? ucwords($_GET['lettre']) : null;

$sth_directeurs = $dbh->prepare("SELECT * FROM directors WHERE dir_nom LIKE \"".$l_director."%\" ORDER BY dir_nom");
$sth_directeurs->execute();
$directeurs = $sth_directeurs->fetchAll();

echo "<div class = 'tabletitle' id='movielist'><h2>" .count($directeurs) . " r&eacute;alisateurs commencant par " . $l_director . ".</h2></div>";
echo "<table>
	<thead>
        <tr>
			<th>R&eacute;alisateur</th><th>Informations</th><th>Films</th>
		</tr>
	</thead>
	<tbody>
";

// Parcours des réalisateurs
foreach ($directeurs as $key => $tb) {

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
			<div><a href=\"./liste_movies_for_one_director.php?id_director=".$tb['id_dir']."\">Cliquez ici pour ses films r&eacute;alis&eacute;s</div>
			<table id=\"montrer_infos_".$tb['id_dir']."\" style=\"display:none;\" >
				<thead><th>Image</th><th>Date de naissance</th></thead>
				<tr>
					<td>".$image."</td>
					<td>".$naissance."</td>
				</tr>
			</table>
		</td>
		<td>
			<div id=\"show_movie_".$tb['id_dir']."\" onclick=\"show_list(".$tb['id_dir'].")\">Montrer ses films</div>
			<ul id=\"montrer_film_".$tb['id_dir']."\" style=\"display:none;\" >";
		
		$sth_films = $dbh->prepare("
			SELECT 	m.mov_resource, 
					m.mov_titre
			FROM 	directors d, 
					movies m, 
					link_directors_movies l 
			WHERE 	d.id_dir = l.id_dir
			AND		l.id_mov = m.id_mov
			AND 	d.id_dir = ".$tb['id_dir']);
		$sth_films->execute();
		$movies = $sth_films->fetchAll();
		foreach($movies as $key => $movie) {
				echo "<li><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($movie['mov_resource']) . "\">" . utf8_decode($movie['mov_titre']) ."</li>";
		}
		
		echo "</ul></td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
