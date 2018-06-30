<?php

include('simple_html_dom.php');

$fileSongsJson = dirname(__FILE__)."/spotifySongs.json";

$spotifyPlaylistEmbedCode = '';

// --------------------------------––-----------------------------------------
// -------------------------------- FUNCIONES --------------------------------

function checkFileExist($file){
	if(empty($file)){
		echo '<span style="color:red">Error: File '.$file.' does not exist.</span><br/>'."\n";
		exit;
	}
	if(!file_exists($file)){
		echo '<span style="color:red">Error: File '.$file.' does not exist.</span><br/>'."\n";
		exit;
	}
}

function getSpotifyPlaylistUrlFromEmbedCode($spotifyPlaylistEmbedCode){
	echo "Getting Spotify Playlist URL from Embed Code";
	flush(); ob_flush();
	$urlPlaylistSpotify = '';
	if(empty($spotifyPlaylistEmbedCode)){
		echo '<span style="color:red"> Error: No Spotify Embed Code of Playlist </span><br/>'."\n";
		exit;
	}


	if (strpos($spotifyPlaylistEmbedCode, 'embed') === false){
		echo '<span style="color:red"> Error: Bad Spotify URL of Playlist (You must copy the Embed Code)</span><br/>'."\n";
		exit;
	}

	preg_match('/src="([^"]+)"/', $spotifyPlaylistEmbedCode, $match);
	if(empty($match[1])){
		echo '<span style="color:red"> Error: Bad Spotify Embed Code URL of Playlist </span><br/>'."\n";
		exit;
	}
	$urlPlaylistSpotify = $match[1];

	if (strpos($urlPlaylistSpotify, 'open.spotify.com') !== false){
		if (strpos($urlPlaylistSpotify, 'embed') !== false){
			echo '<span style="color:#090"> Done </span><br/>'."\n";
		}else{
			echo '<span style="color:red"> Error: Bad Spotify URL of Playlist (You must copy the Embed Code)</span><br/>'."\n";
			exit;
		}
	}else{
		echo '<span style="color:red"> Error: Bad Spotify URL of Playlist </span><br/>'."\n";
		exit;
	}
	echo '<br>';
	return $urlPlaylistSpotify;
}


function getWebPage( $url ){
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}

function youtubeIdFromHtml($html) {
	preg_match_all("/href=[\"\']?(\/watch\?v=([a-z_A-Z0-9\-]{11}))[\"\']?/", $html, $matches);
    return $matches[2];
}

function sleepDot($sleepSec){
	echo "Waiting $sleepSec seg for next ";
	for($x=0; $x<=$sleepSec; $x++) {
		echo '.';
		flush(); ob_flush();
		sleep(1);
	}
	flush(); ob_flush();
	echo '<br>';
}

// Get Youtube Link and put it on a jsonFile
function getYoutubeLink($spotifySongs){
	global $fileSongsJson;
	checkFileExist($fileSongsJson);
	$cont = 0;
	echo "Getting Youtube Link";
	echo '<br>';
	flush(); ob_flush();
	$count = count($spotifySongs);
	foreach ($spotifySongs as $song) {
		sleepDot(15);
		$songName = str_replace(' ','+',$song["songName"]);
		$artistName = str_replace(' ','+',$song["artistName"]);
		$auxCont = $cont + 1;
		echo ' - Getting Youtube Link of: '.$artistName.'+'.$songName.' ('.$auxCont.' of '.$count.')';
		flush(); ob_flush();
		$url = 'https://www.youtube.com/results?search_query='.$artistName.'+'.$songName;
		//echo $url; continue;
		$page = file_get_contents($url);
		//$page = file_get_contents("https://www.youtube.com/feed/Barns+Courtney+Fire");
		//$page = file_get_contents("https://www.youtube.com/results?search_query=Barns+Courtney+Fire");
		//print_r($page); exit;
		$video_ids = array();
		$i = 1;
		foreach ( youtubeIdFromHtml($page) as $video_id){
			if ($i%2){
				$link = 'https://www.youtube.com/watch?v='.$video_id;
				$video_ids[] = $video_id;
			}
			$i++;
		}
		if(empty($video_ids)){
			echo '<span style="color:red"> Error: No Youtube video for '.$songName.'</span><br/>'."\n";
			flush(); ob_flush();
			continue;
		}
		$spotifySongs[$cont]['video_ids'] = $video_ids;
		file_put_contents($fileSongsJson, json_encode($spotifySongs));
		$cont++;
		echo '<span style="color:#090"> Done</span><br/>'."\n";
		flush(); ob_flush();
	}
	echo '<span style="color:#090"> Done </span><br/>'."\n";
	return $spotifySongs;
}

