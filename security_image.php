<?php
/*
Filename: security_image.php
Last Modified: 3/14/2007

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

NOTICE:
This better way of preventing spammers was provided by the link below:
http://www.reconn.us/random_image.html

I made only minor changes compared to the original one they used.
*/
//start a session
session_start();

// make a string with all the characters that we 
// want to use as the verification code
$alphanum  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

// generate the verication code 
$rand_str = substr(str_shuffle($alphanum), 0, 5);

//Get each letter in one valiable, we will format all letters different
$letter1=substr($rand_str,0,1);
$letter2=substr($rand_str,1,1);
$letter3=substr($rand_str,2,1);
$letter4=substr($rand_str,3,1);
$letter5=substr($rand_str,4,1);

//Creates an image from a jpeg file.
$image=imagecreatefromjpeg("images/noise.jpg");

//Get a random angle for each letter to be rotated with.
$angle1 = rand(-20, 20);
$angle2 = rand(-20, 20);
$angle3 = rand(-20, 20);
$angle4 = rand(-20, 20);
$angle5 = rand(-20, 20);

//Get a random font. (In this examples, the fonts are located in "fonts" directory and named from 1.ttf to 5.ttf)
$font1 = "font/".rand(1, 5).".ttf";
$font2 = "font/".rand(1, 5).".ttf";
$font3 = "font/".rand(1, 5).".ttf";
$font4 = "font/".rand(1, 5).".ttf";
$font5 = "font/".rand(1, 5).".ttf";

//Define a table with colors (the values are the RGB components for each color).
$colors[0]=array(122,229,112);
$colors[1]=array(85,178,85);
$colors[2]=array(226,108,97);
$colors[3]=array(141,214,210);
$colors[4]=array(214,141,205);
$colors[5]=array(100,138,204);

//Get a random color for each letter.
$color1=rand(0, 5);
$color2=rand(0, 5);
$color3=rand(0, 5);
$color4=rand(0, 5);
$color5=rand(0, 5);

//Allocate colors for letters.
$textColor1 = imagecolorallocate ($image, $colors[$color1][0],$colors[$color1][1], $colors[$color1][2]);
$textColor2 = imagecolorallocate ($image, $colors[$color2][0],$colors[$color2][1], $colors[$color2][2]);
$textColor3 = imagecolorallocate ($image, $colors[$color3][0],$colors[$color3][1], $colors[$color3][2]);
$textColor4 = imagecolorallocate ($image, $colors[$color4][0],$colors[$color4][1], $colors[$color4][2]);
$textColor5 = imagecolorallocate ($image, $colors[$color5][0],$colors[$color5][1], $colors[$color5][2]);

//Write text to the image using TrueType fonts.
$size = 20;
$y = $size+15;
imagettftext($image, $size, $angle1, 10, $y, $textColor1, $font1, $letter1);
imagettftext($image, $size, $angle2, 35, $y, $textColor2, $font2, $letter2);
imagettftext($image, $size, $angle3, 60, $y, $textColor3, $font3, $letter3);
imagettftext($image, $size, $angle4, 85, $y, $textColor4, $font4, $letter4);
imagettftext($image, $size, $angle5, 110, $y, $textColor5, $font5, $letter5);

//We memorize the md5 sum of the string into a session variable
$_SESSION['image_value'] = md5($rand_str);

// Date in the past 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 

// always modified 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 

// HTTP/1.1 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 

// HTTP/1.0 
header("Pragma: no-cache"); 

header('Content-type: image/jpeg');
//Output image to browser
imagejpeg($image);
//Destroys the image
imagedestroy($image);
?>
