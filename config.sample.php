<?php

$series_dir = './series/';

$movies_dir = './movies/';

$music_dir = './music/';

$manual_sort = './manual_sort/';

$scanning_dir = './files_to_move/';

$video_match = array('wmv', 'mkv', 'avi', 'mp4','mpg','m4v','rmvb');

$audio_match = array('m4a', 'mp3', 'flac', 'wma');

$permissions = 0777;

$debug = true;

$prod = true;

$lock_file = $scanning_dir . "lock";

$ignore_files = array('txt', 'nfo', 'jpg', 'DS_Store', 'm3u', 'log');
