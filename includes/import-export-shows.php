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

 //FIXME debug code
 // error_log("t ". print_r($post_id,true)."\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)

  	//collection of useful template code.
  	//call __success if import is successful
  		// $yaml_import_message = __('this is a success message', 'radio-station');
  		// add_action('admin_notices', 'yaml_import__success');

  	//call __failure if we have a problem
  		// $yaml_import_message = __('this is a failure message', 'radio-station');
  		// add_action('admin_notices', 'yaml_import__failure');

  	// wp_safe_redirect( admin_url( 'admin.php?page=import-export-shows' ) ); exit;
    // error_log("YAML file uploaded but not parsed.\n", 3, "/tmp/my-errors.log"); //FIXME debugging code


 // -------------------------
 // - Import (and replace) all show data (YAML)
 // -------------------------
 //this function handles the response to the post requests from the page's form submissions
 add_action( 'admin_init', 'process_show_data_import' );
 function process_show_data_import() {
 	//using a global variable since that seems to be the only easy way to
 	//get a parameter to an add_action() callback function
 	global $yaml_import_message;
 	global $yaml_parse_errors;
 	$yaml_parse_errors = '';
 	$yaml_import_message = '';

  switch ($_POST['action']) {
    case 'radio_station_yaml_import_action':
      import_helper();
      break;
    case 'radio_station_yaml_export_action':
      export_helper();
      break;
    default:
      //do nothing.
      return;
  }
 }//process_show_data_import()

 //import helper function
 function import_helper(){
   	global $yaml_import_message;
   	global $yaml_parse_errors;
    if( ! wp_verify_nonce( $_POST['yaml_import_nonce'], 'yaml_import_nonce' ) )
  		return;
  	if( ! current_user_can( 'manage_options' ) )
  		return;
  	$extension = end( explode( '.', $_FILES['import_file']['name'] ) );
  	if( $extension != 'yaml' ) {
  		//call __failure if we have a problem
  		$yaml_import_message = __('Please upload a valid YAML file.', 'radio-station');
  		add_action('admin_notices', 'yaml_import__failure');
  		// wp_die( __( 'Please upload a valid YAML file' ) );
  	}
  	$import_file = $_FILES['import_file']['tmp_name'];
  	if( empty( $import_file ) ) {
  		//call __failure if we have a problem
  		$yaml_import_message = __('Please upload a file to import.', 'radio-station');
  		add_action('admin_notices', 'yaml_import__failure');
  		// wp_die( __( 'Please upload a file to import' ) );
  	}

    //fetch the fate of the existing show data (delete existing show data checkbox state)
    $existing_data_fate = filter_var($_POST['delete_show_data'], FILTER_VALIDATE_BOOLEAN);

  	//parse and save the yaml file if possible, returning success or failure messages to the user as appropriate
  	if (yaml_import_ok($import_file, $existing_data_fate)){
  		//$globals $yaml_import_message, and $yaml_parse_errors are empty
      if ($existing_data_fate){
    		$yaml_import_message = __('Successfully parsed and imported YAML file, deleting pre-existing show data.', 'radio-station');
      }else{
    		$yaml_import_message = __('Successfully parsed and imported YAML file. Pre-existing show data remains unchanged.', 'radio-station');
      }
  		add_action('admin_notices', 'yaml_import__success');
  	}else{
  		//global $yaml_import_message contins message to display to the user
  		//global $yaml_parse_errors contains the detail for display by import-export-shows.php
  		add_action('admin_notices', 'yaml_import__failure');
  	}
 }//import_helper()

 //export helper function
 function export_helper(){
 	 global $yaml_import_message;
   global $yaml_parse_errors;
   if( ! wp_verify_nonce( $_POST['yaml_export_nonce'], 'yaml_export_nonce' ) )
     return;
   if( ! current_user_can( 'manage_options' ) )
     return;


   $image_url = filter_var($_POST['image_prefix_url'], FILTER_VALIDATE_URL);
   $upload_dir = wp_upload_dir();
   $base_url = $upload_dir['baseurl'];
   $base_dir = $upload_dir['basedir'];

   if ($_POST['export_file_name']){
     $export_filename = sanitize_file_name($_POST['export_file_name']);
   }else{ //create file name based on date/time
     $file_date = new DateTime();
     // $result = $date->format('Y-m-d H:i:s');
     $export_filename = $file_date->format('Y-m-d-'). time(). '_show_data.yaml';
   }

   //create the zip files that the user can download
   $image_index = create_show_image_archive();
   if (! $image_index){
     //something went wrong, abort
     return;
   }

   //create show_data.yaml
   $yaml_data = get_published_shows($image_url, $image_index);
   $yaml = Yaml::dump($yaml_data);
   $yaml = "---\n#Show data file (YAML format)\n\n" . $yaml . "...\n";
   file_put_contents($base_dir . '/show_data.yaml', $yaml);

   //set up links for user to download the files
   $yaml_import_message = __('Export successful. Use the following link(s) to download your data.', 'radio-station');
   if ($_POST['image_prefix_url']){
     $yaml_import_message .= "
          <ul style=\"padding-left: 10px;\">
            <li><a href=\"$base_url/show_images.zip\" download>Image zip file</a> $zip_size (bytes)</li>
            <li><a href=\"$base_url/show_images.tgz\" download>Image tgz file</a></li>
            <li><a href=\"$base_url/show_data.yaml\" download=\"$export_filename\">Show data file (YAML)</a></li>
          </ul>
     ";
     $yaml_import_message .= __('Download one of the image files and the data file. See help for details on how to stage an import including images.', 'radio-station');
   }else{
     $yaml_import_message .= "
          <ul style=\"padding-left: 10px;\">
            <li><a href=\"$base_url/show_data.yaml\" download=\"$export_filename\">Show data file (YAML)</a></li>
          </ul>
     ";
     $yaml_import_message .= __('Images <strong>not</strong> exported. See help for details on how to include images.', 'radio-station');
   }

   //queue up messaging and return
   add_action('admin_notices', 'yaml_import__success');
 }//export_helper()

 //helper function
 //returns an array of all published shows
 function get_published_shows($image_location = false, $image_index){
   $parameters = array(
     'posts_per_page'     => -1,
     'post_type'          => 'show',
     'post_status'        => 'publish'
   );
   $shows = get_posts($parameters);
   $yaml_shows = array();
   foreach ($shows as $show){
      //fetch all the metadata for this show
      $metadata = get_post_meta($show->ID);
      // error_log("POST METADATA---------\n". print_r($metadata,true)."\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)
      //build an array matching the YAML structure we wish to have
      //these fields are core post fields
      $yaml_show = array();
      $yaml_show['show-title'] = $show->post_title;
      $yaml_show['show-description'] = $show->post_content;
      $yaml_show['show-excerpt'] = $show->post_excerpt;
      $yaml_show['show-schedule'] = get_show_schedule($show->ID);
      $yaml_show['show-user-list'] = get_show_users($show->ID);
      $yaml_show['show-producer-list'] = get_show_producers($show->ID);

      //these fields are metadata
      $yaml_show['show-tagline'] = $metadata['show_tagline'][0];
      $yaml_show['show-url'] = $metadata['show_link'][0];
      if ($metadata['show_podcast'][0] == ''){
        $yaml_show['show-podcast'] = null;
      }else{
        $yaml_show['show-podcast'] = $metadata['show_podcast'][0];
      }
      $yaml_show['show-email'] = $metadata['show_email'][0];
      $yaml_show['show-active'] = $metadata['show_active'][0];
      $yaml_show['show-patreon'] = $metadata['show_patreon'][0];

      //these fields are image related. Populate according to 'upload-images' setting
      if ($image_location){
        $yaml_show['show-image'] = "$image_location/" . $image_index{$metadata['show_image_id'][0]}['file'];
        $yaml_show['show-avatar'] = "$image_location/" . $image_index{$metadata['show_avatar'][0]}['file'];
        $yaml_show['show-header'] = "$image_location/" . $image_index{$metadata['show_header'][0]}['file'];
        $yaml_show['upload-images'] = 'yes';
      }else{
        $yaml_show['show-image'] = null;
        $yaml_show['show-avatar'] = null;
        $yaml_show['show-header'] = null;
        $yaml_show['upload-images'] = 'no';
      }

      array_push($yaml_shows, $yaml_show);
   }//foreach ($shows...
   return $yaml_shows;
 }//get_published_shows()

 //helper function
 //Display success admin message
 function yaml_import__success(){
 	global $yaml_import_message;
 	?>
 	<div class="notice notice-success is-dismissible">
 		<p><?php echo $yaml_import_message; ?></p>
 	</div>
 	<?php
 }

 //helper function
 //Display failure admin message
 function yaml_import__failure($msg){
 	global $yaml_import_message;
 	?>
 	<div class="notice notice-error is-dismissible">
 		<p><?php echo $yaml_import_message; ?></p>
 	</div>
 	<?php
 }

