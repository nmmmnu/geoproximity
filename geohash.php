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

