<?php
require_once("geosphere.php");
require_once("geodata_test.php");


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

