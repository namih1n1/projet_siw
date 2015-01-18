<?php
/**********************************************************/
/*      ALIMENTATION DE LA TABLE oscar_disney_movie      */
/*         Les oscars obtenus par les films Disney    	  */
/**********************************************************/

// Purge de la table oscar_disney_movies
$dbh->exec("TRUNCATE TABLE oscar_disney_movie");

// Récupération des infos de chaque disney
$sth = $dbh->prepare("SELECT dm_id, dm_resource FROM disney_movies");
$sth->execute();
$resdisney = $sth->fetchAll();

if( !$resdisney ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Parcours des disney
foreach($resdisney as $key => $tb) {
	$resource_disney = "<".$__ressource . $tb['dm_resource'].">";

	// Recherche SPARQL des oscars obtenus pour le disney courant
	$sparql = "
		select DISTINCT ?oscresource ?categorieoscar
		where {
			".$resource_disney." 	dbpedia-owl:wikiPageWikiLink ?oscresource .
			?oscresource 			rdfs:label ?categorieoscar .
			FILTER (?oscresource LIKE \"http*fr.dbpedia.org/resource/Oscar_de*\" OR ?oscresource LIKE \"http*fr.dbpedia.org/resource/Oscar_du_meilleur*\" OR ?oscresource LIKE \"http*fr.dbpedia.org/resource/*Film_avec_un_Oscar*\" ) .
     FILTER langmatches(lang(?categorieoscar),\"fr\")
		} ";
		
	$list_oscar_disney = sparql_query( $sparql );
	if( !$list_oscar_disney ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	unset($sparql);
	
	// Parcours des acteurs du film courant
	while( $row = sparql_fetch_array( $list_oscar_disney ) )
	{
		$traited_resoscar = substr($row['oscresource'],strrpos($row['oscresource'],"/")+1);
		$traited_resoscar = substr($traited_resoscar,strrpos($traited_resoscar,utf8_decode("Catégorie:Film_avec_un_")+strlen(utf8_decode("Catégorie:Film_avec_un_"))));
		$sth_oscar = $dbh->prepare("
			SELECT * FROM oscar_disney_movie 
			WHERE osc_dm_id = " .$tb['dm_id']. "
			AND osc_dm_resource LIKE \"%". $traited_resoscar ."%\"");
		$sth_oscar->execute();
		
		// Non-existence de l'oscar = ajout à la base
		if($sth_oscar->fetchAll() == null) {
			$dbh->prepare("
				INSERT INTO oscar_disney_movie
				VALUES ( " . $tb['dm_id'] . ", 
						\"" . $traited_resoscar . "\",
						\"" . $row['categorieoscar'] . "\"
				)")->execute();
		}
	}
	unset($list_oscar_disney);
}
unset($resdisney);
echo "alim_oscar_disney_movie : FINI <br />";
?>