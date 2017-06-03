<?php

const FILMAFFINITY_URL = "https://www.filmaffinity.com/es/";
require_once('workflows.php');
$wf = new Workflows();

$query = $argv[1];
$json = $wf->request( FILMAFFINITY_URL . "search-ac.ajax.php?action=searchTerm&term=".urlencode($query));
$json = json_decode(utf8_decode($json));
$int = 1;
$dom = new DOMDocument;
foreach($json->results as $film):
	$dom->loadHTML($film->label);
	$divs = $dom->getElementsByTagName("div");

	for ($i = 0; $i < $divs->length; $i++) {
		$item = $divs->item($i);
		$class = $item->getAttribute('class');
		if ($class == 'cast') {
			$cast = utf8_decode($item->nodeValue);
		} else if ($class == 'title') {
			$year = $item->getElementsByTagName('small')->item(0)->nodeValue;
		} else if ($class == 'see-all') {
			$seeAll = true;
		}
	}

	if ($seeAll) {
		$wf->result($int.'.'.time(), FILMAFFINITY_URL . 'search.php?stext='.$query.'&stype=all', "Buscar más resultados", '', 'noicon.png');
	} else {
		$wf->result($int.'.'.time(), FILMAFFINITY_URL . 'film'.$film->id.'.html', $film->value." ".$year, $cast, 'icon.png');
	}

	$int++;
endforeach;

$results = $wf->results();
if (count($results) == 0):
	$wf->result('noresults', FILMAFFINITY_URL . 'search.php?stext='.$query.'&stype=all', 'Sin resultados', 'No hemos encontrado nada con "'.$query.'". Buscar en FilmAffinity.', 'icon.png');
endif;

echo $wf->toxml();

?>
