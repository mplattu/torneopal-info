# torneopal-info

These PHP scripts create desired views of Tourneopal API (http://spl.torneopal.fi/taso/rest/help). The main
interest is to create pages to be shown with Yodeck info screen system.

To access the API you need to get a key and set it at `settings.php` (see `settings.php.sample`).

## Get Your Field IDs

First you need to find out the ids of your field(s). Start by setting `$ALLOW_DEBUG` as `true` in
`settings.php`.

 1. Find your district by getting `debug.php?f=getDistricts`.
    Note the `district` of your district (a string, e.g. `splhelsinki`).
 1. Find one club which plays in your venue by getting `debug.php?f=getClubs&district=splhelsinki`.
    Note the `club_id` (an integer, e.g. `5`).
 1. Get the venue ID by searching with your `district` and `club_id`: `debug.php?f=getVenues&district=splhelsinki&club_id=5`.
    Note the `venue_id` of your field(s) (an integer, e.g. `355`, `2673`, `2674`).
 1. Try to make a search of matches with your `venue_id`: `debug.php?f=getMatches&venue_id=355`. You can
    combine several venue IDs with commas (`355,2673,2674`).
    If your venue IDs appear to be OK you can enter the IDs to `settings.php`, `$API_PARAMETERS->venues`.

Don't forget to set `$ALLOW_DEBUG` as `false` in `settings.php` to avoid unwanted use of API with your key.

## Info Screen View

Get `screen.php`.
