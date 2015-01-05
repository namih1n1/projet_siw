<?php
include("./includes/header.php");
$sparql = "
 select ?nom where {
 ?Ressource foaf:name ?nom .
 ?Ressource rdf:type ?profession .
 ?Ressource dbpedia-owl:wikiPageWikiLink dbpedia-fr:Hollywood .
 ?Ressource dbpedia-owl:wikiPageWikiLink dbpedia-fr:Cinéma .
 FILTER (?profession like \"*Actor*\") .
 }
 ORDER BY ?nom";
$list_actor = sparql_query( $sparql );
if( !$list_actor ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
$fields = sparql_field_array( $list_actor );
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
echo "<td><a href=\"" . $__url_wiki . utf8_decode("$row[$field]") ."\"> ".utf8_decode("$row[$field]"). "</td>";
}
print "</tr>";
}
print "</table>";
?>
