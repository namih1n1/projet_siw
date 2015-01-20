<?php
/******************************************************/
/*      ALIMENTATION DE LA TABLE success_movies       */
/*       Tous les films au box-office mondial         */
/******************************************************/

$dbh->exec("TRUNCATE TABLE success_movies" );

$sparql = "
	select distinct ?ressource
	where {
		?ressource 	rdf:type 				<http://schema.org/Movie> ; 
					rdfs:label 				?titre .
		<http://fr.dbpedia.org/resource/Liste_des_plus_gros_succès_du_box-office_mondial> 	dbpedia-owl:wikiPageWikiLink 	?ressource . 
		FILTER langmatches(lang(?titre),\"fr\") .
}
ORDER BY ?ressource
";
$list_success_films = sparql_query( $sparql );
if( !$list_success_films ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

unset($sparql);
unset($array_result);
$array_result = array();
$cpt = 1;

while( $row = sparql_fetch_array( $list_success_films ) )
{	
	$dbh->prepare("INSERT INTO success_movies 
			VALUES ( ". $cpt .", 
			\"" . substr($row['ressource'],strrpos($row['ressource'],"/")+1) . "\"
			)")->execute();
	$cpt++;
}
unset($list_succes_films);
echo "alim_success_movies : FINI <br />";

?>