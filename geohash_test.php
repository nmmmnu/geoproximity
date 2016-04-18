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
	if ($a["hash"] == $b["hash"])
		return 0;

	return $a["hash"] < $b["hash"] ? -1: +1;
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

