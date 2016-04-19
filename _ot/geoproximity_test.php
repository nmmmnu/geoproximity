<?
require_once "geoproximity.php";

$distance = new EarthDistance();
$hash = new GeoHash();
$bdistance = new BoundingBoxDistance();

$coord = [
	"Sofia"		=> [ "city" => "Sofia",		"lat" =>  42.6977,	"lon" =>  23.3219 ],
	"Bozhurishte"	=> [ "city" => "Bozhurishte",	"lat" =>  42.7635,	"lon" =>  23.2020 ],
	"Plovdiv"	=> [ "city" => "Plovdiv",	"lat" =>  42.1354,	"lon" =>  24.7453 ],
	"Pazardzhik"	=> [ "city" => "Pazardzhik",	"lat" =>  42.1928,	"lon" =>  24.3336 ],
	"Stara Zagora"	=> [ "city" => "Stara Zagora",	"lat" =>  42.4258,	"lon" =>  25.6345 ],
	"Burgas"	=> [ "city" => "Burgas",	"lat" =>  42.5048,	"lon" =>  27.4626 ],
	"Varna"		=> [ "city" => "Varna",		"lat" =>  43.2141,	"lon" =>  27.9147 ],
	"Pleven"	=> [ "city" => "Pleven",	"lat" =>  43.4170,	"lon" =>  24.6067 ],
	"Lovech"	=> [ "city" => "Lovech",	"lat" =>  43.1370,	"lon" =>  24.7142 ],
	"Vratsa"	=> [ "city" => "Vratsa",	"lat" =>  43.2102,	"lon" =>  23.5529 ],

	"Berlin"	=> [ "city" => "Berlin",	"lat" =>  52.5200,	"lon" =>  13.4050 ],
	"Bonn"		=> [ "city" => "Bonn",		"lat" =>  50.7374,	"lon" =>   7.0982 ],
	"Cologne"	=> [ "city" => "Cologne",	"lat" =>  50.9375,	"lon" =>   6.9603 ],
	"Duesseldorf"	=> [ "city" => "Duesseldorf",	"lat" =>  51.2277,	"lon" =>   6.7735 ],
	"Duisburg"	=> [ "city" => "Duisburg",	"lat" =>  51.4344,	"lon" =>   6.7623 ],

	"Libourne, FR"	=> [ "city" => "Libourne, FR",	"lat" =>  44.9130,	"lon" =>  -0.2440 ],
	"Bergerac, FR"	=> [ "city" => "Bergerac, FR",	"lat" =>  44.8538,	"lon" =>   0.4834 ],

	"Quito, EC"	=> [ "city" => "Quito, EC",	"lat" =>  -0.1859,	"lon" => -78.7114 ],
	"Malchingu, EC"	=> [ "city" => "Malchingu, EC",	"lat" =>   0.0651,	"lon" => -78.4002 ],

	"Dubai"		=> [ "city" => "Dubai",		"lat" =>  25.0657,	"lon" =>  55.1713 ],
	"Abu Dhabi"	=> [ "city" => "Abu Dhabi",	"lat" =>  24.4666,	"lon" =>  54.3667 ],

	// This is "opposite" to BG
	"Afif, AE"	=> [ "city" => "Afif, AE",	"lat" =>  23.9052, "lon" =>  42.9125 ],
	"Ar Rass, AE"	=> [ "city" => "Ar Rass, AE",	"lat" =>  25.8517, "lon" =>  43.5222 ],
];

// =======================

foreach($coord as $k => & $c){
	$c["hash"] = $hash($c["lat"], $c["lon"]);
	$c["city"] = $k;
}

unset($c);

uasort($coord, function($a, $b){
	if ($a["hash"] == $b["hash"])
		return 0;

	return $a["hash"] < $b["hash"] ? -1: +1;
});

// =======================

$dcord = [
	[ "Sofia",		"Pleven"	],
	[ "Sofia",		"Burgas"	],
	[ "Pleven",		"Burgas"	],

	[ "Bonn",		"Cologne"	],
	[ "Bonn",		"Berlin"	],
	[ "Berlin",		"Cologne"	],

	[ "Libourne, FR",	"Bergerac, FR"	],
	[ "Bergerac, FR",	"Libourne, FR"	],
];

foreach($dcord as $dc){
	$c1 = $coord[ $dc[0] ];
	$c2 = $coord[ $dc[1] ];

	$d = $distance(
		$c1["lat"],	$c1["lon"],
		$c2["lat"],	$c2["lon"]
	);

	printf("%-14s -> %-14s = %4d km.\n", $dc[0], $dc[1], $d);
}

echo "-------------------------\n";

foreach($coord as $c){
        printf( "%-20s | %9.4f %9.4f | %14d %14x\n", $c["city"], $c["lon"], $c["lat"], $c["hash"], $c["hash"] );
}

echo "-------------------------\n";

//find_near("Sofia",		150);
find_near("Bergerac, FR",	150);

// =======================

function find_near($city, $km){
	global $coord;
	global $bdistance;

	$c = $coord[ $city ];

	$bb = $bdistance($c["lat"], $c["lon"], $km);

	printf("%20s near %4d km:\n", $city, $km);

print_r($bb);

	find_near2($bb[0], $bb[1], $c["lat"], $c["lon"]);
	find_near2($bb[2], $bb[3], $c["lat"], $c["lon"]);

	printf("\n");
}

function find_near2($h1, $h2, $c_lat, $c_lon){
	global $coord;
	global $distance;

	foreach($coord as $c)
		if ($c["hash"] >= $h1 && $c["hash"] <= $h2)
			printf( "%-20s | %14d | %4d km.\n",
				$c["city"], $c["hash"],
				$distance( $c_lat, $c_lon, $c["lat"], $c["lon"] )
			);
}

