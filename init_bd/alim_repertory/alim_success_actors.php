<?php
/******************************************************/
/*      ALIMENTATION DE LA TABLE success_acteurs      */
/*       Tous les acteurs au box-office mondial       */
/******************************************************/

// Purge de la table success_actors
$dbh->exec("TRUNCATE TABLE success_actors");

// Récupération des infos de chaque film de success_movies
$sth = $dbh->prepare("SELECT id_success_m, sm_resource FROM success_movies");
$sth->execute();
$resfilm = $sth->fetchAll();

if( !$resfilm ) { print_r($dbh->errorInfo()); echo "\n"; exit; }
$cpt = 1;

// Parcours des films
foreach($resfilm as $key => $tb) {
	$resource_film = "<".$__ressource . $tb['sm_resource'].">";

	// Recherche SPARQL des acteurs du film courant
	$sparql = "
		select ?resactor ?nom ?naissance ?image ?nationalite
		where {
			".$resource_film." 	dbpedia-owl:starring 	?resactor .
			?resactor 			dbpedia-owl:birthDate  	?naissance ;
								rdfs:label 				?nom .
			OPTIONAL {?resactor dbpedia-owl:thumbnail 	?image }
			OPTIONAL {?resactor	dbpedia-owl:nationality	?nationalite } 
			FILTER langmatches(lang(?nom), \"fr\") .
		}";

	$list_success_actors = sparql_query( $sparql );
	if( !$list_success_actors ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	unset($sparql);
	
	// Parcours des acteurs du film courant
	while( $row = sparql_fetch_array( $list_success_actors ) )
	{
		$traited_resnom = substr($row['resactor'],strrpos($row['resactor'],"/")+1);
		$sth_actor = $dbh->prepare("SELECT * FROM success_actors WHERE sa_resource LIKE \"%". $traited_resnom ."%\"");
		$sth_actor->execute();
		
		// Non-existence de l'acteur = ajout à la base
		if(count($sth_actor->fetchAll()) == 0) {
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
			
			$nation 	= isset($row['nationalite']) ? substr($row['nationalite'],strrpos($row['nationalite'],"/")+1) : NULL;
			$dbh->prepare("INSERT INTO success_actors
					VALUES ( " . $cpt . ", 
					\"" . $traited_resnom . "\",
					\"" . $row['nom'] . "\",
					\"" . $row['naissance'] . "\",
					\"" . $nation . "\",
					\"" . $url_img . "\",
					\"\"
					)")->execute();
			$cpt++;
		}
		$sth_id_films = $dbh->prepare("SELECT list_idfilms FROM success_actors WHERE sa_resource LIKE \"%". $traited_resnom ."%\"");
		$sth_id_films->execute();
		$list_id_films = $sth_id_films->fetchAll();
		$new_list_id_film = $list_id_films[0]['list_idfilms']. ",". $tb['id_success_m'];

		// Insertion id du film
		$dbh->prepare("UPDATE success_actors SET list_idfilms = \"". $new_list_id_film. "\" WHERE sa_resource LIKE \"%". $traited_resnom ."%\"")->execute();

	}
	unset($list_success_actors);
}
unset($resfilm);
echo "alim_success_actors : FINI <br />";

?>