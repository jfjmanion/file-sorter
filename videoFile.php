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

  private $file_name_only;

  public function __construct ($filepath){

    $pathinfo = pathinfo($filepath);
    $this->file_location = $pathinfo['dirname'] . "/";
    $this->file_name_only = $pathinfo['filename'];
    $this->filename = $pathinfo['filename'] . "." . $pathinfo['extension'];
    //in case the file has spaces instead of periods
    $fixed_filename = str_replace(" ", ".", $pathinfo['filename'] . "." . $pathinfo['extension']);
    $this->file_extension = $pathinfo['extension'];

    $result = preg_match('.S[0-9]{1,2}E([0-9]{1,2}).', $fixed_filename, $matches);
    if ($result === 1) {
        $temp = explode('E', $matches[0]);

        $this->episode = sprintf('%02d', $temp[1]);
        $this->season = sprintf('%02d', ltrim($temp[0], 'S'));

        $matches = array();
        $result = preg_match('/(.*?).S[0-9]{1,2}E[0-9]{1,2}/i', $this->filename, $matches);

        $this->series_slug  = $matches[1];
        $this->series_name = implode(" ", explode('.', $this->series_slug));

        $getID3 = new getID3;
        $ThisFileInfo = $getID3->analyze($filepath);
        $this->video_quality =  $ThisFileInfo['video']['resolution_y'];
    } else {
      $this->is_series = false;
    }
  }

  public function get_video_quality(){
    return $this->video_quality;
  }
  public function get_filename(){
    return $this->filename;
  }

  public function get_file_location(){
    return $this->file_location;
  }

  public function get_series(){
    return $this->series_name;
  }
  public function get_episode(){
    return $this->episode;
  }

  public function get_season(){
    return $this->season;
  }

  public function get_series_slug(){
    return $this->series_slug;
  }

  public function get_file_extension(){
    return $this->file_extension;
  }

  public function is_series(){
    return $this->is_series;
  }

  public function get_file_name_only(){
    return $this->file_name_only;
  }

}
