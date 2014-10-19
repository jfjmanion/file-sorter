<?php
//require_once('./vendor/autoload.php');
require_once('./vendor/JamesHeinrich/getID3/getid3/getid3.php');
require_once('./config.php');
require_once('./videoFile.php');
require_once('./audioFile.php');

$videos = array();
$audios = array();
$others = array();
$directories = array();
$lock_flag = false;

//get all of the files in the Completed folder and generate classes
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($scanning_dir), RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $name => $object){
    $pathinfo = pathinfo($name);
    if (in_array($pathinfo['extension'], $video_match) && strpos($pathinfo['filename'], 'sample') === false ){
      $videos[] = new videoFile($name);
    } else if (in_array($pathinfo['extension'], $audio_match)) {
      $audios[] = new audioFile($name);
    } else if (!is_dir($name)){
      $others[] = $name;
    } else {
      $directories[] = $name;
    }
}

foreach ($videos as $video){
  //make the directory
  if ($video->is_series()) {
    $directory = $series_dir . $video->get_series() . '/Season ' . $video->get_season() . '/';

    $video_location = $directory . "Episode " . $video->get_episode() . " (" .$video->get_video_quality() . "p).".$video->get_file_extension();

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

  if ($prod && !file_exists($lock_file)){
    //if it already exists, just delete it
    if (!file_exists($video_location)){
      @mkdir($directory, $permissions, true);
      copy($video->get_file_location().$video->get_filename(), $video_location );
      unlink($video->get_file_location().$video->get_filename());
    } else {
      unlink($video->get_file_location().$video->get_filename());
    }
  } else{
    $lock_flag = true;
  }
}

foreach ($audios as $audio){

  $artist = $audio->getArtist();
  $artist = (!empty($artist)) ? $artist . "/" : "";
  $year = $audio->getAlbumYear();
  $year = (!empty($year)) ? " (" . $year . ")" : "";
  $album = $audio->getAlbum();
  $album = (!empty($album)) ? $album . $year . "/" : "";
  $tracknumber = $audio->getTrackNumber();
  $tracknumber = (!empty($tracknumber)) ? $tracknumber . " - " : "";

  $directory = $music_dir . $artist . $album;
  $filename = $tracknumber . $audio->getTitle() . "." . $audio->getExtension();
  $new_location = $directory . $filename;

  if ($prod && !file_exists($lock_file)){
    if (!file_exists($new_location)){
      @mkdir($directory, $permissions, true);
      copy($audio->getFileLocation().$audio->getFileName(), $new_location);
      unlink($audio->getFileLocation() . $audio->getFileName());
    } else {
      unlink($audio->getFileLocation() . $audio->getFileName());
    }
  } else {
    $lock_flag = true;
  }

  if ($debug){
  echo "old file location: " . $audio->getFileLocation(). $audio->getFileName() . "<br>";
  echo "New file location: " . $new_location . "<br><br>";
  }
}

foreach ($others as $other){
  //move them into an new "Other to sort" folder
  $pathinfo = pathinfo($other);

  if ($debug){
    echo "Copy Other File: " . $other . "<br>";
    echo "To : " . $manual_sort . $pathinfo['filename'] . "." . $pathinfo['extension'] . "<br><br>";
  }

  if ($prod && !file_exists($lock_file)){
    //if its a sample or the extension is ignore, just remove it
    if (!in_array($pathinfo['extension'], $ignore_files) && strpos($pathinfo['filename'], 'sample') === false){
      copy($other, $manual_sort . $pathinfo['filename'] . "." . $pathinfo['extension']);
    }
    unlink($other);
  } else {
    $lock_flag = true;
  }
}

$directories = array_reverse($directories);
foreach($directories as $directory){
  if ($debug) {
    echo "Directory to delete: " . $directory . "<br>";
  }

  if ($prod && $lock_flag === false){
    @rmdir($directory);
  }

}
