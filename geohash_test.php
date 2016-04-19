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
	return cmp_($a["hash"], $b["hash"]);
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

$cities = [
	"Sofia",
	"Bourgas",
	"Pleven",

	"Bonn",
	"Bonn",
	"Berlin",

	"Libourne, FR",
	"Bergerac, FR",
];

$spere = new EarthSphere();

$km = 150;

foreach($cities as $city){
	$city_c = $coord[ $city ];

	$bb = geoBoundingBox( $spere, $city_c["lat"], $city_c["lon"], $km );

	printf("%-14s\n", $city);
//	print_r($bb);

	$a = array_unique(
		find_($coord, $bb[0], $bb[1]) +
		find_($coord, $bb[2], $bb[3]) +
		find_($coord, $bb[4], $bb[5])
	);

	print_r($a);
}




function find_( & $coordinates, $min, $max){
	$a = [];
	foreach($coordinates as $c){
		//print_r($c);

		if (between_($c["hash"], $min, $max))
			$a[] = $c["name"];
	}

	return $a;
}

function between_($a, $min, $max){
	return $a >= $min && $a <= $max;
}

function cmp_($a, $b){
	if ($a == $b)
		return 0;

	return $a < $b ? -1: +1;
}

