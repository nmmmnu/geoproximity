<?php
require_once("geosphere.php");

class GeoHash{
	const DEBUG		= false;

	const BITS		= 16;		// uint16_t, hashes are uint32_t

	const GEO_MAX_UINT	= 0xffff;	// uint16_t gives 65K points
						// Equator is 40K km.
						// so resolution is less 1 km

	public static $LAT_MULTIPLIER	= 0;
	public static $LON_MULTIPLIER	= 0;

	// =======================

	public function __construct(){
		if (self::$LAT_MULTIPLIER == 0){
			self::$LAT_MULTIPLIER	= self::GEO_MAX_UINT / GeoSphere::$LAT_SIZE;
			self::$LON_MULTIPLIER	= self::GEO_MAX_UINT / GeoSphere::$LON_SIZE;
		}
	}

	public function __invoke($lon, $lat){
		$a1 = self::rotateCyclical_($lat, GeoSphere::$LAT_SIZE);
		$a2 = self::rotateCyclical_($lon, GeoSphere::$LON_SIZE);

		$result = self::combineBits_(
			$a1 * self::$LAT_MULTIPLIER,
			$a2 * self::$LON_MULTIPLIER
		);

		if (self::DEBUG){
			printf("a1  = %s\n", decbin($a1 * self::$LAT_MULTIPLIER	));
			printf("a2  = %s\n", decbin($a2 * self::$LON_MULTIPLIER	));
			printf("res = %s\n", decbin($result			));
			printf("\n");
		}

		return $result;
	}

	// ===============================

	private static function rotateCyclical_($a, $size){
		while($a < 0)
			$a += $size;

		while($a > $size)
			$a -= $size;

		return $a;
	}

	private static function combineBits_($a1, $a2){
		$a = [ $a1, $a2 ];

		$result = 0;
		$result_bit = 1;

		$bit = 1;
		for($i = 0; $i < self::BITS; ++$i){
			for($j = 0; $j < 2; ++$j){
				if ($a[$j] & $bit)
					$result = $result | $result_bit;

				$result_bit = $result_bit << 1;
			}

			$bit = $bit << 1;
		}

		return $result;
	}

}

function geoBoundingBox(GeoSphere $sphere, $lat, $lon, $km){
	$hash = new GeoHash();
//	$h = $hash($lat, $lon);

	$lenLat = $km / $sphere->meridianSegment();
	$lenLon = $km / $sphere->parallelSegment($lon);

	$hlen  = $hash($lenLat, $lenLon);

	$h0  = $hash($lat, $lon);
	$h01 = $h0 - $hlen;
	$h02 = $h0 + $hlen;

	$h1 = $hash($lat - $lenLat, $lon - $lenLon);
	$h11 = $h1 - $hlen;
	$h12 = $h1 + $hlen;

	$h2 = $hash($lat + $lenLat, $lon + $lenLon);
	$h21 = $h2 - $hlen;
	$h22 = $h2 + $hlen;

	return [
		$h01, $h02,
		$h11, $h12,
		$h21, $h22,
	];
}

