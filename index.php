<?php
//require_once('./vendor/autoload.php');
require_once('./vendor/JamesHeinrich/getID3/getid3/getid3.php');
require_once('./config.php');
require_once('./videoSeries.php');

$videos = array();
$audios = array();
$others = array();

//get all of the files in the Completed folder and generate classes
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($scanning_dir), RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $name => $object){
    $pathinfo = pathinfo($name);
    if (in_array($pathinfo['extension'], $video_match) && strpos($pathinfo['filename'], 'sample') === false ){
      $videos[] = new videoSeries($name);
    } else if (in_array($pathinfo['extension'], $audio_match)) {
      $audios[] = $name;
    } else {
      $others[] = $name;
    }
}

foreach ($videos as $video){
  //make the directory
  if ($video->is_series()) {
    $directory = $series_dir . '/' . $video->get_series() . '/Season ' . $video->get_season() . '/';

    //make the directory
    @mkdir($directory, $permissions, true);

    $video_location = $directory . "Episode " . $video->get_episode().".".$video->get_file_extension();

    if ($debug){
      echo "Series: ". $video->get_series() . "<Br>";
      echo "Season: ".$video->get_season() . "<Br>";
      echo "Episode: ". $video->get_episode() . "<Br>";

      echo "Filename: ".$video->get_filename() . "<Br>";
      echo "File Location: ".$video->get_file_location() . "<Br>";
      echo "Series Slug: ".$video->get_series_slug() . "<Br>";
    }
  } else {
    //move it into the movies
    $directory = $movies_dir;
    $video_location = $directory . $video->get_filename();
  }
  /* @TODO remove comment
  if (!file_exists($video_location)){
    copy($video->get_file_location().$video->get_filename(), $video_location );
  }*/
}

foreach ($audios as $audio){
  $getID3 = new getID3;
  $ThisFileInfo = $getID3->analyze($audio);
  getid3_lib::CopyTagsToComments($ThisFileInfo);

  $audio_pathinfo = pathinfo($audio);
  $extension = $audio_pathinfo['extension'];

  $artist = $ThisFileInfo['comments']['artist'][0];
  $year = $ThisFileInfo['comments']['year'][0];
  $album = $ThisFileInfo['comments']['album'][0];
  $title = $ThisFileInfo['comments']['title'][0];


  $artist = (!empty($artist)) ? $artist . "/" : "";
  $year = (!empty($year)) ? " (" . $year . ")" : "";
  $album = (!empty($album)) ? $album . $year . "/" : "";

  $directory = $music_dir . $artist . $album;
  $filename = $title . "." . $extension;
  $new_location = $directory . $filename;

  echo "file location: " . $audio . "<br>";
  echo "New file location: " . $new_location . "<br><br>";

}
