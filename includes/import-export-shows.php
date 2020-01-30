<?php
/*
 * Import/Export Show data from/to YAML file
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */

 require_once __DIR__.'/../vendor/autoload.php';
 use Symfony\Component\Yaml\Yaml;
 use Symfony\Component\Yaml\Exception\ParseException;


 define('WITH_PARAGRAPH_TAGS', true);
 define('NULL_OK', true);
 define('WEEKDAYS', array("sun", "mon", "tue", "wed", "thu", "fri", "sat", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"));


// this function handles processing data from a YAML file and writing it to the DB
function yaml_import_ok($file_name = ''){
  global $yaml_import_message;
  global $yaml_parse_errors;

  try {
    $shows = Yaml::parseFile($file_name);
    // error_log(print_r($shows, true)."\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)
  } catch (ParseException $exception) {
    $yaml_parse_errors = $exception->getMessage();
    $yaml_import_message = __('YAML import error. See below for details.', 'radio-station');
    return false;
  }

  //YAML data validation code follows
  $return_value = true;
  foreach ($shows as $show){
    $sanitized_show = array();
    if (show_is_valid($show, $sanitized_show)){
      //FIXME database update code goes here
      error_log(">".$sanitized_show['show-user-list']."<\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)
    } else {
      $return_value = false;
      //errors are accumulated in the global $yaml_import_message, for display to the user
    }
  }

  return $return_value;
}


 //this function validates the datastructure for a show and display's any error messages
 function show_is_valid($show, &$sanitized_show = array()){
   //validation proceeds field by field as noted below, until all have been checked. Errors are accumulated in
   //$errors. Successfully validating fields are copied into $sanitized_show as we go along
   //success is returned at the end if there are no errors. In case of failure, $sanitized_show
   //will still contain the fields that validated properly, but no bad data. This is intended to be failsafe.
   //The goal is to make it impossible for any bad data to be injected into the program/website via a
   //corrupt or maliciously crafted YAML file.
   $errors = '';

   //check for a minimum field set and return an error message if any required fields are missing.
   //if no fields are to be required, comment out the whole if statement and $show_keys variable assignment.
   $show_keys = array_keys($show);
   if (!(in_array('show-title', $show_keys)
       // && in_array('show-description', $show_keys))
       // && in_array('show-schedule', $show_keys)
       // && in_array('show-active', $show_keys)
      )){

     $errors .= '<li>' . __('Each show in the YAML file must define at minimum the following keys: '
                              .'show-title.'
                              // .'show-description, '
                              // .'show-schedule, '
                              // .'show-active.'
                            ,'radio-station') . '</li>';
   }

   //validate title (make sure it's a string)
   if (!is_null($show['show-title'])){
     $sanitized_show['show-title'] = keep_basic_html_only($show['show-title']);
   }else{
     $errors .= '<li>' . __('show-title: may not be null.','radio-station') . '</li>';
   }
   //validate description
   if (!is_null($show['show-description'])){
     $sanitized_show['show-description'] = keep_basic_html_only($show['show-description'],WITH_PARAGRAPH_TAGS);
   }
   //Uncomment if requiring show-description
   // else{
   //   $errors .= '<li>' . __('show-description: may not be null.','radio-station') . '</li>';
   // }

   //validate excerpt
   $sanitized_show['show-excerpt'] = keep_basic_html_only($show['show-excerpt'],WITH_PARAGRAPH_TAGS);

   //validate image (make sure it's a URL or an integer)
   $tmp_var = filter_var($show['show-image'], FILTER_VALIDATE_URL);
   if ($tmp_var){
     $sanitized_show['show-image'] = $tmp_var;
   }else{
     if (!is_null($show['show-podcast'])){ //allow null
       $errors .= '<li>' . __('show-image: must be a URL reference to an existing image.','radio-station') . '</li>';
     }
   }

   //validate show-avatar
   $tmp_var = filter_var($show['show-avatar'], FILTER_VALIDATE_URL);
   if ($tmp_var){
     $sanitized_show['show-avatar'] = $tmp_var;
   }else{
     if (!is_null($show['show-podcast'])){ //allow null
       $errors .= '<li>' . __('show-avatar: must be a URL reference to an existing image.','radio-station') . '</li>';
     }
   }

   //validate show-header
   $tmp_var = filter_var($show['show-header'], FILTER_VALIDATE_URL);
   if ($tmp_var){
     $sanitized_show['show-header'] = $tmp_var;
   }else{
     if (!is_null($show['show-header'])){ //allow null
       $errors .= '<li>' . __('show-header: must be a URL reference to an existing image.','radio-station') . '</li>';
     }
   }

   //validate tagline
   $sanitized_show['show-tagline'] = keep_basic_html_only($show['show-tagline']);

   //validate show-schedule
   if (schedule_is_valid($show['show-schedule'], $errors)){
     $sanitized_show['show-schedule'] = $show['show-schedule'];
   }

   //validate show-url (make sure it's a URL)
   $tmp_var = filter_var($show['show-url'], FILTER_VALIDATE_URL);
   if ($tmp_var){
     $sanitized_show['show-url'] = $tmp_var;
   }else{
     if (!is_null($show['show-url'])){ //allow null
       $errors .= '<li>' . __('show-url: must be a valid web address.', 'radio-station') . '</li>';
     }
   }

   //validate show-podcast (make sure it's a URL)
   $tmp_var = filter_var($show['show-podcast'], FILTER_VALIDATE_URL);
   if ($tmp_var){
     $sanitized_show['show-podcast'] = $tmp_var;
   }else{
     if (!is_null($show['show-podcast'])){ //allow null
       $errors .= '<li>' . __('show-podcast: must be a valid web address.', 'radio-station') . '</li>';
     }
   }

   //validate show-user-list
   $tmp_var = $show['show-user-list'];
   $sanitized_show['show-user-list'] = array();
   if (is_array($tmp_var) && !isAssoc($tmp_var)){
     foreach ($tmp_var as $email_address){
        $tmp_var2 = filter_var($email_address, FILTER_VALIDATE_EMAIL);
        if ($tmp_var2){ //push the email onto our output array if valid
          array_push($sanitized_show['show-user-list'], $tmp_var2);
        }
     }
   }else{
     //allow null values
     if (!is_null($show['show-user-list'])){
       $errors .= '<li>' . __('show-user-list: must be a simple array of valid email addresses.', 'radio-station') . '</li>';
     }
   }

   //validate show-producer-list
   $tmp_var = $show['show-producer-list'];
   $sanitized_show['show-producer-list'] = array();
   if (is_array($tmp_var) && !isAssoc($tmj_var)){
     foreach ($tmp_var as $email_address){
        $tmp_var2 = filter_var($email_address, FILTER_VALIDATE_EMAIL);
        if ($tmp_var2){ //push the email onto our output array if valid
          array_push($sanitized_show['show-producer-list'], $tmp_var2);
        }
     }
   }else{
     //allow null values
     if (!is_null($show['show-producer-list'])){
       $errors .= '<li>' . __('show-producer-list: must be a simple array of valid email addresses.', 'radio-station') . '</li>';
     }
   }

   //validate show-email
   $tmp_var = filter_var($show['show-email'], FILTER_VALIDATE_EMAIL);
   if ($tmp_var){
     $sanitized_show['show-email'] = $tmp_var;
   }else{
     //allow null values
     if (!is_null($show['show-podcast'])){
       $errors .= '<li>' . __('show-email: must be a valid email address.', 'radio-station') . '</li>';
     }
   }

   //validate show-active... true for "1", "true", "on", and "yes", false otherwise
   if (!is_null($show['show-active'])){
     $sanitized_show['show-active'] = filter_var($field, FILTER_VALIDATE_BOOLEAN);
   }else{
     $sanitized_show['show-active'] = false; #null defaults to inactive
     // $errors .= '<li>' . __('show-active: may not be null.','radio-station') . '</li>';
   }

   //validate show-patreon
   $sanitized_show['show-patreon']= sanitize_title($show['show-patreon']);

   if ($errors === ''){
     return true;
   }else {
     global $yaml_import_message;
     global $yaml_parse_errors;
     $yaml_import_message = __('YAML data parsed successfully, but contains formatting errors. See below for details.', 'radio-station');
     $errors = '<h2>'.$sanitized_show['show-title'].'</h2>'.__('Data file errors noted as follows:', 'radio-station').
               '<ul style="padding-left: 20px; list-style: disc;">' . $errors . '</ul>';
 		 $yaml_parse_errors = $errors;
 		 add_action('admin_notices', 'yaml_import__failure');
     return false;
   }
 }//function show_is_valid()

 //Validation helper function
 //validates the schedule portion of an imported YAML file. returns true if valid, false otherwise, with any error messages appended to $error_buffer
 function schedule_is_valid($schedule, &$error_buffer){
   /* expected format for $schedule data passed in. the presence of at least one day/time-block is enforced

   show-schedule:
     mon: #expressed as one of [sun, mon, tue, wed, thu, fri, sat]. Spelling out days ("Monday" or "monday") is also supported
      - ["05:30", "06:00", "disabled", "encore"] #optional 3rd and 4th parameters supported as indicated. Present only if true.
      - ["05:00", "17:30", ] #all time expressed in 24h format. First time is start-time, last time is end-time.
     wednesday:
      - ["05:30", "06:00"]
      - ["17:00", "17:30"]
     Friday:
      - ["05:30", "06:00"]
      - ["17:00", "17:30"]

   */
   $errors = '';

   $tmp_weekdays = array_keys($schedule);
   if (count($tmp_weekdays) > 0){ #at least one weekday is defined
     foreach ($tmp_weekdays as $day){
       if (in_array(strtolower($day), WEEKDAYS)){ #weekday format is valid
         if (count($schedule[$day]) > 0){ #at least one time pair is defined
          foreach($schedule[$day] as $time_pair){
            $tmp_first_part = array_slice($time_pair, 0, 2); #pull off the time pair itself
            //validate the time pair proper
            if (!(preg_match("/\d\d:\d\d/", $time_pair[0]) && preg_match("/\d\d:\d\d/", $time_pair[1]))){
              $errors .= '<li>' . __('show-schedule[<weekday>] time blocks must be in 24h format and have the form "04:55" (note 0 padding).', 'radio-station') . '</li>';
            }
            $tmp_2nd_part = array_slice($time_pair, 2); #the rest will be flags if present
            //validate flags if present
            if (!is_null($tmp_2nd_part[0])){
              switch ($tmp_2nd_part[0]){
                case 'disabled':
                      if (!is_null($tmp_2nd_part[1])){
                        if ($tmp_2nd_part[1] != 'encore'){
                         $errors .= '<li>' . __('Error, for show-schedule[<weekday>], only "disabled" and "encore" flags are allowed', 'radio-station') . '</li>';
                        }
                      }
                      break;
                case 'encore':
                      if (!is_null($tmp_2nd_part[1])){
                        if ($tmp_2nd_part[1] != 'disabled'){
                         $errors .= '<li>' . __('Error, for show-schedule[<weekday>], only "disabled" and "encore" flags are allowed', 'radio-station') . '</li>';
                        }
                      }
                      break;
                default:
                     $errors .= '<li>' . __('Error, for show-schedule[<weekday>], only "disabled" and "encore" flags are allowed', 'radio-station') . '</li>';
              }//switch
            }//if (count($tmp_2nd_part) > 0 && count($tmp_2nd_part) < 3)
          }//foreach($schedule[$day] as $time_pair)
         }else{
           $errors .= '<li>' . __('show-schedule[<weekday>] must reference an array of time blocks containing at least one element.', 'radio-station') . '</li>';
         }//if (count($schedule[$day]) > 0){
       }else{
         $errors .= '<li>' . __('Invalid weekday. show-schedule[<weekday>] must be one of "sun".."sat", or "sunday".."saturday" (case insensitive).', 'radio-station') . '</li>';
       }//if (in_array(strtolower($day), WEEKDAYS)){
     }//foreach ($tmp_weekdays as $day){
   }else{
     $errors .= '<li>' . __('show-schedule: must define at least one weekday.', 'radio-station') . '</li>';
   }//(count($tmp_weekdays) > 0){

   if ($errors == ''){
     return true;
   }else{
     $error_buffer .= $errors;
     return false;
   }
 }//function schedule_is_valid()

 //Validation helper function
 //removes all html tags except for those explicitly defined below
 function keep_basic_html_only($string, $with_paragraph = false){
   #paragraph
   $tmp = $string;
   if ($with_paragraph) {
     $tmp = str_replace('<p>', '&lt;p&gt;', $tmp);
     $tmp = str_replace('</p>', '&lt;/p&gt;', $tmp);
   }
   #bold
   $tmp = str_replace('<b>', '&lt;b&gt;', $tmp);
   $tmp = str_replace('</b>', '&lt;/b&gt;', $tmp);
   #stron
   $tmp = str_replace('<strong>', '&lt;strong&gt;', $tmp);
   $tmp = str_replace('</strong>', '&lt;/strong&gt;', $tmp);
   #italic
   $tmp = str_replace('<i>', '&lt;i&gt;', $tmp);
   $tmp = str_replace('</i>', '&lt;/i&gt;', $tmp);
   #emphesis
   $tmp = str_replace('<em>', '&lt;em&gt;', $tmp);
   $tmp = str_replace('</em>', '&lt;/em&gt;', $tmp);
   #mark
   $tmp = str_replace('<mark>', '&lt;mark&gt;', $tmp);
   $tmp = str_replace('</mark>', '&lt;/mark&gt;', $tmp);
   #small
   $tmp = str_replace('<small>', '&lt;small&gt;', $tmp);
   $tmp = str_replace('</small>', '&lt;/small&gt;', $tmp);
   #deleted text
   $tmp = str_replace('<del>', '&lt;del&gt;', $tmp);
   $tmp = str_replace('</del>', '&lt;/del&gt;', $tmp);
   #inserted text
   $tmp = str_replace('<ins>', '&lt;ins&gt;', $tmp);
   $tmp = str_replace('</ins>', '&lt;/ins&gt;', $tmp);
   #subscript
   $tmp = str_replace('<sub>', '&lt;sub&gt;', $tmp);
   $tmp = str_replace('</sub>', '&lt;/sub&gt;', $tmp);
   #superscript
   $tmp = str_replace('<sup>', '&lt;sup&gt;', $tmp);
   $tmp = str_replace('</sup>', '&lt;/sup&gt;', $tmp);

   #cleanup and return
   $tmp = strip_tags($tmp);
   $tmp = wp_strip_all_tags($tmp);
   return htmlspecialchars_decode(trim($tmp));
 }//function keep_basic_html_only()


 //Validation helper functions
 //parses whether or not field passed is a valid URL or ID
 function is_url_or_ID(&$field, $nullOK = false){
   $tmp_var = filter_var($field, FILTER_VALIDATE_URL);
   if ($tmp_var){
     $field = $tmp_var;
     return true;
   }else{ //show-image is not a URL so let's see if it's an integer
     $tmp_var = filter_var($field, FILTER_VALIDATE_INT);
     if ($tmp_var){
       $field = $tmp_var;
       return true;
     }else{
       error_log(">".print_r($field, TRUE)."< (".print_r($tmp_var, TRUE).")\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)
       if ($nullOK && is_null($field)){
         return true;
       }else {
         return false;
       }
     }
   }
 }//function is_url_or_ID()

 //function returns true if associative array
 function isAssoc(array $arr)
 {
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
 }