// this function handles processing data from a YAML file and writing it to the DB
function yaml_import_ok($file_name = '', $delete_existing = false){
  global $yaml_import_message;
  global $yaml_parse_errors;

  //try importing the YAML file
  try {
    $shows = Yaml::parseFile($file_name);
  } catch (ParseException $exception) {
    $yaml_parse_errors = $exception->getMessage();
    $yaml_import_message = __('YAML import error. See below for details.', 'radio-station');
    return false;
  }

  //Base import worked, proceed with import if show data validates
  $return_value = true;
  foreach ($shows as $show){
    $sanitized_show = array();
    if (show_is_valid($show, $sanitized_show)){
      //FIXME re-factor the code to insert an "are you sure" dialogue step prior to actually doing the import

      if ($delete_existing){
        //remove all existing show data prior to import if requested
        delete_show_data();
      }

      //convert the show schedule metadata to the legacy format
      $converted_show_schedule = convert_show_schedule($sanitized_show['show-schedule']);
      //retrieve the show's producer and user IDs from the DB
      $show_users = convert_user_list($sanitized_show['show-user-list']);
      $show_producers = convert_user_list($sanitized_show['show-producer-list']);
      //format show image url's for inclusion if supplied
      $image_urls = array();
      if (!is_null($sanitized_show['show-image'])){ $image_urls['show_image_url'] = $sanitized_show['show-image']; }
      if (!is_null($sanitized_show['show-avatar'])){
        $image_urls['avatar_image_url'] = $sanitized_show['show-avatar'];
        if ($sanitized_show['upload-images']){ #upload avatar image here so we have its ID for the metadata below
          $image_urls['show_avatar'] = upload_image($sanitized_show['show-avatar'], null);
          $image_urls['show_header'] = upload_image($sanitized_show['show-header'], null);
        }
      }
      if (!is_null($sanitized_show['show-header'])){ $image_urls['header_image_url'] = $sanitized_show['show-header']; }
      $post_data = array(
        'post_title'   => $sanitized_show['show-title'], //show-title
        'post_content' => $sanitized_show['show-description'], //show-desciption
        'post_excerpt' => $sanitized_show['show-excerpt'], //show-excerpt
        'post_status'  => 'publish',
        'post_type'    => 'show',
        'meta_input' => array(
         //all the post meta data that doesn't require special handling
          'imported_on'        => date("D M d, Y G:i ") . "UTC",
          'show_tagline'       => $sanitized_show['show-tagline'],
          'show_link'          => $sanitized_show['show-url'],
          'show_podcast'       => $sanitized_show['show-podcast'],
          'show_email'         => $sanitized_show['show-email'],
          'show_active'        => $sanitized_show['show-active'],
          'show_patreon'       => $sanitized_show['show-patreon'],
          'show_sched'         => $converted_show_schedule,
          'show_user_list'     => $show_users,
          'show_producer_list' => $show_producers,
          'show_image_id'      => ''
        ) + $image_urls
      );
      //insert the new show into the database
      $new_show = wp_insert_post($post_data);

      //upload show-image & show-header if show inserted correctly and upload of images is called for
      if ($sanitized_show['upload-images'] && is_int($new_show) && $new_show > 0){
        $image_id = upload_image($sanitized_show['show-image'], $new_show);
        update_post_meta($new_show, 'show_image_id', $image_id); //store the image ID so we can use it later
      }
    } else {
      $return_value = false;
      //errors are accumulated in the global $yaml_import_message, for display to the user
    } //if_show_is_valid
  }

  return $return_value;
}//function yaml_import_ok()

