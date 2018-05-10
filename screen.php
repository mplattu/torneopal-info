<?php

  include_once('settings.php');
  include_once('TorneopalInfo.php');

  $api = new TorneopalInfo();
  $api->setAPIkey($MY_API_KEY);
  $api->setAPIURL($MY_API_URL);

  $function = $_GET['f'];

  $html = null;
  $data = null;

  if ($function == 'getMatches') {
    $limit = allowed_chars_number($_GET['limit']);
    $current_season = date('Y');
    $start_date = date('Y-m-d');
    $match_data = $api->getMatches(Array('season_id'=>$current_season, 'start_date'=>$start_date, 'venue_id'=>$API_PARAMETERS['venues'], 'joined_venues'=>0));

    $combined_data = get_match_data($match_data, $limit);
    $html = get_match_html($combined_data);
  }

  if (!is_null($html)) {
    print $html;
  }
  elseif (!is_null($data)) {
    print_r($data);
  }

  exit(0);



  function get_match_data($data, $limit) {
    global $api;

    $match_count = 0;

    $result = Array();

    foreach ($data as $this_match) {
      $match_count++;
      if ($match_count > $limit) {
        // We have reached the $limit
        break;
      }

      $this_result = Array(
        'team_A_name' => $this_match['team_A_name'],
        'team_B_name' => $this_match['team_B_name'],
        'category_name' => $this_match['category_name'],
        'round_name' => $this_match['round_name'],
        'venue_name' => $this_match['venue_name'],
        'date' => $this_match['date'],
        'time' => $this_match['time'],
        'time_end' => $this_match['time_end']
      );

      array_push($result, $this_result);
    }

    return $result;
  }

  function get_match_html ($data) {
    $html = '<table>';

    foreach ($data as $this_match) {
      $row = '<tr>';
      $row .= '<td><span class="date">'.$this_match['date'].'</span> <span class="time">'.$this_match['time'].'-'.$this_match['time_end'].'</span></td>';
      $row .= '<td><span class="category">'.$this_match['category_name'].'</span> <span class="round">'.$this_match['round_name'].'</span></td>';
      $row .= '<td><span class="venue">'.$this_match['venue_name'].'</span></td>';
      $row .= '</tr>';
      $html .= $row;

      $row = '<tr>';
      $row .= '<td><span class="team_home">'.$this_match['team_A_name'].'</span></td>';
      $row .= '<td><span class="team_visitor">'.$this_match['team_B_name'].'</span></td>';
      $row .= '</tr>';
      $html .= $row;
    }

    $html .= "</table>";

    return $html;
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

  function allowed_chars_number ($str) {
    $str = preg_replace('/[^0-9]/', '', $str);
    $str = substr($str, 0, 5);
    return $str;
  }

?>
