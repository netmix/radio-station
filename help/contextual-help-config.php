<?php
/*
 * Contextual help system for radio-station plugin
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */

 function rs_contextual_help( $contextual_help, $screen_id, $screen ) {

  //Add a custom if statement stanza per screen you wish to include help on.
  //Make sure the screen id in the if condition matches the page where you want the included help to appear
  //and edit the id and title in the add_help_tab() call to suite. id must be unique accross all help tabs.
  //you can create multiple tabs on the same help screen by duplicating the inner
  //if statement multiple times (with a unique help tab id for each one).


  //uncomment line below and view log file to id a particular screen
  error_log("Displaying screen ". print_r($screen->id,true)."\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)
  if ('edit-show' == $screen->id ) {
    //import feature documentation tab
    $help_file = RADIO_STATION_DIR . '/help/edit-show.php';
    if (file_exists($help_file)) {
      $content = render_php($help_file);
      $screen->add_help_tab(array(
        'id'        =>  'rs-edit-show',
        'title'     =>  __('Managing Shows'),  //tab name
        'content'   =>  $content
      ));
    }
  }//if ( $prefix . 'import-export-shows' == $screen->id ) {


  //contextual help for import/export screen
  $prefix = $screen->parent_base . '_page_';
  if ( $prefix . 'import-export-shows' == $screen->id ) {

    //import feature documentation tab
    $help_file = RADIO_STATION_DIR . '/help/import.php';
    if (file_exists($help_file)) {
      $content = render_php($help_file);
      $screen->add_help_tab(array(
        'id'        =>  'rs-import',
        'title'     =>  __('Import'),  //tab name
        'content'   =>  $content
      ));
    }

    //export feature documentation tab
    $help_file = RADIO_STATION_DIR . '/help/export.php';
    if (file_exists($help_file)) {
      $content = render_php($help_file);
      $screen->add_help_tab(array(
        'id'        =>  'rs-export',
        'title'     =>  __('Export'),  //tab name
        'content'   =>  $content
      ));
    }

    //YAML file format documentation tab
    $help_file = RADIO_STATION_DIR . '/help/yaml.php';
    if (file_exists($help_file)) {
      $content = render_php($help_file);
      $screen->add_help_tab(array(
        'id'        =>  'yaml-data-format',
        'title'     =>  __('YAML format'),  //tab name
        'content'   =>  $content
      ));
    }

    //show-schedule key documentation tab
    $help_file = RADIO_STATION_DIR . '/help/show-schedule.php';
    if (file_exists($help_file)) {
      $content = render_php($help_file);
      $screen->add_help_tab(array(
        'id'        =>  'rs-show-schedule',
        'title'     =>  __('show-schedule:'),  //tab name
        'content'   =>  $content
      ));
    }


  }//if ( $prefix . 'import-export-shows' == $screen->id ) {


  return $contextual_help;
}
add_action( 'contextual_help', 'rs_contextual_help', 10, 3 );

//returns the evaluated contents of a php file
function render_php($path)
{
    ob_start();
    include($path);
    $var=ob_get_contents();
    ob_end_clean();
    return $var;
}
