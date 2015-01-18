<?php
include("../includes/header.php");

$type_quiz = rand(1,2);

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
	
	// Mélange du tablau de résultat
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
			<div class=\"photo_actor\"><img src=\"".utf8_decode($rnd_quizactor[$choix_photo]['sa_url_image'])."\" width='200px' height='300px' /></div>
		</div>
		<div id=\"question\">
			<input type=\"button\" name=\"choix_0\" value=\"".utf8_decode($rnd_quizactor[0]['sa_nom'])."\" onclick=\"verif_photo(0,".$choix_photo.")\"  />
			<input type=\"button\" name=\"choix_1\" value=\"".utf8_decode($rnd_quizactor[1]['sa_nom'])."\" onclick=\"verif_photo(1,".$choix_photo.")\"  />
			<input type=\"button\" name=\"choix_2\" value=\"".utf8_decode($rnd_quizactor[2]['sa_nom'])."\" onclick=\"verif_photo(2,".$choix_photo.")\"  />
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

function again() {
	window.location.reload(5);
}

function changerQuiz() {
	window.location = \"./liste_quiz.php\";
}
	
</script>";

include("../includes/footer.php");
?>
