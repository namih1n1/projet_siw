<?php
include("../includes/header.php");

$type_quiz_m = rand(1,2);

if ($type_quiz_m == 1) {
	// Récupération de 2 films à succès aléatoires
	$sth_quizmovie = $dbh->prepare("SELECT * FROM movies WHERE mov_is_success = 1 ORDER BY RAND() LIMIT 2");
	$sth_quizmovie->execute();
	$quizmovie = $sth_quizmovie->fetchAll();

	if( !$quizmovie ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

	?>
	<div class="question_quiz">Quiz sur les films au box-office</div>

	<?php 
	if (strpos($quizmovie[0]['mov_titre'],"(film") != 0)
		$traitement_titre0 = trim(utf8_decode(substr($quizmovie[0]['mov_titre'],0,strpos($quizmovie[0]['mov_titre'],"(film"))));
	else $traitement_titre0 = trim(utf8_decode($quizmovie[0]['mov_titre']));
		
	if (strpos($quizmovie[1]['mov_titre'],"(film") != 0)
		$traitement_titre1 = trim(utf8_decode(substr($quizmovie[1]['mov_titre'],0,strpos($quizmovie[1]['mov_titre'],"(film"))));
	else $traitement_titre1 = trim(utf8_decode($quizmovie[1]['mov_titre']));
	
	echo "
		<div class = \"quizz\">
			<div class=\"bloc_question\">
				<p>Est-ce que</p><p style='font-weight:bold;text-decoration:underline;'>" . $traitement_titre0 . "</p><p>est sorti avant</p><p style='font-weight:bold;text-decoration:underline;'>" . $traitement_titre1 . " ?</p>
			</div>
			<br />
			<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_film_no(". $quizmovie[0]['mov_annee'] .",". $quizmovie[1]['mov_annee'] .")\" />
			<input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_film_yes(". $quizmovie[0]['mov_annee'] .",". $quizmovie[1]['mov_annee'] .")\" />
		</div>
		<div class=\"propositions\">
			<div class=\"reponse_quiz\"></div>
			
			<div id=\"choix\" style=\"display:none\">
				<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
				<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
			</div>
		</div>	
		";
}
if ($type_quiz_m == 2) {
	// 1 réalisateur random d'au moins 3 films sortis après 1980 (et qui ont une date de naissance, et ont fait joué des acteurs connus)
	$sth_real = $dbh->prepare("
		SELECT sel.nb_id_dir as 'id_dir', sel.dir_nom as 'dir_nom', sel.dir_url_image as 'dir_url_image', COUNT(sel.nb_id_dir) 
		FROM (	
			SELECT 	DISTINCT 	d.id_dir as 'nb_id_dir',
								d.dir_nom as 'dir_nom', 
								d.dir_url_image as 'dir_url_image',
								m.mov_titre
			
			FROM 	directors d, 
					link_directors_movies dm, 
					movies m,
					link_movies_actors ma,
					actors a
					
			WHERE 	d.id_dir = dm.id_dir
			AND		dm.id_mov = m.id_mov
			AND 	m.id_mov = ma.id_mov
			AND     ma.id_act = a.id_act
			
			AND		m.mov_annee > 1980
			AND 	d.dir_naissance NOT LIKE '0000-00-00'
			AND		d.dir_url_image NOT LIKE ''
			AND		a.act_is_success = 1
			GROUP BY d.id_dir, d.dir_nom, m.mov_titre
		) AS sel
		GROUP BY sel.nb_id_dir
		HAVING COUNT(sel.nb_id_dir) > 2
		ORDER BY RAND()
		LIMIT 1
		");
	$sth_real->execute();
	$real = $sth_real->fetchAll();
		
	// Récupération des films du réalisateur
	$sth_mov = $dbh->prepare("	
		SELECT  m.mov_titre
		FROM   	link_directors_movies dm,
				movies m,
				directors d
		WHERE	d.id_dir = ".$real[0]['id_dir']."
		AND		d.id_dir = dm.id_dir
		AND		dm.id_mov = m.id_mov
		ORDER BY m.mov_annee DESC
		");
	$sth_mov->execute();
	$list_mov = $sth_mov->fetchAll();
	
	// Construction de la liste de films à afficher
	// Ajout du titre le plus récent
	$titre_recent = utf8_decode(array_pop($list_mov)['mov_titre']);
	$tb_film[0] =array('id' => 0, 'titre' => $titre_recent);
	
	// Mélange du tableau de films
	$keys = array_keys($list_mov);
	shuffle($keys);
	foreach($keys as $key) {
		$rnd_list_mov[$key] = $list_mov[$key];
	}
	$list_mov = $rnd_list_mov;
	unset($rnd_list_mov);
	
	// Ajout de deux autres titres.
	$tb_film[1] = array('titre' => utf8_decode(array_pop($list_mov)['mov_titre']));
	$tb_film[2] = array('titre' => utf8_decode(array_pop($list_mov)['mov_titre']));
	
	// Mélange du tableau de films
	$keys = array_keys($tb_film);
	shuffle($keys);
	foreach($keys as $key) {
		$rnd_tb_film[$key] = $tb_film[$key];
	}
	$tb_film = $rnd_tb_film;
	unset($rnd_tb_film);
	
	// Récupération de deux autres réalisateurs, qui n'ont pas réalisés les films proposés
	$sth_other = $dbh->prepare("
		SELECT 	d.dir_nom as 'dir_nom', d.dir_url_image as 'dir_url_image'
		FROM	directors d
		WHERE	d.id_dir != ".$real[0]['id_dir']."
		AND		d.dir_url_image NOT LIKE ''
		ORDER BY RAND()
		LIMIT 2
		");
	$sth_other->execute();
	$list_other = $sth_other->fetchAll();
	
	$reponse = $real[0]['dir_nom'];
	$tb_prop[0] = array('nom' => $real[0]['dir_nom'], 'url_img' => $real[0]['dir_url_image'], 'choix' => 1);
	$tb_prop[1] = array('nom' => $list_other[0]['dir_nom'], 'url_img' => $list_other[0]['dir_url_image'], 'choix' => 0);
	$tb_prop[2] = array('nom' => $list_other[1]['dir_nom'], 'url_img' => $list_other[1]['dir_url_image'], 'choix' => 0);
	
	// Mélange du tableau des réalisateurs
	$keys = array_keys($tb_prop);
	shuffle($keys);
	foreach($keys as $key) {
		$rnd_tb_prop[$key] = $tb_prop[$key];
	}
	$tb_prop = $rnd_tb_prop;
	unset($rnd_tb_prop);
	
	unset($real);
	unset($list_mov);
	unset($list_other);
	echo "
		<div class = \"quizz\">
			<table>
				<thead><tr><th>Question</th><th>Propositions</th></tr></thead>
				<tr>
					<td style=\"font-size:20px; text-align:center;\">Quelle personne a r&eacute;alis&eacute; ces trois films ? <br />
						<ul>"; 
					foreach($tb_film as $key => $film){
						echo "<li>".$film['titre']."</li>";
					}
	echo 		"		</ul>
					</td>
				<td>
					<table>
						<tr>";
				$cpt = 0;
				foreach($tb_prop as $cle => $director) {
					echo "	<td><img src=\"".utf8_decode($director['url_img'])."\" height='250px'/><br />
								<input class=\"button_dir\" type=\"button\" name=\"".$director['nom']."\" value=\"".$director['nom']."\" onclick=\"verif_director(".$cpt.",".$director['choix'].",'".addslashes($reponse)."')\" />
							</td>";
					$cpt++;
				}
				echo "	</tr>
					</table>
				</td>
			</tr>
		</table>
		<div class=\"reponse_quiz\"></div>
		
		<div id=\"choix\" style=\"display:none\">
			<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
			<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
		</div>
		</div>
		
		";
}
include("../includes/footer.php");
?>
