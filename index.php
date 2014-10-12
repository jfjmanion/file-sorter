<?php
//require_once('./vendor/autoload.php');
require_once('./vendor/JamesHeinrich/getID3/getid3/getid3.php');
require_once('./config.php');
require_once('./videoFile.php');
require_once('./audioFile.php');

$videos = array();
$audios = array();
$others = array();

//get all of the files in the Completed folder and generate classes
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($scanning_dir), RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $name => $object){
    $pathinfo = pathinfo($name);
    if (in_array($pathinfo['extension'], $video_match) && strpos($pathinfo['filename'], 'sample') === false ){
      $videos[] = new videoFile($name);
    } else if (in_array($pathinfo['extension'], $audio_match)) {
      $audios[] = new audioFile($name);
    } else {
      $others[] = $name;
    }
}

foreach ($videos as $video){
  //make the directory
  if ($video->is_series()) {
    $directory = $series_dir . $video->get_series() . '/Season ' . $video->get_season() . '/';

    $video_location = $directory . "Episode " . $video->get_episode().".".$video->get_file_extension();

    if ($debug){
      echo "Series: ". $video->get_series() . "<Br>";
      echo "Season: ".$video->get_season() . "<Br>";
      echo "Episode: ". $video->get_episode() . "<Br>";
      echo "Series Slug: ".$video->get_series_slug() . "<Br>";
      echo "Old File Location: ".$video->get_file_location() . $video->get_filename() . "<Br>";
      echo "New File Location: ".$video_location . "<Br><br>";
    }
  } else {
    //move it into the movies
    $directory = $movies_dir;
    $video_location = $directory . $video->get_filename();
  }

  if (!file_exists($video_location) && $prod){
    //make the directory
    @mkdir($directory, $permissions, true);
    copy($video->get_file_location().$video->get_filename(), $video_location );
  }
}

foreach ($audios as $audio){

  $artist = $audio->getArtist();
  $artist = (!empty($artist)) ? $artist . "/" : "";
  $year = $audio->getAlbumYear();
  $year = (!empty($year)) ? " (" . $year . ")" : "";
  $album = $audio->getAlbum();
  $album = (!empty($album)) ? $album . $year . "/" : "";

  $directory = $music_dir . $artist . $album;
  $filename = $audio->getTitle() . "." . $audio->getExtension();
  $new_location = $directory . $filename;

  if (!file_exists($new_location) && $prod){
    @mkdir($directory, $permissions, true);
    copy($audio->getFileLocation().$audio->getFileName(), $new_location);
  }
  if ($debug){
  echo "old file location: " . $audio->getFileLocation(). $audio->getFileName() . "<br>";
  echo "New file location: " . $new_location . "<br><br>";
  }
}
