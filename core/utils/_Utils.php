<?php

class _Utils {

	static function getUserIP() {
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$ip = $_SERVER['REMOTE_ADDR'];
		return $ip;
	}

	static function urlRemove($param = '') {
		if ($param != '' && strstr($_SERVER['REQUEST_URI'], $param)) {
			$pattern = array ("'^([^\?]*)\?" . $param . "(?:=[0-9]+)?$'","'^([^\?]*\?)" . $param . "(?:=[0-9]+)?&(.+)$'","'^(.*)&" . $param . "(?:=[0-9]+)?(.*)$'");
			$replacement = array ("$1?","$1$2&","$1$2&");
			return preg_replace($pattern, $replacement, $_SERVER['REQUEST_URI']);
		} else {
			if (strstr($_SERVER['REQUEST_URI'], '?')) {
				if (substr($_SERVER['REQUEST_URI'], -1) != '&' && substr($_SERVER['REQUEST_URI'], -1) != '?') {
					return $_SERVER['REQUEST_URI'] . '&';
				} else {
					return $_SERVER['REQUEST_URI'];
				}
			} else {
				return $_SERVER['REQUEST_URI'] . '?';
			}
		}
	}

	static function translitUrl($str, $dot=true) {
		if (defined('URL_CUT')) {
			$str = Utils::trimText($str, URL_CUT);
		}
		$str = mb_convert_case($str, MB_CASE_LOWER);
		$tr = array ("айс" => "ice","а" => "a","б" => "b","в" => "v","г" => "g","д" => "d","е" => "e","ё" => "e","ж" => "j","з" => "z","и" => "i","й" => "j","к" => "k","л" => "l","м" => "m","н" => "n","о" => "o","п" => "p","р" => "r","с" => "s","т" => "t","у" => "u","ф" => "f","х" => "h","ц" => "ts","ч" => "ch","ш" => "sh","щ" => "sch","ъ" => "y","ы" => "y","ь" => "","э" => "e","ю" => "yu","я" => "ya"," " => "-","." => "","/" => "_",";" => "",":" => "","Є" => "E","Ї" => "Y","І" => "I","є" => "e","ї" => "y","і" => "i");
		if (!$dot) unset($tr["."]);
		if (preg_match('/[^A-Za-z0-9_\-]/', $str)) {
			$str = strtr($str, $tr);
			$str = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $str);
		}
		
		return $str;
	}

	static function translitUrlRu($str) {
		if (defined('URL_CUT')) {
			$str = Utils::trimText($str, URL_CUT);
		}
		$str = mb_convert_case($str, MB_CASE_LOWER);
		$tr = array (" " => "-","." => "","," => "","/" => "_",";" => "",":" => "");
		
		$str = strtr($str, $tr);
		return $str;
	}
}