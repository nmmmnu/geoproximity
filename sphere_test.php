<?php
require_once("sphere.php");


test(new GeoSphere(EarthSphere::RADIUS));
test(new EarthSphere());


function test($sphere){
	$coordinates = [
		"Sofia"		=> [ "name" => "Sofia",		"lat" =>  42.6977,	"lon" =>  23.3219 ],
		"Burgas"	=> [ "name" => "Burgas",	"lat" =>  42.5048,	"lon" =>  27.4626 ],
		"Plovdiv"	=> [ "name" => "Plovdiv",	"lat" =>  42.1354,	"lon" =>  24.7453 ],

		"Bonn"		=> [ "name" => "Bonn",		"lat" =>  50.7374, "lon" =>   7.0982 ],
		"Cologne"	=> [ "name" => "Cologne",	"lat" =>  50.9375, "lon" =>   6.9603 ],
		"Duesseldorf"	=> [ "name" => "Duesseldorf",	"lat" =>  51.2277, "lon" =>   6.7735 ],

		"Libourne, FR"	=> [ "name" => "Libourne, FR",	"lat" =>  44.9130, "lon" =>  -0.2440 ],
		"Bergerac, FR"	=> [ "name" => "Bergerac, FR",	"lat" =>  44.8538, "lon" =>   0.4834 ],

	];

	printf("Meridian Size: %s\n", $sphere->meridianSegment());
	printf("Equator  Size: %s\n", $sphere->parallelSegment(0));
	printf("North    Size: %s\n", $sphere->parallelSegment(89));

	test_distance($sphere, $coordinates, "Sofia",		"Plovdiv"	);
	test_distance($sphere, $coordinates, "Sofia",		"Burgas"	);

	test_distance($sphere, $coordinates, "Bonn",		"Cologne"	);
	test_distance($sphere, $coordinates, "Bonn",		"Duesseldorf"	);

	test_distance($sphere, $coordinates, "Cologne",		"Duesseldorf"	);

	test_distance($sphere, $coordinates, "Libourne, FR",	"Bergerac, FR"	);

}

function test_distance($sphere, $coordinates, $n1, $n2){
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

