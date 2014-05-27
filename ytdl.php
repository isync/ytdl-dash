#!/usr/bin/php -q
<?php

if(!isset($argv[1])) die("Usage: php me.php <video id>\n");

$tmp = "/tmp/".posix_getpid();
$vid = $argv[1];

hellohello($vid);

echo "Making $tmp/\n";
mkdir($tmp);
posix_mkfifo($tmp."/audio", 0600);
posix_mkfifo($tmp."/video", 0600);

download($vid, "out.mkv");

echo "Cleaning $tmp/\n";
rmstar($tmp);
rmdir($tmp);

function hellohello($vid){
	echo "|".str_repeat("=",70)."|\n";
	echo "|  $vid\n";
	echo "|".str_repeat("=",70)."\n";
}

function rmstar($dir){
	$d = scandir($dir);
	foreach($d as $f){
		if($f=="." || $f=="..") continue;
		unlink("$dir/$f");
	}
}

function download($vid,$out){
	global $tmp;
	$temp = escapeshellarg($tmp);
	$url = escapeshellarg($vid);
	$audio_out = escapeshellarg($tmp."/audio");
	$video_out = escapeshellarg($tmp."/video");


	echo "Getting formats for $vid:\n";
	$cmd = "(cd $temp; youtube-dl -F $url | grep DASH | sed 's/\t\t*/ /g' | cut -d ' ' -f 1)";
	$data = explode("\n", trim(`$cmd`));
	$fmt = array();
	foreach($data as $f){
		$f = intval($f);
		$fmt[$f]=true;
	}

	$audio = 0;
	if(isset($fmt[172])) $audio=172;	// 256k webm
	else if(isset($fmt[140])) $audio=140;	// 128k m4a
	else listfmt($vid);

	$video = 0;
	if(isset($fmt[138])){ $video=138;die("$vid is 1440p?\n");} // 1440p DASH?
	else if(isset($fmt[137])) $video=137;		// 1080p DASH
	else if(isset($fmt[136])) $video=136;		// 720p DASH
	else if(isset($fmt[135])) $video=135;		// 480p DASH
	else if(isset($fmt[134])) $video=134;		// 360p DASH
	else if(isset($fmt[133])) $video=133;		// 480p DASH
	else listfmt($vid);

	
	$cmd = "(cd $temp; youtube-dl $url -f $video -o $video_out & youtube-dl $url -f $audio -o $audio_out & ) 1>&2";
	`$cmd`;
	
	// mux!
	if(!file_exists("$done/$out")){
		avconv("$tmp/audio", "$tmp/video", $out);
	}
}

function avconv($aud,$vid,$out){
	$aud = escapeshellarg($aud);
	$vid = escapeshellarg($vid);
	$out = escapeshellarg($out);
	echo ($cmd = "avconv -i $aud -i $vid -codec copy $out 1>&2")."\n";
	`$cmd`;
}
function listfmt($vid){
	$url = escapeshellarg($vid);
	$cmd = "(youtube-dl $url -F) 1>&2";
	`$cmd`;
	die();
}
