<?php

$description = '[hidden] [locked] [value=435 is the shit]This should be hidden';

preg_match_all('/\[([^[]+)\]/', $description, $matches);

echo '<pre>';
var_dump($matches);
echo '</pre>';

foreach($matches[0] AS $match){
	$description = str_replace($match, '', $description);
}

foreach($matches[1] AS $match){
	if (false !== strpos($match, '=')){
		$match_key = substr($match, 0, strpos($match,'='));
		$match_value = substr($match, strpos($match,'=')+1);
		
		echo $match_key . ': ' . $match_value;
	} else {
		echo $match . ': TRUE';
	}
	
	echo '<br />';
}

$description = trim($description);

var_dump($description);


