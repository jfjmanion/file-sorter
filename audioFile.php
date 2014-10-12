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

  function __construct ($filepath){
    $pathinfo = pathinfo($filepath);
    $this->file_location = $pathinfo['dirname'] . "/";
    $this->filename = $pathinfo['filename'] . "." . $pathinfo['extension'];
    $this->file_extension = $pathinfo['extension'];

    $getID3 = new getID3;
    $ThisFileInfo = $getID3->analyze($filepath);
    getid3_lib::CopyTagsToComments($ThisFileInfo);

    $audio_pathinfo = pathinfo($audio);
    $extension = $audio_pathinfo['extension'];

    $this->artist = $ThisFileInfo['comments']['artist'][0];
    $this->album_year = $ThisFileInfo['comments']['year'][0];
    $this->album = $ThisFileInfo['comments']['album'][0];
    $this->title = $ThisFileInfo['comments']['title'][0];
    unset($ThisFileInfo);

  }

  function getArtist(){
    return $this->artist;
  }

  function getAlbumYear(){
    return $this->album_year;
  }

  function getAlbum(){
    return $this->album;
  }

  function getExtension(){
    return $this->file_extension;
  }

  function getFileLocation(){
    return $this->file_location;
  }
  function getFileName(){
    return $this->filename;
  }

  function getTitle(){
    return $this->title;
  }










}
