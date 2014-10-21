<?php
class audioFile
{

  private $title;

  private $album;

  private $album_year;

  private $artist;

  private $file_location;

  private $filename;

  private $file_extension;

  private $tracknumber;

  public function __construct ($filepath){
    $pathinfo = pathinfo($filepath);
    $this->file_location = $pathinfo['dirname'] . "/";
    $this->filename = $pathinfo['filename'] . "." . $pathinfo['extension'];
    $this->file_extension = $pathinfo['extension'];

    $getID3 = new getID3;
    $ThisFileInfo = $getID3->analyze($filepath);
    getid3_lib::CopyTagsToComments($ThisFileInfo);

    $audio_pathinfo = pathinfo($audio);
    $extension = $audio_pathinfo['extension'];

    $tracknumber = $ThisFileInfo['comments']['tracknumber'][0];
    $this->tracknumber = (!empty($tracknumber)) ? $tracknumber : $ThisFileInfo['comments']['track'][0];
    $this->tracknumber = sprintf('%02d', $this->tracknumber);

    $this->artist = ucwords(strtolower($ThisFileInfo['comments']['artist'][0]);
    $this->album_year = $ThisFileInfo['comments']['year'][0];
    $this->album = ucwords(strtolower($ThisFileInfo['comments']['album'][0]);
    $this->title = ucwords(strtolower($ThisFileInfo['comments']['title'][0]);

    unset($ThisFileInfo);

  }

  public function getArtist(){
    return $this->artist;
  }

  public function getAlbumYear(){
    return $this->album_year;
  }

  public function getAlbum(){
    return $this->album;
  }

  public function getExtension(){
    return $this->file_extension;
  }

  public function getFileLocation(){
    return $this->file_location;
  }
  public function getFileName(){
    return $this->filename;
  }

  public function getTitle(){
    return $this->title;
  }

  public function getTrackNumber(){
    return $this->tracknumber;
  }
}
