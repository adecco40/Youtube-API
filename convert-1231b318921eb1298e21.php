<?php

$_GET['query'];

$query = $_GET['query'];

header("Content-Type: application/json");

header('Access-Control-Allow-Origin: *');

function getYouTubeIdFromURL($url)
{
    $url_string = parse_url($url, PHP_URL_QUERY);
    parse_str($url_string, $args);
    return isset($args['v']) ? $args['v'] : false;
}

$youtube_id = getYouTubeIdFromURL($query);

$ytapi = "https://api.chisdealhd.co.uk/v1/youtubeapi/video/".$youtube_id;

$contentytapi = file_get_contents($ytapi);

$jsonytapi = json_decode($contentytapi, true);

$jsontitle = preg_replace('/\s+/', '_', $jsonytapi['items'][0]['snippet']['title']);

$exists = file_exists($jsontitle . ".mp3");
if (!$exists) {

    $warframe = "https://apps.chisdealhd.co.uk/apps/ytdl/convert.php?youtubelink=" . $query;

    $content = file_get_contents($warframe);

    $json = json_decode($content, true);

    $ar = [];

    if ($json['error'] == true) {
        $ar["status"] = (string) "ERROR";
        $ar["msg"] = (string) "ERROR: Cant Download Song, might be Protected or Try Other Link?";
    } else {
        // Initialize a file URL to the variable
        $url = $json['file'];

        // Use basename() function to return the base name of file
        $file_name = basename($url);

        // Use file_get_contents() function to get the file
        // from url and use file_put_contents() function to
        // save the file by using base name
        if (file_put_contents($file_name, file_get_contents($url))) {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, 'https://apps.chisdealhd.co.uk/apps/ytdl/convert.php?delete=' . $json['youtube_id']);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            $buffer = curl_exec($curl_handle);
            curl_close($curl_handle);
	   
           $titlefile = $json['title'];
          
           copy($file_name,$titlefile.".mp3");
	
	   if(copy($file_name,$titlefile.".mp3")) {

            header("Location: " . $titlefile.".mp3");
	    unlink($file_name);
           } else {
           
	    header("Location: " . $file_name);
            copy($file_name,$titlefile.".mp3");
           } 
            //echo "File downloaded successfully";
        } else {
            echo "File downloading failed.";
        }
    }
} else {
    header("Location: " . $jsontitle.".mp3");
     unlink($file_name);
}
?>

