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
			<th>R&eacute;alisateur</th><th>Photo</th><th>Date de naissance</th><th>Films</th>
		</tr>
	</thead>
	<tbody>
";

// Parcours des réalisateurs
foreach ($directeurs as $key => $tb) {

	$traitement_nom = trim(utf8_decode($tb['dir_nom']));
	
	echo "
    <tr>
        <td><a href=\"" . $__url_wiki . utf8_decode($tb['dir_resource']) . "\">" . $traitement_nom ."</td>
		<td><img src=\"".utf8_decode($tb['dir_url_image'])."\" width='200px' height='auto' /></td>
        <td>" . $tb['dir_naissance'] . "</td>
		<td><ul>";
		
		
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
				echo "<li><a href=\"" . $__url_wiki . utf8_decode($movie['mov_resource']) . "\">" . utf8_decode($movie['mov_titre']) ."</li>";
		}
		
		echo "</ul></td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
