<?php
/******************************************************/
/*      ALIMENTATION DE LA TABLE disney_movies        */
/*      	     Tous les films Disney       		  */
/******************************************************/

$dbh->exec("TRUNCATE TABLE disney_movies" );

// Récupération du dernier id de la table movies
$sth_lastid = $dbh->prepare("SELECT MAX(id_mov) as max FROM movies");
$sth_lastid->execute();
$last_id = $sth_lastid->fetchAll();
if( !$last_id ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

$cpt = $last_id[0]['max'] + 1;
$sparql = "
	select DISTINCT ?resfilm ?titre ?annee ?image
where {
 ?resfilm dbpedia-owl:producedBy ?studio ;
          rdf:type <http://schema.org/Movie> ;
          rdfs:label ?titre ;
          prop-fr:annéeDeSortie ?annee ;
          dcterms:subject <http://fr.dbpedia.org/resource/Catégorie:Long_métrage_d'animation_Disney> .
		  OPTIONAL {?resfilm dbpedia-owl:thumbnail ?image }
  FILTER langmatches(lang(?titre),\"fr\") .
  FILTER (?studio LIKE \"http*fr.dbpedia.org/resource/Walt_Disney_Pictures*\" OR ?studio LIKE \"http*fr.dbpedia.org/resource/Walt_Disney_Animation_Studios*\" ) .
}
ORDER BY DESC(?annee)
";
$list_disney_films = sparql_query( $sparql );
if( !$list_disney_films ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

unset($sparql);
unset($array_result);
$array_result = array();

while( $row = sparql_fetch_array( $list_disney_films ) )
{
	$url_img = isset($row['image']) ? $row['image'] : NULL;
	// Test d'existence des liens images
	$url_img = isset($row['image']) ? $row['image'] : NULL;
	if ($url_img != "") {
		if (@fclose(@fopen($url_img, "r"))) { 
			$url_img = $url_img;
		} else { // Erreur 404 = réécriture des liens
			$url_img = str_replace("commons/thumb","fr",$url_img);
			$url_img = substr($url_img,0,strrpos($url_img,"/"));
			
			if (@fclose(@fopen($url_img, "r"))) { 
				$url_img = $url_img;
			} else { 
				$url_img = "";
			}
		}
	}
	
	$traited_resource = substr($row['resfilm'],strrpos($row['resfilm'],"/")+1);
	$dbh->prepare("INSERT INTO disney_movies 
			VALUES ( ". $cpt .", 
			\"" . $traited_resource . "\",
			\"" . $row['titre'] . "\",
			\"" . $url_img . "\",
			". $row['annee'] ."
			)")->execute();
	$cpt++;
}
unset($list_disney_films);
echo "alim_disney_movies : FINI <br />";

?>