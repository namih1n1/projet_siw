<?php
include("../includes/header.php");
$l_actor = ( isset($_GET['lettre']) ) ? ucwords($_GET['lettre']) : null;

$sth_acteurs = $dbh->prepare("SELECT * FROM actors WHERE act_nom LIKE \"".$l_actor."%\" ORDER BY act_nom");
$sth_acteurs->execute();
$acteurs = $sth_acteurs->fetchAll();

echo "<div class = 'tabletitle' id='movielist'><h2>" .count($acteurs) . " acteurs commencant par " . $l_actor . ".</h2></div>";
echo "<table>
	<thead>
        <tr>
			<th>Acteurs</th><th>Photo</th><th>Date de naissance</th><th>Au box-office</th><th>Films</th>
		</tr>
	</thead>
	<tbody>
";

// Parcours des acteurs
foreach ($acteurs as $key => $tb) {

	$traitement_nom = trim(utf8_decode($tb['act_nom']));
	
	$succes = ($tb['act_is_success'] == 1) ? 'Oui' : 'Non';
	echo "
    <tr>
        <td><a href=\"" . $__url_wiki . utf8_decode($tb['act_resource']) . "\">" . $traitement_nom ."</td>
		<td><img src=\"".utf8_decode($tb['act_url_image'])."\" width='200px' height='auto' /></td>
        <td>" . $tb['act_naissance'] . "</td>
		<td>" . $succes . "</td>
		<td><ul>";
		
		
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
				echo "<li><a href=\"" . $__url_wiki . utf8_decode($movie['mov_resource']) . "\">" . utf8_decode($movie['mov_titre']) ."</li>";
		}
		
		echo "</ul></td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
