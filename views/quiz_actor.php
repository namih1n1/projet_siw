<?php
include("../includes/header.php");

$criteria = ( isset($_GET['easy']) && ($_GET['easy'] == 'yes') ) ? " AND act_is_success = 1" : "";
$type_quiz = rand(1,3);
echo "<div class=\"question_quiz\">Quiz sur les acteurs</div>
	<div class=\"propositions\">
		<div class=\"reponse_quiz\"></div>
			
		<div id=\"choix\" style=\"display:none\">
			<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
			<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
		</div>
	</div>";

if ($type_quiz == 1) { // Question sur les dates de naissance
	// Récupération de 2 acteurs 
	$sth_quizactor = $dbh->prepare("SELECT * FROM actors WHERE act_naissance NOT LIKE '0000-00-00' ".$criteria." ORDER BY RAND() LIMIT 2");
	$sth_quizactor->execute();
	$quizactor = $sth_quizactor->fetchAll();

	if( !$quizactor ) { print_r($dbh->errorInfo()); echo "\n"; exit; }
	
	echo "
		<div class = \"quizz\"> 
			<div class=\"bloc_question\">
				<p>Est-ce que \"" . utf8_decode($quizactor[0]['act_nom']) . "\" est n&eacute; avant \"" . utf8_decode($quizactor[1]['act_nom']) . "\" ?</p>
			</div>
			<br />
		<input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes('".$quizactor[0]['act_naissance']."','". $quizactor[1]['act_naissance'] ."')\" />
		<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no('".$quizactor[0]['act_naissance']."','". $quizactor[1]['act_naissance'] ."')\" />
		</div>
		";
}

if ($type_quiz == 2 ) { // Question de type Qui est-ce ?
	// Récupération de 3 acteurs à succès aléatoires
	$sth_quizactor = $dbh->prepare("SELECT * FROM actors WHERE act_url_image NOT LIKE '' ".$criteria." ORDER BY RAND() LIMIT 3");
	$sth_quizactor->execute();
	$quizactor = $sth_quizactor->fetchAll();

	$choix_photo = rand(0,2);
	
	// Mélange du tableau de résultat
	mix_tb_keyed($quizactor);
	
	echo "
		<div class=\"quizzidentite\">
			<div id=\"identite\"> 
				<div class=\"bloc_question\">
					<p>Qui est-ce ?</p>
					<div class=\"photo_actor\"><img style='padding-bottom:20px;' src=\"".utf8_decode($quizactor[$choix_photo]['act_url_image'])."\" width='200px' height='300px' /></div>
				</div>
				<br />
			</div>
			<div id=\"question\">";
			$cpt = 0;
			foreach($quizactor as $cle => $tb) {
				echo "<input type=\"button\" name=\"choix_".$cle."\" value=\"".utf8_decode($quizactor[$cle]['act_nom'])."\" onclick=\"verif_photo(".$cle.",".$choix_photo.",'".addslashes(trim($quizactor[$choix_photo]['act_nom']))."',".$cpt.")\"  />";
				$cpt++;
			}
			echo "</div>
		</div>
		";	
}

