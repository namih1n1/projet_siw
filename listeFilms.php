<?php
include("./includes/header.php");
$actor = $_GET['actor'];

$sparql = "
select distinct ?titres where {
?Ressource dbpedia-owl:starring ?actor ;
rdfs:label ?titres
FILTER (?actor like \"".$actor."\") .
FILTER langmatches(lang(?titres),\"fr\") .
}";
$list_films = sparql_query( $sparql );
if( !$list_films ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
$fields = sparql_field_array( $list_films );
print sparql_num_rows( $list_films )." films avec ". $actor .".</p>";
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
$nom_films = utf8_decode(substr("$row[$field]",strrpos("$row[$field]","/")+1));
echo "<td><a href=\"".$__url_wiki. utf8_decode("$row[$field]")."\"> ".utf8_decode("$row[$field]"). "</td>";
}
print "</tr>";
}
print "</table>";
?>
