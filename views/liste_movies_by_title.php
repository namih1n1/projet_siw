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
			<th>Films</th><th>Ann&eacute;e de sortie</th><th>Au box-office</th><th>Acteurs</th>
		</tr>
	</thead>
	<tbody>
";

// Parcours des films de l'acteur courant

foreach ($films as $key => $tb) {
	if (substr($tb['mov_titre'],0,strpos($tb['mov_titre'],"(film")) != false)
		$traitement_titre = trim(utf8_decode(substr($tb['mov_titre'],0,strpos($tb['mov_titre'],"(film"))));
	else $traitement_titre = trim(utf8_decode($tb['mov_titre']));
	
	$succes = ($tb['mov_is_success'] == 1) ? 'Oui' : 'Non';
	echo "
    <tr>
        <td><a href=\"" . $__url_wiki . utf8_decode($tb['mov_resource']) . "\">" . $traitement_titre ."</td>
        <td>" . $tb['mov_annee'] . "</td>
		<td>" . $succes . "</td>
		<td><ul>";
		
		
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
				echo "<li><a href=\"" . $__url_wiki . utf8_decode($actor['act_resource']) . "\">" . utf8_decode($actor['act_nom']) ."</li>";
		}
		
		echo "</ul></td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
