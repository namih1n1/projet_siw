<?php
include("../includes/header.php");
$id_actor = ( isset($_GET['id_actor']) ) ? $_GET['id_actor'] : -1;

$schema = "http://schema.org/Movie";

/* TO DO : traiter la nullité du paramètre

FIN TO DO */

// Récupération des informations de l'acteur
$sth_act = $dbh->prepare("
	SELECT 	sa_resource
			,sa_nom
			,sa_naissance
	FROM 	success_actors 
	WHERE	id_success_a = ".$id_actor);
$sth_act->execute();
$actor = $sth_act->fetchAll();
if( !$actor ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Récupération des films de l'acteur en SPARQL
$sparql = "
	select distinct ?resfilm ?titre ?year 
	where {
		?resfilm 	rdf:type 						?lien ; 
					rdfs:label 						?titre ;
					prop-fr:annéeDeSortie   		?year;
					dbpedia-owl:starring            ?acteur .
		FILTER (?lien like \"" . $schema . "\") .
		FILTER langmatches(lang(?titre),\"fr\") .
		FILTER (?acteur like \"*" . utf8_decode(str_replace($pattern,"*",$actor[0]['sa_resource'])) . "*\")
}
ORDER BY DESC(?year)
";

$list_movies = sparql_query( $sparql );
if( !$list_movies ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
unset($sparql);
// var_dump($list_movies);exit();
echo "<h2>" .sparql_num_rows($list_movies) . " films avec " . $actor[0]['sa_nom'] . ".</h2>";
echo "<table class='movies_table'>
        <tr><th>Films</th><th>Ann&eacute;e de sortie</th></tr>
";

// Parcours des films de l'acteur courant
while( $row = sparql_fetch_array( $list_movies ) )
{
	$traited_resfilm = substr($row['resfilm'],strrpos($row['resfilm'],"/")+1);
	
	echo "
    <tr>
        <td><a href=\"" . $__url_wiki . utf8_decode($traited_resfilm) . "\">" . utf8_decode($row['titre']) ."</td>
        <td>" . $row['year'] . "</td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
