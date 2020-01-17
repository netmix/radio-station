<?php
/*
 * Import/Export Show data from/to YAML file
 * Author: Andrew DePaula
 * @Since: 2.3.1 (unsure about this... assumed, since that's the release this feature is tagged to)
 * (c) Copyright 2020
 * Licence: GPL3
 */

 require_once __DIR__.'/../vendor/autoload.php';
 use Symfony\Component\Yaml\Yaml;
 use Symfony\Component\Yaml\Exception\ParseException;

 //this function handles processing data from a YAML file. Assumes it is already parsed and
 //expects an array of shows passed as $yaml_object, in the following format:
 /*
       array (
         [0] => array (
           'show-title' => 'Your Story Hour',
           'show-description' => 'Dramatized half-hour stories taken from sacred and secular history...',
           'show-image' => 'https://lifetalk.net/wp-content/uploads/2019/12/your-story-hour.jpg',

           'show-byline' => 'with Aunt Carole and Uncle Dan',
           'show-day' => 'monday',
           'show-times' => array(
              [0] => array(
                [0] => "05:30",
                [1] => "06:00"
              ),
              [1] => array(
                [0] => "17:00",
                [1] => "17:30"
              )
           )
           'show-url' => 'https://lifetalk.net/programs/your-story-hour/',
           'show-podcast: ''
         ),
         [1] => array (
           'show-title' => 'the next show...',
         ),
       )

     // Here's what the YAML file for the above might look like

     #the following is part of the custom post type itself
     show-title: "Your Story Hour"
     show-description: >-
       Dramatized half-hour stories taken from sacred and secular history and true-life
       situations, build character and equip today’s youth for life’s challenges and
       good decision-making. Your Story Hour provides wholesome character-building
       entertainment for the whole family.
     show-image: "https://lifetalk.net/wp-content/uploads/2019/12/your-story-hour.jpg"

     #the following is metadata
     show-byline: "with Aunt Carole and Uncle Dan"
     #show-day and show-times are stored in the DB as a single metadata field (show_sched)
     show-day: mon #one of [sun, mon, tue, wed, thu, fri, sat]. Spelling out days ("Monday" or "monday") is also supported
     show-times:
       - ["05:30", "06:00"] #all times in 24h format
       - ["17:00", "17:30"]
     show-url: "https://lifetalk.net/programs/your-story-hour/"
     show-podcast: ~
  */

// this function handles processing data from a YAML file and writing it to the DB
function yaml_import_ok($file_name = ''){
  global $yaml_import_message;
  global $yaml_parse_errors;

  try {
    $value = Yaml::parseFile($file_name);
    error_log(print_r($value, true)."\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)
  } catch (ParseException $exception) {
    $yaml_parse_errors = $exception->getMessage();
    $yaml_import_message = __('YAML import error. See below for details.', 'radio-station');
    return false;
  }

  //FIXME YAML data validation code goes here
  //FIXME database update code goes here

  return true;
}


 //this function validates the datastructure for a show and display's any error messages
 function show_is_valid(&$show = null){
   $errors = '';
   //validate title (make sure it's a string)
   $show['show-title'] = trim(htmlspecialchars($show['show-title']));
   //validate description (FIXME allow limited HTML markup)
   $show['show-description'] = trim(htmlspecialchars($show['show-description']));
   //validate image (make sure it's a URL)
   $tmp_var = filter_var($show['show-image'], FILTER_VALIDATE_URL);
   if ($tmp_var){
     $show['show-image'] = $tmp_var;
   }else{
     $errors .= '<li>' . __('Show image must be a URL.','radio-station') . '</li>';
   }
   //validate byline (FIXME allow limited HTML markup)
   $show['show-byline'] = trim(htmlspecialchars($show['show-byline']));
   //validate show-day (make sure it's one of [sun..sat] or [sunday..saturday] or [Sunday..Saturday])
   $days = array("sun", "mon", "tue", "wed", "thu", "fri", "sat", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday");
   $show['show-day'] = strtolower($show['show-day']);
   if (!in_array($show['show-day'], $days)){
     $errors .= '<li>' . __('Show day must be one of the days of the week expressed as ["sun".."sat"], ["Sunday".."Saturday"], or ["sunday", "saturday"].','radio-station') . '</li>';
   }
   //validate show-times
             /*
             'show-times' => array(
                [0] => array(
                  [0] => "05:30",
                  [1] => "06:00"
                ),
                [1] => array(
                  [0] => "17:00",
                  [1] => "17:30"
                )
             )
             */
   //validate show-url (make sure it's a URL)
   //validate show-podcast (make sure it's a URL)

   if ($errors === ''){
     return true;
   }else {
     $errors = '<h2>'.$shows['show-title'].'</h2><ul>' . $errors . '</ul>';
     global $yaml_import_message;
     $errors = __('YAML data parsed successfully, but contains formatting errors as follows:', 'radio-station') . $errors;
 		 $yaml_import_message = $errors;
 		 add_action('admin_notices', 'yaml_import__failure');
     return false;
   }
 }//function show_is_valid()