// GET FIRST 100 SONGS FROM SPOTIFY
function getFirts100SongsFromSpotifyPlaylist($spotifyPlaylistEmbedCode){
	$urlPlaylistSpotify = getSpotifyPlaylistUrlFromEmbedCode($spotifyPlaylistEmbedCode);
	echo "Getting 100 first songs from Spotify Playlist: ".$urlPlaylistSpotify;
	flush(); ob_flush();
	$arrayWebPage = getWebPage($urlPlaylistSpotify);

	//print_r($arrayWebPage['content']);
	$html = str_get_html($arrayWebPage['content']);

	foreach($html->find('script[id=resource]') as $element){
	    $jsonResource[] = $element->innertext;
	}

	if(count($jsonResource) != 1) {
		echo '<span style="color:red"> Error: Could not get the songs from Spotify Playlist</span><br/>'."\n";
		exit;
	}

	$jsonResource = $jsonResource[0];
	$resource = json_decode($jsonResource,TRUE);
	//print_r($resource['tracks']);
	echo '<span style="color:#090"> Done</span><br/>'."\n";
	echo '<br>';
	return $resource;
}

// GET SONG NAME AND ARTIST FROM JSON ---------------------
function parceJsonSpotifyPlaylist($spotifyPlaylistEmbedCode){
	$resource = getFirts100SongsFromSpotifyPlaylist($spotifyPlaylistEmbedCode);
	echo "Parsing Spotify Playlist";
	flush(); ob_flush();
	$spotifySongs = array();
	$cont = 0;
	foreach ($resource['tracks'] as $value) {
		foreach ($value as $aux) {
			//print_r($aux);
			$spotifySongs[$cont]['artistName'] = $aux['track']['artists'][0]['name'];
			$spotifySongs[$cont]['songName'] = $aux['track']['name'];
			$cont++;
		}
	}
	echo '<span style="color:#090"> Done</span><br/>'."\n";
	echo '<br>';
	return $spotifySongs;
}

// GET DOWNLOAD LINK FROM YOUTUBE ID ARRAYs---------------------
function getDownloadLinkFromYoutubeID($arrayYoutubeId){
	foreach ($arrayYoutubeId as $id) {
		echo '<iframe style="height:100px; border:0px;" src="https://y-api.org/button/?f=mp3&t=1&v='.$id.'"></iframe>';
		//echo '<br>';
	}
}

function getYoutubeIDFromJsonFila(){
	global $fileSongsJson;
	checkFileExist($fileSongsJson);
	$arrayID = array();
	$songsJson = json_decode(file_get_contents($fileSongsJson),true);
	if(empty($songsJson)){
		echo '<span style="color:red"> Error: You must get the songs first.</span><br/>'."\n";
		exit;
	}
	foreach ($songsJson as $value) {
		if(empty($value['video_ids'])) continue;
		if(!empty($value['video_ids'][0])) $arrayID[] = $value['video_ids'][0];
	}
	return $arrayID;
}

// --------------------------------––-----------------------------------------
// --------------------------------- INICIO ----------------------------------
// --------------------------------––-----------------------------------------

if(empty($_GET)){
	echo '<span style="color:red"> Error: Some parameters needed!</span><br/>'."\n";
	exit;
}

if($_GET['action'] == 'getjson'){
	$arraySpotifySongs = getYoutubeLink(parceJsonSpotifyPlaylist($spotifyPlaylistEmbedCode));
	if(!empty($_GET['verbose'])) {
		echo '<br>';
		echo '<br>';
		print_r($arraySpotifySongs);
	}
	echo '<br>';
	echo '<br>';
	echo '<a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'?action=getlink">Download MP3</a>';
}

if($_GET['action'] == 'getlink'){
	$ids = getYoutubeIDFromJsonFila();
	getDownloadLinkFromYoutubeID($ids);
}

echo '<br>';
echo '<br>';
echo '<span style="color:#090"> FINISHED </span><br/>'."\n";
