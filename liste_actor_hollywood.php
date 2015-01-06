<?php
include("./includes/header.php");

$sparql = "
select distinct ?Ressource ?birth 
where {
    ?Ressource foaf:name ?o ;
               rdf:type ?profession;
               dbpedia-owl:birthDate ?birth;
               dbpedia-owl:wikiPageWikiLink dbpedia-fr:Hollywood ;
               dbpedia-owl:wikiPageWikiLink ?autrecritere .
FILTER (?profession like \"*Actor*\") .
FILTER (?autrecritere like \"*Acteur*\")
}
ORDER BY ?Ressource";

$list_actor = sparql_query( $sparql );
if( !$list_actor ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
$fields = sparql_field_array( $list_actor );
$array_result = array();
while( $row = sparql_fetch_array( $list_actor ) )
{
    array_result['nom'] = utf8_decode(substr("$row['$field'Ressource']",strrpos("$row['$field'Ressource']","/")+1));
    array_result['birth'] = $row['birth'];
 //print_r($row);

}
print_r($array_result);
/*
print sparql_num_rows( $list_actor )." acteurs d'Hollywood.</p>";
print "<table class='example_table'>";
print "<tr>";
// Entête
// foreach( $fields as $field ) { print "<th>$field</th>"; }
print "</tr>";
while( $row = sparql_fetch_array( $list_actor ) )
{
print "<tr>";
foreach( $fields as $field )
{
$nom_actor = utf8_decode(substr("$row[$field]",strrpos("$row[$field]","/")+1)); 
echo "<td><a href=\"" . $__url_wiki . $nom_actor ."\"> ".$nom_actor. "</a> -- <a href=\"./listeFilms.php?actor=$nom_actor\" >Voir ses films</a></td>";
}
print "</tr>";
}
print "</table>";
*/
?>
