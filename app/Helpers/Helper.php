<?php
namespace App\Helpers;
use App\Models\User;

class Helper{

	public static function str_random(){
	    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 

	    $length = 64;
	    $token = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
	    return $token;
	}
	public static function generateOTP($length){
		$string		=	'0123456789';
		$strShuffled=	str_shuffle($string);
		$otp		=	substr($strShuffled, 1, $length);
		return $otp;
	}
	public static function createUserReferal($length){
		$string		=	'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ9630125478abcdefghijklmnopqrstuvwxyz9876543210';
		$strShuffled=	str_shuffle($string);
		$referCode	=	substr($strShuffled, 1, $length);
		return $referCode;
	}
	public static function get_tot_users(){
		return User::where(["role"=>6])->count(); // Role -> 6 (Subadmin)
	}
	public static function pr($arr = []){
		echo "<pre>"; print_r($arr); echo "</pre>";
	}
}
?>