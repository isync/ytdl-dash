#!/usr/bin/php -q
<?php

if(!isset($argv[1])) die("Usage: php me.php <video id>\n");

array_shift($argv);
foreach ($argv as $vid) {
	hellohello($vid);	// echo status/current video to cli

	$tmp = maketemp();	// make temp dir

	download($vid, $tmp);	// dl and run avconv

	deltemp($tmp);		// del temp dir
}

function maketemp(){
	$tmp = tempnam("/tmp", "ytdl-");
	echo "Making $tmp/\n";
	unlink($tmp);
	mkdir($tmp);
	posix_mkfifo($tmp."/audio", 0600);
	posix_mkfifo($tmp."/video", 0600);
	return $tmp;
}
function deltemp($dir){
	echo "Deleting $dir/\n";
	rmstar($dir);
	rmdir($dir);
}
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
function extreplace($name, $ext){
	$pos = strrpos($name, ".");
	if($pos === false) return "$name.$ext";
	return substr($name, 0, $pos).".$ext";
}

function download($vid, $tmp){
	$temp = escapeshellarg($tmp);
	$url = escapeshellarg($vid);
	$audio_out = escapeshellarg($tmp."/audio");
	$video_out = escapeshellarg($tmp."/video");

	echo "Getting filename for $vid:\n";
	$cmd = "(cd $temp; youtube-dl --get-filename $url)";
	$out = trim(`$cmd`);
	$out = extreplace($out, "mkv");

	if(file_exists($out)){
		echo "Cannot download, file already exists.\n";
		return;
	}

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
	if(isset($fmt[138])){ $video=138; echo "Yay for >1080p content!\n"; } // 1440p DASH?
	else if(isset($fmt[137])) $video=137;		// 1080p DASH
	else if(isset($fmt[136])) $video=136;		// 720p DASH
	else if(isset($fmt[135])) $video=135;		// 480p DASH
	else if(isset($fmt[134])) $video=134;		// 360p DASH
	else if(isset($fmt[133])) $video=133;		// 480p DASH
	else listfmt($vid);

	$cmd = "(cd $temp; youtube-dl $url -f $video -o $video_out & youtube-dl $url -f $audio -o $audio_out & ) 1>&2";
	`$cmd`;
	avconv("$tmp/audio", "$tmp/video", $out);
}

function avconv($aud,$vid,$out){
	$aud = escapeshellarg($aud);
	$vid = escapeshellarg($vid);
	$out = escapeshellarg($out);
	echo ($cmd = "avconv -i $aud -i $vid -c copy $out 1>&2")."\n";
	`$cmd`;
}
function listfmt($vid){
	$url = escapeshellarg($vid);
	$cmd = "(youtube-dl $url -F) 1>&2";
	`$cmd`;
	die();
}
