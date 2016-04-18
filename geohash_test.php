<?
require_once("geohash.php");
require_once("geodata_test.php");

$geohash = new GeoHash();

$coord = coordinates();

foreach($coord as & $c){
	$c["hash"] = $geohash($c["lat"], $c["lon"]);
}

unset($c);

uasort($coord, function($a, $b){
	return compare_($a["hash"], $b["hash"]);
});

foreach($coord as $c){
	printf("%-20s %8.4f %8.4f %12s %8x\n",
		$c["name"],
		$c["lat"],
		$c["lon"],
		$c["hash"],
		$c["hash"]
	);
}





function compare_($a, $b){
	if ($a == $b)
		return 0;

	return $a < $b ? -1: +1;
}