if ($type_quiz == 3 ) { // Question films communs
	$test = 0;
	while ($test < 3) {
		// Récupération d'un acteur au hasard, selon le mode de difficulté
		$requete_hard = "SELECT DISTINCT id_act FROM link_movies_actors";
		$requete_easy = "
			SELECT 	DISTINCT l.id_act as 'id_act' 
			FROM 	link_movies_actors l, 
					actors a 
			WHERE 	l.id_act = a.id_act 
			AND 	a.act_is_success = 1";
		$requete = ( isset($_GET['easy']) && ($_GET['easy'] == 'yes') ) ? $requete_easy : $requete_hard;
		
		$sth_count = $dbh->prepare($requete);
		$sth_count->execute();
		$nb = $sth_count->rowCount();
		$id_act = rand(1,$nb);
		
		$requete_hard = null;
		$requete_easy = null;
		$requete = null;
		
		// Récupération des films de l'acteur choisi (au moins 3), selon le mode de difficulté
		$requete_hard = "SELECT DISTINCT id_mov FROM link_movies_actors WHERE id_act = ".$id_act;
		$requete_easy = "
			SELECT 	DISTINCT l.id_mov as 'id_mov'
			FROM 	link_movies_actors l, 
					movies m 
			WHERE 	l.id_mov = m.id_mov
			AND 	m.mov_is_success = 1 
			AND 	l.id_act = ".$id_act;
		$requete = ( isset($_GET['easy']) && ($_GET['easy'] == 'yes') ) ? $requete_easy : $requete_hard;
		
		$sth_id_films = $dbh->prepare($requete);
		$sth_id_films->execute();
		$test = $sth_id_films->rowCount();
		
		$requete_hard = null;
		$requete_easy = null;
		$requete = null;
	}
	$list_id_films = $sth_id_films->fetchAll();
	
	// Construction du critère de films choisis
	$tb_id_films = array();
	foreach($list_id_films as $key => $tb) {
		array_push($tb_id_films,(int)($tb['id_mov']));
	}
	$critere = implode(",",$tb_id_films);

	// Récupération des titres des films
	$sth_mov = $dbh->prepare("
		SELECT mov_titre FROM movies WHERE id_mov IN (".$critere.") ORDER BY RAND() LIMIT 3
	");
	$sth_mov->execute();
	$tb_titres = $sth_mov->fetchAll();
	
	// Récupération de 2 acteurs qui ne jouent pas dans les films de l'acteur
	$requete_hard = "
		SELECT 	a.act_nom as 'act_nom'
		FROM 	link_movies_actors l, actors a 
		WHERE 	l.id_mov NOT IN (".$critere.") 
		AND		l.id_act = a.id_act
		ORDER BY RAND() LIMIT 2";
	$requete_easy = "
		SELECT 	a.act_nom as 'act_nom'
		FROM 	link_movies_actors l, actors a 
		WHERE 	l.id_mov NOT IN (".$critere.") 
		AND		a.act_is_success = 1
		AND		l.id_act = a.id_act
		ORDER BY RAND() LIMIT 2";
	$requete = ( isset($_GET['easy']) && ($_GET['easy'] == 'yes') ) ? $requete_easy : $requete_hard;
	
	$sth_aut_act = $dbh->prepare($requete);
	$sth_aut_act->execute();
	$list_aut_act = $sth_aut_act->fetchAll();
	
	// Récupération de l'acteur choisi
	$sth_oui_act = $dbh->prepare("SELECT act_nom FROM actors WHERE id_act = ".$id_act);
	$sth_oui_act->execute();
	$acteur = $sth_oui_act->fetchAll();
	
	// Création du tableau des 3 acteurs
	$tb_acteurs[0] = array("nom" => $acteur[0]['act_nom'], "choix" => 1);
	$tb_acteurs[1] = array("nom" => $list_aut_act[0]['act_nom'], "choix" => 0);
	$tb_acteurs[2] = array("nom" => $list_aut_act[1]['act_nom'], "choix" => 0);
	
	// Mélange du tableau de résultat
	mix_tb_keyed($tb_acteurs);

	echo "
		<div class=\"quizzcommun\">
			<div id=\"commun\" class=\"bloc_question\"> 
				<p>Quel est l'acteur commun &agrave; ces films ?</p>
				<div class=\"films\"><ul>";
				foreach($tb_titres as $key => $titre) {
					echo "<li>".utf8_decode($titre['mov_titre'])."</li>";
				}
				echo "</ul></div>
			</div>
			<div id=\"question\">";
			$cpt = 0;
			foreach($tb_acteurs as $cle => $tb) {
				echo "<input type=\"button\" name=\"choix_".$cle."\" value=\"".utf8_decode($tb_acteurs[$cle]['nom'])."\" onclick=\"verif_commun(".$cpt.",".$tb_acteurs[$cle]['choix'].",'".addslashes($acteur[0]['act_nom'])."')\"  />";
				$cpt++;
			}
			echo "
			</div>
		</div>
		";	
}
include("../includes/footer.php");
?>
