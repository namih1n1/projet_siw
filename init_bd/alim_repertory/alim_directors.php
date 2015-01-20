<?php
/******************************************************/
/*      ALIMENTATION DE LA TABLE directors     		  */
/*         Tous les réalisateurs de movies            */
/******************************************************/

$dbh->exec("TRUNCATE TABLE directors" );

// Récupération des ressources de movies
$sth_ress = $dbh->prepare("SELECT id_mov, mov_resource FROM movies");
$sth_ress->execute();
$list_ress = $sth_ress->fetchAll();
if( !$list_ress ) { print_r($dbh->errorInfo()); echo "\n"; exit; }
$cpt = 1;
// Requête SPARQL de récupération des réalisateurs des ressources récupérés
foreach($list_ress as $key => $tb) {
	$res_mov = "<".$__ressource . $tb['mov_resource'].">";
	$sparql = "
		select ?resreal ?nom ?naissance ?image
		where {
			".$res_mov."   dbpedia-owl:director   ?resreal .
			?resreal rdfs:label ?nom .
			OPTIONAL {?resreal dbpedia-owl:birthDate ?naissance} .
			OPTIONAL {?resreal dbpedia-owl:thumbnail ?image} .

			FILTER langmatches(lang(?nom),\"fr\") .
		}
	";
	$list_real = sparql_query( $sparql );
	if( !$list_real ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	unset($sparql);
	
	while( $row = sparql_fetch_array( $list_real ) )
	{
		$traited_resreal = substr($row['resreal'],strrpos($row['resreal'],"/")+1);
		// Traitement des OPTIONAL
		$naissance 	= (isset($row['naissance']))	? $row['naissance']		: NULL;
		$url_img 	= isset($row['image']) 			? $row['image'] : NULL;
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
		
		// Non-existence du réalisateur dans directors :
		// ajout dans directors et dans link_directors_movies
		$sth_exist = $dbh->prepare("SELECT id_dir FROM directors WHERE dir_resource LIKE \"%".$traited_resreal."%\"");
		$sth_exist->execute();
		$occ = $sth_exist->fetchAll();
		
		if ($occ == null) {
			$sth_insert = $dbh->prepare("INSERT INTO directors VALUES (
				".$cpt.",
				\"".$traited_resreal."\",
				\"".$row['nom']."\",
				\"".$naissance."\",
				\"".$url_img."\"
				)")->execute();
			
			$sth_upd = $dbh->prepare("INSERT INTO link_directors_movies VALUES (".$cpt.",".$tb['id_mov'].")");
			$sth_upd->execute();
			
			$cpt++;
		}
		else {
			// Ajout du lien director-movie
			$sth_upd = $dbh->prepare("INSERT INTO link_directors_movies VALUES (".$occ[0]['id_dir'].",".$tb['id_mov'].")");
			$sth_upd->execute();
		}
	}
	unset($list_real);
}
unset($list_ress);
echo "alim_directors : FINI <br />alim_link_directors_movies : FINI \n";

?>