<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

$text = intval( $_GET['text']);
//$image_file ="shirt.png";
$image_file = dirname(__FILE__) .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,urldecode($_GET['picpath']));
if(is_file($image_file)) {
	$data = getimagesize ( $image_file );
	$image = imagecreatefrompng ( $image_file );
	$textcolor = imagecolorallocate ( $image, 0, 0, 0 );
	$xpos = ( $text > 9 )?9:12;
	if ( function_exists ( 'imagesavealpha' ) )
	{
		imageAlphaBlending ( $image, false );
		imageSaveAlpha ( $image, true );
	}
	imagestring ( $image, 2, $xpos, 1, $text, $textcolor);
	header ( "Content-Type: image/png" );
	imagepng ( $image );
	imagedestroy ( $image );
} else {
	echo 'cannot find template picture in ' . $image_file;
}