//this function uploads an image to the media library and adds it as the featured image of a post if $post_id is supplied
function upload_image($image_url, $post_id = null){
  $image_name       = basename(parse_url($image_url, PHP_URL_PATH));
  $upload_dir       = wp_upload_dir(); // Set upload folder
  $image_data       = file_get_contents($image_url); // Get image data
  $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
  $filename         = basename( $unique_file_name ); // Create image file name

  // Check folder permission and define file location
  if( wp_mkdir_p( $upload_dir['path'] ) ) {
      $file = $upload_dir['path'] . '/' . $filename;
  } else {
      $file = $upload_dir['basedir'] . '/' . $filename;
  }

  // Create the image  file on the server
  file_put_contents( $file, $image_data );

  // Check image file type
  $wp_filetype = wp_check_filetype( $filename, null );

  // Set attachment data
  $attachment = array(
      'post_mime_type' => $wp_filetype['type'],
      'post_title'     => sanitize_file_name( $filename ),
      'post_content'   => '',
      'post_status'    => 'inherit'
  );

  // Create the attachment
  $attach_id = wp_insert_attachment( $attachment, $file);

  // Include image.php
  require_once(ABSPATH . 'wp-admin/includes/image.php');

  // Define attachment metadata
  $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

  // Assign metadata to attachment
  wp_update_attachment_metadata( $attach_id, $attach_data );

  // And finally assign featured image to the post if id has been supplied
  if ($post_id){
    set_post_thumbnail( $post_id, $attach_id );
  }
  return $attach_id;
}//function add_featured_image()

