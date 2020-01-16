<?php
/*
 * Import/Export Show data from/to YAML file
 * Author: Andrew DePaula
 * @Since: 2.3.1 (unsure about this... assumed, since that's the release this feature is tagged to)
 * (c) Copyright 2020
 * Licence: GPL3
 */

 //this function handles processing data from a YAML file. Assumes it is already parsed and
 //expects an array of shows passed as $yaml_object, in the following format:
 /*
       array (
         0 => array (
           'show-title' => 'Your Story Hour',
           'show-description' => 'Dramatized half-hour stories taken from sacred and secular history...',
           'show-image' => 'https://lifetalk.net/wp-content/uploads/2019/12/your-story-hour.jpg',

           'show-byline' => 'with Aunt Carole and Uncle Dan',
           'show-day' => 'monday',
           'show-times' => array(
              0 => array(
                0 => "05:30",
                1 => "06:00"
              ),
              1 => array(
                0 => "17:00",
                1 => "17:30"
              )
           )
           'show-url' => 'https://lifetalk.net/programs/your-story-hour/',
           'show-podcast: ''
         ),
         1 => array (
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

 //this function handles processing data from a YAML file and writing it to the DB
 function save_yaml_import_data($yaml_object = array()) {
   //We assume the YAML file is expressed in the US Pacific Timezone
   date_default_timezone_set('America/Los_Angeles');
   //loop through each day of the week
   foreach ($yaml_object as $show){
     //loop through each program on a given day
   }


   error_log("YAML file uploaded and parsed.\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)
 }
