<?php

trait GeoMath{
	private static $DEG_RAD_	= M_PI / 180;

	public static function rad($deg){
		return $deg * self::$DEG_RAD_;
	}

	public static function square($a){
		return $a * $a;
	}
}

class GeoSphere{
	use GeoMath;

	private static $LAT_SIZE	= 2 *  90;	//  -90 to  +90
	private static $LON_SIZE	= 2 * 180;	// -180 to +180

	private $radius_;

	private $equator_;
	private $equator_segment_;
	private $meridian_segment_;

	public function __construct($radius){
		$this->radius_ = $radius;

		$this->equator_		= $this->radius_ * 2 * M_PI;
		$this->equator_segment_	= $this->equator_ / self::$LON_SIZE;
		$this->meridian_segment_ = $this->radius_ * 1 * M_PI / self::$LAT_SIZE;
	}

	public function parallelSegment($latitude){
		return cos(self::rad($latitude)) * $this->equator_segment_;
	}

	public function equator(){
		return $this->meridian_segment_;
	}

	public function meridianSegment(){
		return $this->meridian_segment_;
	}

	public function distance_book($lat1, $lon1, $lat2, $lon2){
		$dLat = self::rad(abs($lat1 - $lat2));
		$dLon = self::rad(abs($lon1 - $lon2));

		$a =
			self::square(sin($dLat/2)) +
			cos(self::rad($lat1)) * cos(self::rad($lat2)) *
			self::square(sin($dLon/2))
		;

		$c = 2 * atan2( sqrt($a), sqrt(1 - $a) );

		$distance = $this->radius_ * $c;

		return $distance;
	}

	public function distance_lame($lat1, $lon1, $lat2, $lon2, $precise){
		$dLat = abs($lat1 - $lat2);
		$dLon = abs($lon1 - $lon2);

		$segment = $precise ?
				$this->parallelSegment( min($lat1, $lat2) + $dlat / 2 )
				:
				$this->equator_segment_;

		$x = $dLon * $segment;

		$y = $dLat * $this->meridian_segment_;

		$c = sqrt($x * $x + $y * $y);

		return $c;
	}

	public function distance($lat1, $lon1, $lat2, $lon2, $precise = -1){
		switch($precise){
			case 0 : return $this->distance_lame($lat1, $lon1, $lat2, $lon2, false);
			default:
			case 1 : return $this->distance_lame($lat1, $lon1, $lat2, $lon2, true);
			case 2 : return $this->distance_book($lat1, $lon1, $lat2, $lon2);
		}
	}
}

class EarthSphere extends GeoSphere{
	const RADIUS		=  6371;	// km

	const EQUATOR		= 40075;	// km
	const MERIDIAN_SIZE	= 20038;	// EQUATOR / 2

	const MERIDIAN_SEGMENT	= 111.32;	// MERIDIAN_SIZE / LAT_SIZE

	public function __construct(){
		parent::__construct(self::RADIUS);
	}
}