//this function creates an archive of all the images associated with all published shows defined in the system.
function create_show_image_archive(){
  //Three files are created in the WP uploads directory: show_images.tgz, show_images.zip, and show_images.yaml
  //The images .tgz and .zip files contain the actual image files. The .yaml file contains the file names, and size
  //information needed to render the data to the user. If previous files are found, they are replaced.
  global $yaml_parse_errors;
  global $yaml_import_message;
  $parameters = array(
    'posts_per_page'     => -1,
    'post_type'          => 'show',
    'post_status'        => 'publish'
  );
  $shows = get_posts($parameters);
  $upload_dir = wp_upload_dir();
  $base_dir = $upload_dir['basedir'];
  $image_index = array();

  //remove the previous files if they exit
  delete_folder($base_dir . '/export');
  unlink($base_dir . '/show_images.yaml');
  unlink($base_dir . '/show_images.tgz');
  unlink($base_dir . '/show_images.zip');

  //create the export directory that will hold our image files raising suitable errors on failure
  if (! mkdir($base_dir . '/export')){
    $yaml_parse_errors = '';
    $yaml_import_message = __('Failed to create export folder', 'radio-station') . ' ' . $base_dir . '/export';
	  add_action('admin_notices', 'yaml_import__failure');
    return false;
  }

  //populate $image_index with all the show image data, indexed by image_ID
  foreach ($shows as $show){
    //fetch all the metadata for this show
    $metadata = get_post_meta($show->ID);

      foreach (['show_image_id', 'show_avatar', 'show_header'] as $image_ref){
        if (! array_key_exists($image_ref, $metadata)){
          break;
        }
        $src = get_image_path($metadata[$image_ref][0]);
        $dst = $base_dir . '/export/' . basename($src);
        if (! copy($src, $dst)) {
          $yaml_parse_errors = "src: $src</br> dst: $dst</br>";
          $yaml_import_message = __('Failed to copy file. See below for details', 'radio-station');
    		  add_action('admin_notices', 'yaml_import__failure');
          return false;
        }
        $image_index[$metadata[$image_ref][0]] = ['path' => $dst, 'file' => basename($src)];
      }
      //create the YAML index file
  }//foreach ($shows...)

  //create the zip file
  $zip = new ZipArchive;
  if ($zip->open($base_dir . '/show_images.zip', ZipArchive::CREATE) === true){
    if ($handle = opendir($base_dir . '/export')){
      while (false !== ($entry = readdir($handle))){
        if ($entry != "." && $entry != ".." && !is_dir("$base_dir/" . $entry)){
          $zip->addFile("$base_dir/export/" . $entry, $entry);
        }
      }
      closedir($handle);
    }
    $zip->close();
  }else{
    $yaml_parse_errors = "";
    $yaml_import_message = __('Failed to create show_images.zip', 'radio-station');
	  add_action('admin_notices', 'yaml_import__failure');
    return false;
  }

  //create the tgz file
  $tgz = new PharData($base_dir . '/show_images.tar');
  $tgz->buildFromDirectory($base_dir . '/export');
  $tgz->compress(Phar::GZ);
  unset($tgz);
  unlink($base_dir . '/show_images.tar');
  rename($base_dir . '/show_images.tar.gz', $base_dir . '/show_images.tgz');
  return $image_index;
}//create_show_image_archive()

