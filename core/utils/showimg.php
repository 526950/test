<?php
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');

function viewFoto($name, $w = 0, $h = 0, $mode = 'x') {
	$w_ = $w;
	$h_ = $h;
	if (!strpos($name, '://'))
		$name = ROOT . str_replace('//', '/', $name);
		// $name = preg_replace ( "'^(.*)(/temp/)(.+)$'i", "$2$3", $name );
	$ar_pic = @ getimagesize($name);
	if ($ar_pic) {
		switch ($ar_pic['mime']) {
			case 'image/png' :
				$img = imagecreatefrompng($name);
				break;
			case 'image/gif' :
				
				$img = imagecreatefromgif($name);
				break;
			case 'image/jpeg' :
				$img = imagecreatefromjpeg($name);
				break;
			default :
				exit();
		}
		$w_old = imagesx($img);
		$h_old = imagesy($img);
		if ($w != 0 & $h != 0 & ($w_old > $w || $h_old > $h)) {
			if ($mode == 'x') {
				
				$k1 = $w / imagesx($img);
				$k2 = $h / imagesy($img);
				$k = $k1 > $k2 ? $k2 : $k1;
				$w = intval(imagesx($img) * $k);
				$h = intval(imagesy($img) * $k);
				
				if ($ar_pic['mime'] == 'image/gif') {
					$img2 = imagecreatetruecolor($w, $h);
					$trnprt_indx = imagecolortransparent($img);
					if ($trnprt_indx >= 0) {
						$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
						$trnprt_indx = imagecolorallocate($img2, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
						imagefill($img2, 0, 0, $trnprt_indx);
						imagecolortransparent($img2, $trnprt_indx);
						imagecopyresampled($img2, $img, 0, 0, 0, 0, $w, $h, imagesx($img), imagesy($img));
					}
				} else {
					$img2 = imagecreatetruecolor($w, $h);
					imagesavealpha($img2, true);
					$trans_colour = imagecolorallocatealpha($img2, 0, 0, 0, 127);
					imagefill($img2, 0, 0, $trans_colour);
					imagecopyresampled($img2, $img, 0, 0, 0, 0, $w, $h, imagesx($img), imagesy($img));
				}
			} else {
				
				$k1 = $w / imagesx($img);
				$k2 = $h / imagesy($img);
				$k = $k1 < $k2 ? $k2 : $k1;
				$w = intval(imagesx($img) * $k);
				$h = intval(imagesy($img) * $k);
				
				$w1 = 0;
				$h1 = 0;
				
				$img2 = imagecreatetruecolor($w_, $h_);
				if ($h < imagesy($img)) {
					$h1 = intval((imagesy($img2) - $h) / 2) * -1;
				} else {
					$w1 = intval((imagesx($img2) - $w) / 2) * -1;
				}
				
				if ($ar_pic['mime'] == 'image/gif') {
					$trnprt_indx = imagecolortransparent($img);
					if ($trnprt_indx >= 0) {
						$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
						$trnprt_indx = imagecolorallocate($img2, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
						imagefill($img2, 0, 0, $trnprt_indx);
						imagecolortransparent($img2, $trnprt_indx);
						imagecopyresampled($img2, $img, 0, 0, $w1, $h1, $w, $h, imagesx($img), imagesy($img));
					}
				} else {
					imagesavealpha($img2, true);
					$trans_colour = imagecolorallocatealpha($img2, 0, 0, 0, 127);
					imagefill($img2, 0, 0, $trans_colour);
					imagecopyresampled($img2, $img, 0, 0, $w1, $h1, $w, $h, imagesx($img), imagesy($img));
				}
			}
		} else {
			if ($ar_pic['mime'] == 'image/gif') {
				$img2 = imagecreatetruecolor($w_old, $h_old);
				$trnprt_indx = imagecolortransparent($img);
				if ($trnprt_indx >= 0) {
					$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
					$trnprt_indx = imagecolorallocate($img2, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
					imagefill($img2, 0, 0, $trnprt_indx);
					imagecolortransparent($img2, $trnprt_indx);
					imagecopy($img2, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
				}
			} else {
				$img2 = imagecreatetruecolor(imagesx($img), imagesy($img));
				imagesavealpha($img2, true);
				$trans_colour = imagecolorallocatealpha($img2, 0, 0, 0, 127);
				imagefill($img2, 0, 0, $trans_colour);
				imagecopy($img2, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
			}
		}
		header('Content-type: ' . $ar_pic['mime']);
		switch ($ar_pic['mime']) {
			case 'image/png' :
				imagepng($img2);
				break;
			case 'image/gif' :
				imagegif($img2);
				break;
			default :
				imagejpeg($img2);
				break;
		}
		imagedestroy($img);
		imagedestroy($img2);
	}
}
if (isset($_REQUEST['img']))
	viewFoto($_REQUEST['img'], @$_REQUEST['w'], @$_REQUEST['h'], @$_REQUEST['mode']);