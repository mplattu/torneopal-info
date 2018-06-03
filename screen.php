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
    $nopassed = allowed_chars_number($_GET['nopassed']);
    $current_season = date('Y');
    $start_date = date('Y-m-d');
    $match_data = $api->getMatches(Array('season_id'=>$current_season, 'start_date'=>$start_date, 'venue_id'=>$API_PARAMETERS['venues'], 'joined_venues'=>0));

    $combined_data = get_match_data($match_data, Array('limit'=>$limit, 'nopassed'=>$nopassed));
    $html = get_match_html($combined_data);
  }

  if (!is_null($html)) {
    print $HTML_HEADER.$html.$HTML_FOOTER;
  }
  elseif (!is_null($data)) {
    print_r($data);
  }

  exit(0);



  function get_match_data($data, $param) {
    global $api;

    // Limit value defaults to 5
    if (is_null($param['limit']) or $param['limit'] == "") {
      $param['limit'] = 5;
    }

    $match_count = 0;

    $result = Array();

    foreach ($data as $this_match) {
      $match_count++;
      if ($match_count > $param['limit']) {
        // We have reached the $limit
        break;
      }

      // Calculate unix end time to check "nopassed" parameter
      $end_unixtime = strtotime($this_match['date'].' '.$this_match['time_end']);
      if ($param['nopassed'] > 0 and $end_unixtime < time()) {
        // This game has already passed, skip
        break;
      }

      $this_result = Array(
        'team_A_name' => $this_match['team_A_name'],
        'team_B_name' => $this_match['team_B_name'],
        'team_A_iconurl' => $api->getTeamIcon($this_match['club_A_id']),
        'team_B_iconurl' => $api->getTeamIcon($this_match['club_B_id']),
        'fs_A' => $this_match['fs_A'],
        'fs_B' => $this_match['fs_B'],
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
    $html = '<table id="table_games">';

    foreach ($data as $this_match) {
      $row = '<tr class="row_details"><td colspan="5" class="cell_details">';
      $row .= '<span class="date">'.date_local($this_match['date']).'</span> <span class="time">'.time_local($this_match['time']).'-'.time_local($this_match['time_end']).'</span>';
      $row .= '<span class="category">'.$this_match['category_name'].'</span> <span class="round">'.$this_match['round_name'].'</span>';
      $row .= '<span class="venue">'.$this_match['venue_name'].'</span>';
      $row .= '</td></tr>';
      $html .= $row;

      $score_home = '';
      $score_visitor = '';
      if ($this_match['fs_A'] != "") {
        $score_home = '<br/>'.$this_match['fs_A'];
      }
      if ($this_match['fs_B'] != "") {
        $score_visitor = '<br/>'.$this_match['fs_B'];
      }

      $row = '<tr><td class="cell_teams"><span class="icon_home"><img src="'.$this_match['team_A_iconurl'].'" class="icon_home_img"></span></td>';
      $row .= '<td class="cell_team_home"><span class="team_home">'.$this_match['team_A_name'].$score_home.'</span></td>';
      $row .= '<td class="cell_team_middle">-</td>';
      $row .= '<td class="cell_team_visitor"><span class="team_visitor">'.$this_match['team_B_name'].$score_visitor.'</span></td>';
      $row .= '<td><span class="icon_visitor"><img src="'.$this_match['team_B_iconurl'].'" class="icon_visitor_img"></span>';
      $row .= '</td></tr>';
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

  function date_local ($date_sql) {
    global $LOCAL_DATE;

    $unix_timestamp = strtotime($date_sql);
    return date($LOCAL_DATE, $unix_timestamp);
  }

  function time_local ($time_sql) {
    global $LOCAL_TIME;

    $unix_timestamp = strtotime($time_sql);
    return date($LOCAL_TIME, $unix_timestamp);
  }
?>
