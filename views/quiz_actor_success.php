<?php
include("../includes/header.php");

$points = 0;
// Récupération de 20 films à succès aléatoires
$sth = $dbh->prepare("SELECT * FROM success_actors ORDER BY RAND() LIMIT 20");
$sth->execute();
$resfilm = $sth->fetchAll();

if( !$resfilm ) { print_r($dbh->errorInfo()); echo "\n"; exit; }
$cpt = 1;

$cpt == 0;
?>
<div class=\"question_quiz\">Quiz acteur </div>

<?php 
while($cpt < count($resfilm)/2) {
	echo "
	<p>Est-ce que \"" . utf8_decode($resfilm[$cpt]['sa_nom']) . "\" est n&eacute; avant \"" . utf8_decode($resfilm[$cpt+1]['sa_nom']) . "\" ?</p>
	<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no(". $resfilm[$cpt]['sa_naissance'] .",". $resfilm[$cpt+1]['sa_naissance'] .",".$points.")\" />
    <input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes(". $resfilm[$cpt]['sa_naissance'] .",". $resfilm[$cpt+1]['sa_naissance'] .",".$points.")\" />
	
	<div class=\"reponse_quiz\"></div>
	";
	$cpt = $cpt + 2;
}
	echo "<div class=\"point_quiz\">Vous avez <span id=\"cumul_point\"></span> points.</div>";

/*
$sparql = "
	SELECT distinct ?resactor ?nom ?naissance
	WHERE {
		?resfilm 	dbpedia-owl:starring 	?resactor ;
                                rdf:type 		?lien.
                ?films 		dbpedia-owl:wikiPageWikiLink 	?resfilm  .  
        
		?resactor	dbpedia-owl:birthDate  	?naissance ;
		rdfs:label				?nom .
		FILTER langmatches(lang(?nom),\"fr\").
                FILTER (?lien like \"http://schema.org/Movie\") .
                FILTER (?films like \"*Liste_des_plus_gros_succ*s_du_box-office_mondial*\") .

	}
ORDER BY RAND()
LIMIT 2

";

$two_success_movies = sparql_query( $sparql );
if( !$two_success_movies ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

unset($sparql);
$result = array(sparql_fetch_array( $two_success_movies ), sparql_fetch_array( $two_success_movies ));
echo "
	<div class=\"question_quiz\">Quiz acteur </div>
	<p>Est-ce que \"" . utf8_decode($result[0]['nom']) . "\" est n&eacute; avant \"" . utf8_decode($result[1]['nom']) . "\" ?</p>
    
	<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no(". $result[0]['naissance'] .",". $result[1]['naissance'] .")\" />
    <input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes(". $result[0]['naissance'] .",". $result[1]['naissance'] .")\" />
	
	<div class=\"reponse_quiz\"></div>
	
	<div id=\"choix\" style=\"display:none\">
		<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
		<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
		
		<div id=\"other_quiz\" style=\"display:none\">
			<a href=\"./quiz_films_old.php\" >Quiz sur les films &agrave; succ&egrave;s</a>
			
		</div>
	</div>
";
*/


echo "<script language=\"javascript\">
var juste 	= \"Vous avez raison !\";
var faux 	= \"Vous avez tort.\";
function answer_no(point,param1,param2){
	var score = point;
	if (param1 > param2) {
		score = score + 1;
	}
	
	(param1 > param2) 	? document.getElementsByClassName('reponse_quiz').item(0).innerHTML = juste
						: document.getElementsByClassName('reponse_quiz').item(0).innerHTML = faux;
	document.getElementById('choix').style.display = \"block\";
	document.getElementById('cumul_point').innerHTML = score;
	
}
function answer_yes(point,param1,param2){
	var score = point;
	if (param1 < param2) {
		score = score + 1;
		
	}
	(param1 < param2) 	? document.getElementsByClassName('reponse_quiz').item(0).innerHTML = juste
						: document.getElementsByClassName('reponse_quiz').item(0).innerHTML = faux;
	document.getElementById('choix').style.display = \"block\";
	document.getElementById('cumul_point').innerHTML = score;
}

function again() {
	window.location.reload(5);
}

function changerQuiz() {
	document.getElementById('other_quiz').style.display = \"block\";
}
</script>";

include("../includes/footer.php");
?>