//helper function
//deletes a folder containing files
function delete_folder($path) {
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            delete_folder(realpath($path) . '/' . $file);
        }
        return rmdir($path);
    }else if (is_file($path) === true) {
        return unlink($path);
    }
    return false;
}//function delete_folder()

//helper function
//retrieves the absolute filesystem path of the passed image_id if available. Can be used to retrieve intermediate sizes also.
function get_image_path($image_id, $size = 'full') {
    $file = get_attached_file($image_id, true);
    if (empty($size) || $size === 'full') {
        // for the original size get_attached_file is fine
        return realpath($file);
    }
    if (! wp_attachment_is_image($image_id) ) {
        return false; // the id is not referring to a media
    }
    $info = image_get_intermediate_size($image_id, $size);
    if (!is_array($info) || ! isset($info['file'])) {
        return false; // probably a bad size argument
    }

    return realpath(str_replace(wp_basename($file), $info['file'], $file));
}

//this function returns an array of email addresses of show users for the given show ID
function get_show_users($show_id){
  $users = get_post_meta($show_id, 'show_user_list', true);
  $email_list = array();
  foreach ($users as $user){
    $user_obj = get_user_by('ID', $user);
    array_push($email_list, $user_obj->user_email);
  }
  return $email_list;
}

//this function returns an array of email addresses of show producers for the given show ID
function get_show_producers($show_id){
  $producers = get_post_meta($show_id, 'show_producer_list', true);
  $email_list = array();
  foreach ($producers as $producer){
    $user_obj = get_user_by('ID', $producer);
    array_push($email_list, $user_obj->user_email);
  }
  return $email_list;
}

