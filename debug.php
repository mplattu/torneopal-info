<?php

  include_once('settings.php');
  include_once('TorneopalInfo.php');

  if (!$ALLOW_DEBUG) {
    print('You need to set $ALLOW_DEBUG to TRUE in settings.php');
    exit(0);
  }
  
  $api = new TorneopalInfo();
  $api->setAPIkey($MY_API_KEY);
  $api->setAPIURL($MY_API_URL);

  $function = $_GET['f'];

  $html = null;
  $data = null;

  if ($function == 'getDistricts') {
    $data = $api->getDistricts();
    $html = data_dump($data);
  }
  elseif ($function == 'getClubs') {
    $data = $api->getClubs(Array('district'=>$_GET['district']));
    $html = data_dump($data);
  }
  elseif ($function == 'getVenues') {
    $data = $api->getVenues(Array('district'=>$_GET['district'], 'club_id'=>$_GET['club_id']));
    $html = data_dump($data);
  }
  elseif ($function == 'getMatches') {
    $current_season = date('Y');
    $start_date = date('Y-m-d');
    $data = $api->getMatches(Array('season_id'=>$current_season, 'start_date'=>$start_date, 'venue_id'=>$_GET['venue_id'], 'joined_venues'=>0));
    $html = data_dump($data);
  }

  if (!is_null($html)) {
    print $html;
  }
  elseif (!is_null($data)) {
    print_r($data);
  }


  // Creates a HTML-formatted dump from a data structure (effectively print_r())
  function data_dump ($data) {
    $html = "<pre>\n".print_r($data, true)."\n</pre>\n";
    return $html;
  }

  // Write log message to httpd error log
  function log_message ($message) {
    error_log($message, 4);
  }
?>
