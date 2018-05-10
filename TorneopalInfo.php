<?php

class TorneopalInfo {
  private $ICONURL = "https://torneopal-sentinelsoftware.netdna-ssl.com/logo/palloliitto/%dx.png";
  private $APIKEY = null;
  private $APIURL = null;

  private $club_url_cache = Array();

  public function setAPIkey ($new_key) {
    $this->APIKEY = $new_key;
  }

  public function setAPIURL ($new_url) {
    $this->APIURL = $new_url;
  }

  private function jsonDecode ($json_string) {
    // Decode JSON string
    return json_decode($json_string, true);
  }

  private function callAPI ($command, $parameters=Array()) {
    // Add current API key to parameters
    $parameters{'api_key'} = $this->APIKEY;

    $url = $this->APIURL.'/'.$command.'?'.http_build_query($parameters);

    log_message('call_api: '.$url);

    $result = file_get_contents($url);

    if (!is_null($result) and $result != "") {
      return $this->jsonDecode($result);
    }

    return null;
  }

  public function getDistricts () {
    $data = $this->callAPI('getDistricts');
    return $data['districts'];
  }

  public function getClubs ($params) {
    $data = $this->callAPI('getClubs',$params);
    return $data['clubs'];
  }

  public function getVenues ($params) {
    $data = $this->callAPI('getVenues',$params);
    return $data['venues'];
  }

  public function getMatches ($params) {
    $data = $this->callAPI('getMatches',$params);
    return $data['matches'];
  }

  public function getTeamIcon ($team_id) {
    return sprintf($this->ICONURL, $team_id);
  }
}

?>
