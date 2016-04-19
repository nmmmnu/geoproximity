<?php

class Sphere{
	const LAT_SIZE	= 180;		//  -90 to  +90
	const LON_SIZE	= 360;		// -180 to +180

	const DEG_RAD = 0.0174533;	// pi() / 180;

	public static function rad($deg){
		return $deg * Sphere::DEG_RAD;
	}

	public static function parallelSize($lat, $radius = 1){
		return cos(self::rad($lat)) * $radius / self::LON_SIZE;
	}
}

class Earth{
	const RADIUS	=  6371;	// km
	const EQUATOR	= 40075;	// ~2 * pi() * RADIUS
	const MERIDIAN	= 20038;	// EQUATOR / 2

	const LAT_KM	= 111.32;	// MERIDIAN / LAT_SIZE
	const KM_LAT	= 0.00898;	// LAT_SIZE / MERIDIAN

	public static function latDistance($lat1, $lat2){
		return abs($lat1 - $lat2) * self::LAT_KM;
	}

	public static function lonDistance($lon1, $lon2, $lat){
		return abs($lon1 - $lon2) * Sphere::parallelSize($lat, self::RADIUS);
	}

};

class SphereDistance_{
	private $radius_;

	public function __construct($radius){
		$this->radius_ = $radius;
	}

	public function __invoke($lat1, $lon1, $lat2, $lon2){
		$dLat = Sphere::rad(abs($lat1 - $lat2));
		$dLon = Sphere::rad(abs($lon1 - $lon2));

		$a =
			$this->square_(sin($dLat/2)) +
			cos($Sphere::rad($lat1)) * cos(Sphere::rad($lat2)) *
			$this->square_(sin($dLon/2))
		;

		$c = 2 * atan2( sqrt($a), sqrt(1 - $a) );

		$distance = $this->radius_ * $c;

		return $distance;
	}

	// ===============================

	private static function square_($a){
		return $a * $a;
	}
}

class EarthDistance extends SphereDistance_{
	public function __construct(){
		parent::__construct(Earth::RADIUS);
	}
}

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
			self::$LAT_MULTIPLIER	= self::GEO_MAX_UINT / Sphere::LAT_SIZE;
			self::$LON_MULTIPLIER	= self::GEO_MAX_UINT / Sphere::LON_SIZE;
		}
	}

	public function __invoke($lon, $lat){
		$a1 = self::rotateCyclical_($lat, Sphere::LAT_SIZE);
		$a2 = self::rotateCyclical_($lon, Sphere::LON_SIZE);

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
		//$corrector = 0;
		//$a += $corrector;

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

class BoundingBoxDistance{
	private static $KM_LAT2	= 0;

	private static $hash__	= null;

	public function __construct(){
		if (self::$KM_LAT2 == 0){
			self::$KM_LAT2 = Earth::KM_LAT / 2;

			self::$hash__ = new GeoHash();
		}

	}

	public function __invoke($lat, $lon, $km){
		// make km to lat
		$len = $km * self::$KM_LAT2;

		$h1 = self::hash_($lat - $len, $lon - $len);
		$h  = self::hash_($lat,        $lon       );
		$h2 = self::hash_($lat + $len, $lon + $len);

		printf("%s, %10.4f %10.4f %10d\n", "h1", $lat - $len, $lon - $len, $h1);
		printf("%s, %10.4f %10.4f %10d\n", "h ", $lat,        $lon       , $h );
		printf("%s, %10.4f %10.4f %10d\n", "h2", $lat + $len, $lon + $len, $h2);

		$mlen = min( abs($h1 - $h), abs($h2 - $h) );

		printf("%s\n", $mlen);

		return [
			$h1 - $mlen, $h1 + $mlen,
			$h2 - $mlen, $h2 + $mlen,
		];
	}

	private static function hash_($lat, $lon){
		return self::$hash__->__invoke($lat, $lon);
	}
}