//this function takes an array of email addresses, and returns, if possible, an array of matching WordPress user ID's.
function convert_user_list($users_email_array){
  $tmp = array();
  foreach ($users_email_array as $user){
    $uid = get_user_by('email', $user);
    if($uid){
      array_push($tmp,$uid->ID);
    }
  }
  return $tmp;
}//function convert_user_list()

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

   //validate upload-images... true for "1", "true", "on", and "yes", false otherwise
   if (!is_null($show['upload-images'])){
     $sanitized_show['upload-images'] = filter_var($show['upload-images'], FILTER_VALIDATE_BOOLEAN);
   }else{
     $sanitized_show['upload-images'] = false; #null defaults to inactive
     // $errors .= '<li>' . __('upload-images: may not be null.','radio-station') . '</li>';
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
   if (is_array($tmp_var) && !isAssoc($tmp_var)){
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
     $tmp_var = filter_var($show['show-active'], FILTER_VALIDATE_BOOLEAN);
     if($tmp_var){
       $sanitized_show['show-active'] = "on";
     }else{
       $sanitized_show['show-active'] = null;
     }
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

//this function converts the show-schedule from the 24h time format used in the YAML file to the am/pm internal structure
function convert_show_schedule($show_schedule){
  //$show is the post ID of the show in question
  //$show_schdule is an associative array as documented in the contextual help under import/export (see also /help/show-schedule.php)
  //data is assumed to be validated. i.e. a valid show ID is passed and $show_schedule contains at least 1 valid timeblock
  $converted_schedule = array();

  foreach ($show_schedule as $day=>$times){ //loop through the days of the week
    foreach($times as $timeblock){//loop through each time block for a given day
      $tmp = array();
      $tmp['day'] = canonicalize_day($day);
      // - ["05:30", "06:00", "disabled", "encore"]
      //convert start time in block
      $tmp['start_hour'] = substr($timeblock[0],0,2);
      $meridian = '';
      if (intval($tmp['start_hour']) > 12){
        $tmp['start_hour'] = trim(strval(intval($tmp['start_hour'])-12),'0');
        $meridian = 'pm';
      }else{
        $tmp['start_hour'] = trim($tmp['start_hour'],'0');
        $meridian = 'am';
      }
      $tmp['start_min'] = substr($timeblock[0],3,2);
      $tmp['start_meridian'] = $meridian;
      //convert end time in block
      $tmp['end_hour'] =  substr($timeblock[1],0,2);
      if (intval($tmp['end_hour']) > 12){
        $tmp['end_hour'] = trim(strval(intval($tmp['end_hour'])-12),'0');
        $meridian = 'pm';
      }else{
        $tmp['end_hour'] = trim($tmp['end_hour'],'0');
        $meridian = 'am';
      }
      $tmp['end_min'] =  substr($timeblock[1],3,2);
      $tmp['end_meridian'] = $meridian;
      if (in_array('encore', $timeblock)){
        $tmp['encore'] = 'on';
      }
      if (in_array('disabled', $timeblock)){
        $tmp['disabled'] = 'yes';
      }
      //push the converted structure on to the stack
      array_push($converted_schedule, $tmp);
    }//foreach($times...)
  }//foreach($show_schedule...)
  return $converted_schedule;
}//convert_show_schedule()

//this function returns a datastructure describing a show's schedule (used by export_helper() function via get_published_shows())
function get_show_schedule($show_id){
  $schedule = get_post_meta($show_id, 'show_sched', true);
  $converted_schedule = array();
  foreach ($schedule as $tb){ //loop through all time blocks
    $accumulator = array(); //convert time block array to internal 24h format
    if ($tb['start_meridian'] == 'pm'){
      $start_time = sprintf('%02u:%02u', $tb['start_hour'] + 12, $tb['start_min']);
    }else{
      $start_time = sprintf('%02u:%02u', $tb['start_hour'], $tb['start_min']);
    }
    if ($tb['end_meridian'] == 'pm'){
      $end_time   = sprintf('%02u:%02u', $tb['end_hour'] + 12, $tb['end_min']);
    }else{
      $end_time   = sprintf('%02u:%02u', $tb['end_hour'], $tb['end_min']);
    }
    array_push($accumulator, $start_time, $end_time);
    if ($tb['disabled'] == 'yes') array_push($accumulator, 'disabled');
    if ($tb['encore'] == 'on') array_push($accumulator, 'encore');

    //add finished array to the $converted_schedule array under the correct day of the week
    if (array_key_exists($tb['day'], $converted_schedule)){
      array_push($converted_schedule[$tb['day']], $accumulator);
    }else{
      $converted_schedule[$tb['day']][0] = $accumulator;
    }
  }
  return $converted_schedule;
}
//this function deletes all shows and associated data from the database
function delete_show_data(){
  //get an array of all show CPT ids
  $parameters = array(
    'posts_per_page'     => -1,
    'post_type'          => 'show',
  );
  $shows = get_posts($parameters);
  foreach ($shows as $show){
     //delete the images associated with the given show
     $avatar_id = get_post_meta($show->ID, 'show_avatar', true);
     wp_delete_attachment($avatar_id);
     $thumbnail_id = get_post_meta($show->ID, '_thumbnail_id', true);
     wp_delete_attachment($thumbnail_id);
     $header_id = get_post_meta($show->ID, 'show_header', true);
     wp_delete_attachment($header_id);

     //now we delete the show itself (deletes metadata too)
     wp_delete_post($show->ID, true);
  }
}//delete_show_data()

//convert_show_schedule helper function
//converts the various day formats to full-length capitalized form
function canonicalize_day($day){
  define('DAYS_LOOKUP', array(
    'sun' => 'Sunday',
    'mon' => 'Monday',
    'tue' => 'Tuesday',
    'wed' => 'Wednesday',
    'thu' => 'Thursday',
    'fri' => 'Friday',
    'sat' => 'Saturday'
  ));
  return DAYS_LOOKUP[substr(strtolower($day),0,3)];
}//canonicalize_day()

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

//Validation helper function
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
     if ($nullOK && is_null($field)){
       return true;
     }else {
       return false;
     }
   }
 }
}//function is_url_or_ID()

//Validation helper function
//function returns true if associative array
function isAssoc(array $arr){
  if (array() === $arr) return false;
  return array_keys($arr) !== range(0, count($arr) - 1);
}

// Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
function file_upload_max_size() {
  static $max_size = -1;

  if ($max_size < 0) {
    // Start with post_max_size.
    $post_max_size = parse_size(ini_get('post_max_size'));
    if ($post_max_size > 0) {
      $max_size = $post_max_size;
    }

    // If upload_max_size is less, then reduce. Except if upload_max_size is
    // zero, which indicates no limit.
    $upload_max = parse_size(ini_get('upload_max_filesize'));
    if ($upload_max > 0 && $upload_max < $max_size) {
      $max_size = $upload_max;
    }
  }
  return $max_size;
}

//helper function
function parse_size($size) {
  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
  if ($unit) {
    // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
  }
  else {
    return round($size);
  }
}

//returns human readable file size string
function convert_filesize($bytes, $decimals = 2){
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
