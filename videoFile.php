<?php
class videoFile
{

  private $series_name;

  private $season;

  private $episode;

  private $file_location;

  private $filename;

  private $series_slug;

  private $is_series = false;

  private $file_extension;

  private $video_quality;

  private $file_name_only;

  public function __construct ($filepath){

    $pathinfo = pathinfo($filepath);
    $this->file_location = $pathinfo['dirname'] . "/";
    $this->file_name_only = $pathinfo['filename'];
    $this->file_extension = $pathinfo['extension'];
    $this->filename = $this->file_name_only . "." . $this->file_extension;
    //replace all unscannable characters
    $fixed_filename = $this->filename;

    $replace = array('[', ']', ")", "(");
    $fixed_filename= str_replace($replace, '',$fixed_filename);

    $replace = array('_', ' ', '-');
    $fixed_filename= str_replace($replace, '.',$fixed_filename);

    $pregs = array(
      '/\.S([0-9]{1,2})E([0-9]{1,2})\./i' =>'/(.*?)\.S[0-9]{1,2}E[0-9]{1,2}/i',
      '/\.([0-1]?[0-9]){1}([0-9]{2})\./' => '/(.*?)\.[0-9]{1}[0-9]{2}\./',
      '/\.([0-9]){1}x([0-9]{2})\./i' => '/(.*?)\.[0-9]{1}x[0-9]{2}\./i',
      '/\.Season\.([0-9]){1,2}\.([0-9]{1,2})/i' => '/(.*?)\.Season\.([0-9]){1,2}\.([0-9]{1,2})/i',
      '/\.S([0-9]{1,2})\.([0-9]{1,2})\./i' =>'/(.*?)\.S[0-9]{1,2}\.[0-9]{1,2}/i',
      '/\.Season\.([0-9]){1,2}\.Episode\.([0-9]{1,2})/i' => '/(.*?)\.Season\.([0-9]){1,2}\.Episode\.([0-9]{1,2})/i',

      '/\.Season\.([0-9]){1,2}\.+([0-9]{1,2})/i' => '/(.*?)\.Season\.([0-9]){1,2}\.+([0-9]{1,2})/i');

    foreach ($pregs as $search => $get){
      $matches = array();
      if ( 1 === preg_match($search, $fixed_filename, $matches)){
        $this->is_series = true;
        $this->season = sprintf('%02d', $matches[1]);
        $this->episode = sprintf('%02d', $matches[2]);

        $series_matches = array();
        $result = preg_match($get, $fixed_filename, $series_matches);
        $this->series_slug  = $series_matches[1];
        $this->series_name = trim(ucwords(strtolower(implode(" ", explode('.', $this->series_slug)))));

        $getID3 = new getID3;
        $ThisFileInfo = $getID3->analyze($filepath);
        $this->video_quality =  $ThisFileInfo['video']['resolution_y'];

        //its a movie if its longer than 70 minutes
        if ((int) $ThisFileInfo["playtime_seconds"] > 4200){
          $this->is_series = false;
        }
        break;
      }
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
