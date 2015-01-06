<?php
include("./includes/header.php");


select ?titres ?actor where {
?Resource dbpedia-owl:starring ?actor ;
rdfs:label ?titres .
FILTER langmatches(lang(?titres),"fr") .
}
ORDER BY ?titres ?actor


?>
