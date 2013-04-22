<?php
class EExpire{	const  MINUTE = 60;
	const  MINUTE_5 = 300;
	const  MINUTE_10 = 600;
	const  MINUTE_15 = 900;
	const  MINUTE_30 = 1800;
	const  HOUR = 3600;
	const  DAY = 86400;
	const  WEEK = 604800;
	const  MONTH = 2592000;
	const  YEAR = 31557600;

	static function minutes($count=1){
		return self::MINUTE*$count;
	}

	static function hours($count=1){
		return self::HOUR*$count;
	}

	static function days($count=1){
		return self::DAY*$count;
	}

	static function weeks($count=1){
		return self::WEEK*$count;
	}

	static function months($count=1){
		return self::MONTH*$count;
	}

	static function years($count=1){		return self::YEAR*$count;	}}