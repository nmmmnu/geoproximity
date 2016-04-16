<?php
require_once("sphere.php");


test(new GeoSphere(EarthSphere::RADIUS));
test(new EarthSphere());


function test($sphere){
	printf("Meridian Size: %s\n", $sphere->meridianSegment());
	printf("Equator  Size: %s\n", $sphere->parallelSegment(0));
	printf("Equator2 Size: %s\n", $sphere->equator());
	printf("North    Size: %s\n", $sphere->parallelSegment(+89));
	printf("South    Size: %s\n", $sphere->parallelSegment(-89));

	$coord = coordinates();

	$destination_pairs = [
		[ "Sofia",		"Plovdiv"	],
                [ "Sofia",		"Burgas"	],

                [ "Bonn",		"Cologne"	],

                [ "Duesseldorf",	"Bonn"		],
                [ "Duesseldorf",	"Cologne"	],
                [ "Duesseldorf",	"Duisburg"	],

		[ "Libourne, FR",	"Bergerac, FR"	],
	];

	foreach($destination_pairs as $p)
		test_distance($sphere, $coord, $p[0], $p[1]);
}


function test_distance(&$sphere, &$coordinates, $n1, $n2){
	$c1 = $coordinates[$n1];
	$c2 = $coordinates[$n2];

	$pr = -1;
	//for($pr = 0; $pr < 3; ++$pr)
	printf("Distance %-15s to %-15s = %5d\n",
		$c1["name"],
		$c2["name"],

		$sphere->distance(
			$c1["lat"], $c1["lon"],
			$c2["lat"], $c2["lon"],
			$pr
		)
	);
}


function coordinates(){
	return [
		"Sofia"		=> [ "name" => "Sofia",		"lat" =>  42.6977,	"lon" =>  23.3219 ],
		"Bozhurishte"	=> [ "name" => "Bozhurishte",	"lat" =>  42.7635,	"lon" =>  23.2020 ],
		"Plovdiv"	=> [ "name" => "Plovdiv",	"lat" =>  42.1354,	"lon" =>  24.7453 ],
		"Pazardzhik"	=> [ "name" => "Pazardzhik",	"lat" =>  42.1928,	"lon" =>  24.3336 ],
		"Stara Zagora"	=> [ "name" => "Stara Zagora",	"lat" =>  42.4258,	"lon" =>  25.6345 ],
		"Burgas"	=> [ "name" => "Burgas",	"lat" =>  42.5048,	"lon" =>  27.4626 ],
		"Varna"		=> [ "name" => "Varna",		"lat" =>  43.2141,	"lon" =>  27.9147 ],
		"Pleven"	=> [ "name" => "Pleven",	"lat" =>  43.4170,	"lon" =>  24.6067 ],
		"Lovech"	=> [ "name" => "Lovech",	"lat" =>  43.1370,	"lon" =>  24.7142 ],
		"Vratsa"	=> [ "name" => "Vratsa",	"lat" =>  43.2102,	"lon" =>  23.5529 ],

		"Berlin"	=> [ "name" => "Berlin",	"lat" =>  52.5200,	"lon" =>  13.4050 ],
		"Bonn"		=> [ "name" => "Bonn",		"lat" =>  50.7374,	"lon" =>   7.0982 ],
		"Cologne"	=> [ "name" => "Cologne",	"lat" =>  50.9375,	"lon" =>   6.9603 ],
		"Duesseldorf"	=> [ "name" => "Duesseldorf",	"lat" =>  51.2277,	"lon" =>   6.7735 ],
		"Duisburg"	=> [ "name" => "Duisburg",	"lat" =>  51.4344,	"lon" =>   6.7623 ],

		"Libourne, FR"	=> [ "name" => "Libourne, FR",	"lat" =>  44.9130,	"lon" =>  -0.2440 ],
		"Bergerac, FR"	=> [ "name" => "Bergerac, FR",	"lat" =>  44.8538,	"lon" =>   0.4834 ],

		"Quito, EC"	=> [ "name" => "Quito, EC",	"lat" =>  -0.1859,	"lon" => -78.7114 ],
		"Malchingu, EC"	=> [ "name" => "Malchingu, EC",	"lat" =>   0.0651,	"lon" => -78.4002 ],

		"Dubai"		=> [ "name" => "Dubai",		"lat" =>  25.0657,	"lon" =>  55.1713 ],
		"Abu Dhabi"	=> [ "name" => "Abu Dhabi",	"lat" =>  24.4666,	"lon" =>  54.3667 ],

		// This is "opposite" to BG
		"Afif, AE"	=> [ "name" => "Afif, AE",	"lat" =>  23.9052,	"lon" =>  42.9125 ],
		"Ar Rass, AE"	=> [ "name" => "Ar Rass, AE",	"lat" =>  25.8517,	"lon" =>  43.5222 ],
	];
}

