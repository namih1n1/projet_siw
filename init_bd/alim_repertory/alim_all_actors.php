<?php
/******************************************************/
/*         ALIMENTATION DE LA TABLE actors            */
/*     Tous les acteurs des films listés en bd        */
/******************************************************/

$dbh->exec("TRUNCATE TABLE actors" );

// Récupération des films de la base
$sth_mov = $dbh->prepare("SELECT id_mov, mov_resource FROM movies ORDER BY id_mov");
$sth_mov->execute();

$movies = $sth_mov->fetchAll();
if( !$movies ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

$cpt = 1;
// Parcours des acteurs
foreach($movies as $key => $tb) {
	$resource_film = "<".$__ressource.$tb['mov_resource'].">";
	// Requête SPARQL des acteurs du film courant
	$sparql = "
		select ?resactors ?nom ?naissance ?image
		where {
		   ".$resource_film."   	dbpedia-owl:starring  ?resactors .
		   ?resactors  				rdfs:label            ?nom .
			OPTIONAL {?resactors 	dbpedia-owl:birthDate ?naissance }
			OPTIONAL {?resactors	dbpedia-owl:thumbnail ?image }
		   FILTER langmatches(lang(?nom),\"fr\") .
		}
	";

	
	$list_actors = sparql_query( $sparql );
	if( !$list_actors ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	unset($sparql);

	// Parcours des acteurs du film courant
	while( $row = sparql_fetch_array( $list_actors ) )
	{
		$traited_resactor = substr($row['resactors'],strrpos($row['resactors'],"/")+1);
		$naissance = isset($row['naissance']) 	? $row['naissance'] : NULL;
		$url_img 	= isset($row['image']) 		? $row['image'] 	: NULL;
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
		
		$sth_actor = $dbh->prepare("SELECT * FROM actors WHERE act_resource LIKE \"%". $traited_resactor ."%\"");
		$sth_actor->execute();
		$acteur = $sth_actor->fetchAll();
		// Non-existence de l'acteur = ajout à la base
		if($acteur == null) {
			$dbh->prepare("INSERT INTO actors
					VALUES ( " . $cpt . ", 
					\"" . $traited_resactor . "\",
					\"" . $row['nom'] . "\",
					\"" . $naissance . "\",
					\"" . $url_img . "\",
					0
					)")->execute();
			$cpt++;
		}
	}
	unset($list_actors);
}
unset($movies);


// Mise à jour des acteurs au box-office
$upd = $dbh->prepare("
		UPDATE 	actors 
		SET 	act_is_success = 1 
		WHERE 	act_resource IN (SELECT DISTINCT sa_resource FROM success_actors)");
$upd->execute();
echo "alim_all_actors FINI\n";

?>