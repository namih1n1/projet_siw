<?php
include("../includes/header.php");
$directeur_id = ( isset($_GET['id_director']) ) ? $_GET['id_director'] : null;

// Infos acteur
$sth_directeur = $dbh->prepare("SELECT dir_nom FROM directors WHERE id_dir = ".$directeur_id);
$sth_directeur->execute();
$directeur = $sth_directeur->fetchAll();

$sth_films = $dbh->prepare("
	SELECT 	m.*
	FROM 	movies m,
			link_directors_movies l
	WHERE 	m.id_mov = l.id_mov
	AND		l.id_dir = ".$directeur_id."
	ORDER BY mov_annee DESC");
$sth_films->execute();
$films = $sth_films->fetchAll();

echo "<div class = 'tabletitle' id='movielist'><h2>" .count($films) . " films r&eacute;alis&eacute;s par " . $directeur[0]['dir_nom']. ".</h2></div>";
echo "<table>
	<thead>
        <tr>
			<th>Films</th><th>Image associ&eacute;e</th><th>Ann&eacute;e de sortie</th><th>Au box-office</th>
		</tr>
	</thead>
	<tbody>
";

// Parcours des films

foreach ($films as $key => $tb) {
	if (substr($tb['mov_titre'],0,strpos($tb['mov_titre'],"(film")) != false)
		$traitement_titre = trim(utf8_decode(substr($tb['mov_titre'],0,strpos($tb['mov_titre'],"(film"))));
	else $traitement_titre = trim(utf8_decode($tb['mov_titre']));
	
	$image = (utf8_decode($tb['mov_url_image']) == "") ? "Pas de photo" : "<img src=\"".utf8_decode($tb['mov_url_image'])."\" width='200px' height='auto' />";
	$succes = ($tb['mov_is_success'] == 1) ? 'Oui' : 'Non';
	echo "
    <tr>
        <td><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($tb['mov_resource']) . "\">" . $traitement_titre ."</td>
		<td>".$image."</td>
        <td>" . $tb['mov_annee'] . "</td>
		<td>" . $succes . "</td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
