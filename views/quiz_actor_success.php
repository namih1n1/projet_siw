<?php
include("../includes/header.php");

$type_quiz = rand(1,3);

if ($type_quiz == 1) {
	// Récupération de 2 acteurs à succès aléatoires
	$sth_quizactor = $dbh->prepare("SELECT * FROM success_actors ORDER BY RAND() LIMIT 2");
	$sth_quizactor->execute();
	$quizactor = $sth_quizactor->fetchAll();

	if( !$quizactor ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

	?>
	<div class="question_quiz">Quiz sur les acteurs</div>

	<?php 
	echo "
		<div class = \"quizz\"> 
		<p>Est-ce que \"" . utf8_decode($quizactor[0]['sa_nom']) . "\" est n&eacute; avant \"" . utf8_decode($quizactor[1]['sa_nom']) . "\" ?</p>
		<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no(". $quizactor[0]['sa_naissance'] .",". $quizactor[1]['sa_naissance'] .")\" />
		<input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes(". $quizactor[0]['sa_naissance'] .",". $quizactor[1]['sa_naissance'] .")\" />
		</div>
		";
}

if ($type_quiz == 2 ) {
	// Récupération de 3 acteurs à succès aléatoires
	$sth_quizactor = $dbh->prepare("SELECT * FROM success_actors WHERE sa_url_image NOT LIKE '' ORDER BY RAND() LIMIT 3");
	$sth_quizactor->execute();
	$quizactor = $sth_quizactor->fetchAll();

	$choix_photo = rand(0,2);
	
	// Mélange du tableau de résultat
	$keys = array_keys($quizactor);
	shuffle($keys);
	foreach($keys as $key) {
		$rnd_quizactor[$key] = $quizactor[$key];
	}
	$quizactor = $rnd_quizactor;
	
	echo "
		<div class=\"quizzidentite\">
		<div id=\"identite\"> 
			<p>Qui est-ce ?</p>
			<div class=\"photo_actor\"><img src=\"".utf8_decode($quizactor[$choix_photo]['sa_url_image'])."\" width='200px' height='300px' /></div>
		</div>
		<div id=\"question\">
			<input type=\"button\" name=\"choix_0\" value=\"".utf8_decode($quizactor[0]['sa_nom'])."\" onclick=\"verif_photo(0,".$choix_photo.")\"  />
			<input type=\"button\" name=\"choix_1\" value=\"".utf8_decode($quizactor[1]['sa_nom'])."\" onclick=\"verif_photo(1,".$choix_photo.")\"  />
			<input type=\"button\" name=\"choix_2\" value=\"".utf8_decode($quizactor[2]['sa_nom'])."\" onclick=\"verif_photo(2,".$choix_photo.")\"  />
		</div>
		</div>
		";	
}

if ($type_quiz == 3 ) {
	$test = 0;
	while ($test < 3) {
		// Récupération d'un acteur au hasard
		$sth_count = $dbh->prepare("SELECT DISTINCT id_act FROM link_movies_actors");
		$sth_count->execute();
		$nb = $sth_count->rowCount();
		$id_act = rand(1,$nb);
		
		// Récupération des films de l'acteur choisi (au moins 3)
		$sth_id_films = $dbh->prepare("SELECT DISTINCT id_mov FROM link_movies_actors WHERE id_act = ".$id_act);
		$sth_id_films->execute();
		$test = $sth_id_films->rowCount();
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
	$sth_aut_act = $dbh->prepare("
		SELECT 	a.sa_nom
		FROM 	link_movies_actors l, success_actors a 
		WHERE 	l.id_mov NOT IN (".$critere.") 
		AND		l.id_act = a.id_success_a
		ORDER BY RAND() LIMIT 2");
	$sth_aut_act->execute();
	$list_aut_act = $sth_aut_act->fetchAll();
	
	// Récupération de l'acteur choisi
	$sth_oui_act = $dbh->prepare("SELECT sa_nom FROM success_actors WHERE id_success_a = ".$id_act);
	$sth_oui_act->execute();
	$acteur = $sth_oui_act->fetchAll();
	
	// Création du tableau des 3 acteurs
	$tb_acteurs[0] = array("nom" => $acteur[0]['sa_nom'], "choix" => 1);
	$tb_acteurs[1] = array("nom" => $list_aut_act[0]['sa_nom'], "choix" => 0);
	$tb_acteurs[2] = array("nom" => $list_aut_act[1]['sa_nom'], "choix" => 0);
	
	// Mélange du tableau de résultat
	$keys = array_keys($tb_acteurs);
	shuffle($keys);
	foreach($keys as $key) {
		$rnd_quizactor[$key] = $tb_acteurs[$key];
	}
	$tb_acteurs = $rnd_quizactor;

	echo "
		<div class=\"quizzcommun\">
		<div id=\"commun\"> 
			<p>Quel est l'acteur commun &agrave; ces films ?</p>
			<div class=\"photo_actor\">";
			foreach($tb_titres as $key => $titre) {
				echo utf8_decode($titre['mov_titre'])."<br />";
			}
			echo "</div>
		</div>
		<div id=\"question\">
			<input type=\"button\" name=\"choix_0\" value=\"".utf8_decode($tb_acteurs[0]['nom'])."\" onclick=\"verif_commun(".$tb_acteurs[0]['choix'].")\"  />
			<input type=\"button\" name=\"choix_1\" value=\"".utf8_decode($tb_acteurs[1]['nom'])."\" onclick=\"verif_commun(".$tb_acteurs[1]['choix'].")\"  />
			<input type=\"button\" name=\"choix_2\" value=\"".utf8_decode($tb_acteurs[2]['nom'])."\" onclick=\"verif_commun(".$tb_acteurs[2]['choix'].")\"  />
		</div>
		</div>
		";	
}

echo "
	<div class=\"propositions\">
	<div class=\"reponse_quiz\"></div>
		
	<div id=\"choix\" style=\"display:none\">
		<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
		<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
	</div>
	</div>

";


echo "<script language=\"javascript\">
var juste 	= \"Vous avez raison !\";
var faux 	= \"Vous avez tort.\";
function answer_no(param1,param2,point){
	var score = point;
	if (param1 > param2) {
		score = score + 1;
	}
	
	(param1 > param2) 	? document.getElementsByClassName('reponse_quiz').item(0).innerHTML = juste
						: document.getElementsByClassName('reponse_quiz').item(0).innerHTML = faux;
	document.getElementById('choix').style.display = \"block\";
	// document.getElementById('cumul_point').innerHTML = score;
	
}
function answer_yes(param1,param2,point){
	var score = point;
	if (param1 < param2) {
		score = score + 1;
		
	}
	(param1 < param2) 	? document.getElementsByClassName('reponse_quiz').item(0).innerHTML = juste
						: document.getElementsByClassName('reponse_quiz').item(0).innerHTML = faux;
	document.getElementById('choix').style.display = \"block\";
	// document.getElementById('cumul_point').innerHTML = score;
}

function verif_photo(id_nom,choix_photo) {
	document.getElementsByClassName('reponse_quiz').item(0).innerHTML = (id_nom == choix_photo) ? juste : faux;
	document.getElementById('choix').style.display = \"block\";
}

function verif_commun(choix_acteur) {
	document.getElementsByClassName('reponse_quiz').item(0).innerHTML = (choix_acteur == 1) ? juste : faux;
	document.getElementById('choix').style.display = \"block\";
}

function again() {
	window.location.reload(5);
}

function changerQuiz() {
	window.location = \"./liste_quiz.php\";
}
	
</script>";

include("../includes/footer.php");
?>
