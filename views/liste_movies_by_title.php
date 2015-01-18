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
			<th>Films</th><th>Ann&eacute;e de sortie</th><th>Au box-office</th><th>Acteurs du box-office</th>
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
		
		$list_id_actor = explode("|",$tb['mov_id_actor']);
		array_pop($list_id_actor);
		foreach($list_id_actor as $cle => $id_act) {
			if ($cle == 0) {
			}
			else {
				$id_actor = (int)($id_act);
				$sth_actor = $dbh->prepare("SELECT sa_resource, sa_nom FROM success_actors WHERE id_success_a = ".$id_actor);
				$sth_actor->execute();
				$actor = $sth_actor->fetchAll();
				
				echo "<li><a href=\"" . $__url_wiki . utf8_decode($actor[0]['sa_resource']) . "\">" . utf8_decode($actor[0]['sa_nom']) ."</li>";
			}
		}
		
		echo "</ul></td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
