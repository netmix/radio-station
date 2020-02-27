# Radio Station Plugin Data

***


## Custom Post Types

### Shows
The main Post Type for Radio Station with assignable Show Shifts.

### Schedule Overrides
Similar to Shows but a date and time block instead of Shifts.

### Playlists
A Track list assignable to a Show.

### [Pro] Episodes
A future release of Radio Statio Pro will include Show Episodes.


## Taxonomies

### Genre Taxonomy
A flexible taxonomy allowing for the addition of Genre terms that can be assigned to a Show (or Override.)

### Language Taxonomy
A fixed taxonomy allowing for assigning of Language terms to a Show (or Override.)


## Data Endpoints

Data Endpoints for Schedule and Show Data are available via REST Routes (if option enabled.)
Alternatively they are also available via Feed Links (again if that option is enabled.)

All endpoints add the following top level station data also: 

* stream_url: Streaming URL
* station_url: Station Home URL
* schedule_url: Schedule Page URL
* language: Main Station Language Code
* timestamp: UTC Timestamp
* date_time: Date Time Stamp (YY-mm-dd H:i:s)

### Endpoint List

Note the Route slugs are filterable via the filter `radio_station_...`
Feed slugs are filterable via the filter `radio_station_...`

**/wp-json/radio/** / **/feed/radio/**
This is the base discovery route for all the other routes.

**/wp-json/radio/station/** / **/feed/station/**
station: 

**/wp-json/radio/broadcast/** / **/feed/broadcast/**
current_show: Currently playing Show 
next_show: Next playing Show.

**/wp-json/radio/schedule/** / **/feed/schedule/**
schedule: Data list of all Scheduled Shows by Day (ordered by time)

**/wp-json/radio/shows/** / **/feed/shows/**
shows: Data list of all Shows by Name

**/wp-json/radio/genres/** / **/feed/genres/**
genres: Data lists of all Genres with all the Shows for each Genre.

**/wp-json/radio/languages/** / **/feed/languages/**
genres: Data lists of all Languages with all the Shows for each Language.



