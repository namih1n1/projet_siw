<?php
include("./includes/header.php");
$actor = $_GET['actor'];

$sparql = "
select distinct ?titres where {
?Ressource dbpedia-owl:starring ?actor ;
prop-fr:titre ?titres
FILTER (?actor like \"*".$actor."*\") .
FILTER langmatches(lang(?titres),\"fr\") .
}";

$list_films = sparql_query( $sparql );
if( !$list_films ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
$fields = sparql_field_array( $list_films );
$array_result = array();
$cpt = 0;
while( $row = sparql_fetch_array( $list_films ) )
{
  $array_result[] = array('id'      => $cpt,
                          'titre'   => $row['titres'],
                          'actors'  => array()
                    );
  $sparql_2 = "
    select ?actors where {
    ".html_entity_decode('<')."http://fr.dbpedia.org/resource/".$row['titres'].html_entity_decode('>')."  dbpedia-owl:starring ?actors ;
      prop-fr:titre ?titre .
    FILTER langmatches(lang(?titre),\"fr\") .
  }
  ORDER BY ?actors
  ";
  $list_actors = sparql_query( $sparql_2 );
  if( !$list_actors ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
  while( $row_2 = sparql_fetch_array( $list_actors ) )
  {
    array_push($array_result['actors'],$row_2['actors']);
  }
  $cpt++;
}
echo "<pre>";
print_r($array_result);
echo "</pre>";
?>
