<?php
class videoFile
{

  private $series_name;

  private $season;

  private $episode;

  private $file_location;

  private $filename;

  private $series_slug;

  private $is_series = true;

  private $file_extension;

  private $video_quality;

  function __construct ($filepath){

    $pathinfo = pathinfo($filepath);
    $this->file_location = $pathinfo['dirname'] . "/";
    //in case the file has spaces instead of periods
    $this->filename = str_replace(" ", ".", $pathinfo['filename'] . "." . $pathinfo['extension']);
    $this->file_extension = $pathinfo['extension'];

    $result = preg_match('.S[0-9]{1,2}E([0-9]{1,2}).', $this->filename, $matches);
    if ($result === 1) {
        $temp = explode('E', $matches[0]);

        $this->episode = ltrim($temp[1], '0');
        $this->season = ltrim(ltrim($temp[0], 'S'), '0');

        $matches = array();
        $result = preg_match('/(.*?).S[0-9]{1,2}E[0-9]{1,2}/i', $this->filename, $matches);

        $this->series_slug  = $matches[1];
        $this->series_name = implode(" ", explode('.', $this->series_slug));
    } else {
      $this->is_series = false;
    }

  $getID3 = new getID3;
  $ThisFileInfo = $getID3->analyze($filepath);
  $this->video_quality =  $ThisFileInfo['video']['resolution_y'];
}

  function get_video_quality(){
    return $this->video_quality;
  }
  function get_filename(){
    return $this->filename;
  }

  function get_file_location(){
    return $this->file_location;
  }

  function get_series(){
    return $this->series_name;
  }
  function get_episode(){
    return $this->episode;
  }

  function get_season(){
    return $this->season;
  }
  function get_series_slug(){
    return $this->series_slug;
  }

  function get_file_extension(){
    return $this->file_extension;
  }

  function is_series(){
    return $this->is_series;
  }

}
