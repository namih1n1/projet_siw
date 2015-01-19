<?php
include("../includes/header.php");

$type_quiz = rand(1,3);

if ($type_quiz == 1) { // Question sur les dates de naissance
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
		<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no('".$quizactor[0]['sa_naissance']."','". $quizactor[1]['sa_naissance'] ."')\" />
		<input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes('".$quizactor[0]['sa_naissance']."','". $quizactor[1]['sa_naissance'] ."')\" />
		</div>
		";
}

if ($type_quiz == 2 ) { // Question de type Qui est-ce ?
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
			<div id=\"question\">";
			$cpt = 0;
			foreach($quizactor as $cle => $tb) {
				echo "<input type=\"button\" name=\"choix_".$cle."\" value=\"".utf8_decode($quizactor[$cle]['sa_nom'])."\" onclick=\"verif_photo(".$cle.",".$choix_photo.",'".$quizactor[$choix_photo]['sa_nom']."',".$cpt.")\"  />";
				$cpt++;
			}
			echo "</div>
		</div>
		";	
}

if ($type_quiz == 3 ) { // Question films communs
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
				<div class=\"films\"><ul>";
				foreach($tb_titres as $key => $titre) {
					echo "<li>".utf8_decode($titre['mov_titre'])."</li>";
				}
				echo "</ul></div>
			</div>
			<div id=\"question\">";
			$cpt = 0;
			foreach($tb_acteurs as $cle => $tb) {
				echo "<input type=\"button\" name=\"choix_".$cle."\" value=\"".utf8_decode($tb_acteurs[$cle]['nom'])."\" onclick=\"verif_commun(".$cpt.",".$tb_acteurs[$cle]['choix'].",'".$acteur[0]['sa_nom']."')\"  />";
				$cpt++;
			}
			echo "
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
?>

<script language="javascript">
var juste 	= "Vous avez raison !";
var faux 	= "Vous avez tort.";

function answer_no(param1,param2){
	var naiss1 = new Date(param1);
	var naiss2 = new Date(param2);
	var formatNaiss1 = naiss1.getDate() + "/" + (naiss1.getMonth() + 1) + "/" + naiss1.getFullYear();
	var formatNaiss2 = naiss2.getDate() + "/" + (naiss2.getMonth() + 1) + "/" + naiss2.getFullYear();
	(naiss1 > naiss2) 	
		? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' ' + formatNaiss1 + " contre " + formatNaiss2
		: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' ' + formatNaiss1 + " contre " + formatNaiss2;
	document.getElementById('choix').style.display = "block";
	document.getElementById("answer_no").style.backgroundColor = "blue";
	document.getElementById('answer_no').disabled = "disabled";
	document.getElementById('answer_yes').disabled = "disabled";
}

function answer_yes(param1,param2){
	var naiss1 = new Date(param1);
	var naiss2 = new Date(param2);
	var formatNaiss1 = naiss1.getDate() + "/" + (naiss1.getMonth() + 1) + "/" + naiss1.getFullYear();
	var formatNaiss2 = naiss2.getDate() + "/" + (naiss2.getMonth() + 1) + "/" + naiss2.getFullYear();
	(naiss1 < naiss2) 	
		? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' ' + formatNaiss1 + " contre " + formatNaiss2
		: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' ' + formatNaiss1 + " contre " + formatNaiss2;
	document.getElementById('choix').style.display = "block";
	document.getElementById("answer_yes").style.backgroundColor = "blue";
	document.getElementById('answer_no').disabled = 'disabled';
	document.getElementById('answer_yes').disabled = 'disabled';
}

function verif_photo(id,choix_photo,reponse,compteur) {

	document.getElementsByClassName('reponse_quiz').item(0).innerHTML = (choix_photo == id) ? juste + " C'est " + reponse 
																							: faux + " C'est " + reponse;
	document.getElementById("choix").style.display = "block";
	var elt = document.getElementById("question").children;
	var i = 0;
	for (i = 0; i < elt.length; ++i) {
		if (i == compteur) elt.item(i).style.backgroundColor = "blue";
		elt.item(i).disabled = "disabled";
	}
	
}

function verif_commun(id,choix_acteur,nom) {
	document.getElementsByClassName("reponse_quiz").item(0).innerHTML = (choix_acteur == 1) 
		? juste + " La r&eacute;ponse est " +nom
		: faux  + " La r&eacute;ponse est " +nom;
	document.getElementById("choix").style.display = "block";
	var elt = document.getElementById("question").children;
	var i = 0;
	for (i = 0; i < elt.length; ++i) {
		if (i == id) elt.item(i).style.backgroundColor = "blue";
		elt.item(i).disabled = "disabled";
	}
}

function again() {
	window.location.reload(5);
}

function changerQuiz() {
	window.location = "./liste_quiz.php";
}
	
</script>
<?php 
	include("../includes/footer.php");
?>
