<?php
/*
 * Import/Export Show admin screen template
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */


 $max_upload_size = convert_filesize(file_upload_max_size());
 ?>

<!-- <div style="width: 620px; padding: 10px"> -->
<h2><?php esc_html_e( 'Import/Export Show Data', 'radio-station' ); ?></h2>
<!-- <div style="padding-left: 15px"> -->



<div class="ui grid">
  <div class="three column row">
    <!-- import pane -->
    <div class="seven wide column ui container segment">
      <h2 class="ui header"><?php _e('Import', 'radio-station') ?></h2>
      <!-- import form goes here -->
      <form class="ui form" method="POST" action="/wp-admin/admin.php?page=import-export-shows" enctype="multipart/form-data">
        <div style="height: 250px;">
          <p class="form-text">
          <?php
    		   _e( 'Import show data from a YAML file.', 'radio-station' );
          ?>
          </p>

          <div id="del-checkbox-div" class="ui checkbox">
            <input type="hidden" value="0" name="delete_show_data" onclick=check()>
            <input id="delete-data-checkbox" type="checkbox" value="1" name="delete_show_data">
            <label for="delete-data-checkbox"> <?php _e('Delete existing show data', 'radio-station')?> </label>
          </div>
          <input type="file" (change)="fileEvent($event)" id="yamlfileinput" class="inputfile" name="import_file"/>
          <label id="upload-button"for="yamlfileinput" class="ui basic button">
            <i class="ui upload icon"></i>
            Select file
          </label>
          <p></p>
          <!-- <p id="delete-data-warning" style="display: none;"> -->
          <p id="delete-data-warning" class="form-text">
          <?php _e( '<span style="color: red;"><strong>WARNING</strong></span>, this will delete all show data you have currently configured, including associated images. We strongly suggest exporting a backup first.', 'radio-station'); ?>
          </p>

        </div> <!-- style="height: 250px... -->
        <input type="hidden" name="action" value="radio_station_yaml_import_action" />
        <?php wp_nonce_field( 'yaml_import_nonce', 'yaml_import_nonce' ); ?>
        <button class="ui left floating button" type="submit" onclick="enable_spinner('import')"><?php _e('Import', 'radio-station') ?></button>
        <div id="upload-file-name">
          No file selected for import.
        </div>
      </form>
      <div id="import-spinner" class="ui large centered floating loader" style="top:50%;"></div>
    </div>

    <!-- export pane -->
    <div class="seven wide column ui container segment">
      <h2 class="ui header"><?php _e('Export', 'radio-station') ?></h2>
      <!-- import form goes here -->
      <form class="ui form" method="POST" action="/wp-admin/admin.php?page=import-export-shows" enctype="multipart/form-data">
        <div style="height: 250px;">
          <p class="form-text">
          <?php
           _e( 'Export show data to a downloadable file. Does not include images by default. Check <strong>Advanced</strong> for additional options.', 'radio-station' );
          ?>
          </p>

          <div id="advanced-checkbox-div" class="ui checkbox">
            <input type="hidden" value="0" name="advanced_options">
            <input id="advanced-options-checkbox" type="checkbox" checked="" name="advanced_options">
            <label> <?php _e('Advanced', 'radio-station')?> </label>
          </div>
          <p></p>
          <div id="advanced-options"> <!-- hidden by default (see css) -->
              <div class="field">
                <label><?php _e('YAML file name', 'radio-station') ?></label>
                <?php
                $tmp_date = new DateTime();
                $export_filename = $tmp_date->format('Y-m-d-'). time(). '_show_data.yaml';
                ?>
                <input type="text" name="export_file_name" placeholder="<?php _e('Default similar to', 'radio-station'); echo " $export_filename" ?>">
              </div>
              <div class="field">
                <label><?php _e('Image location URL', 'radio-station') ?></label>
                <input type="text" name="image_prefix_url" placeholder="<?php _e('URL where show images will be staged for import (see help)', 'radio-station') ?>">
              </div>
          </div>

        </div> <!-- style="height: 250px... -->
        <input type="hidden" name="action" value="radio_station_yaml_export_action" />
        <?php wp_nonce_field( 'yaml_export_nonce', 'yaml_export_nonce' ); ?>
        <button class="ui left floating button" type="submit" onclick="enable_spinner('export')"><?php _e('Export', 'radio-station') ?></button>
      </form>
      <div id="export-spinner" class="ui large centered floating loader" style="top:50%;"></div>
    </div>

    <div class="one wide column">
    </div>
  </div> <!-- three column row -->
</div> <!-- ui grid -->


<div id="error-log-div" style="width:100%;">
	<?php
	//pull in any parsing error details for display to the user
	global $yaml_parse_errors;
	// echo '<pre>';
	// echo wordwrap($yaml_parse_errors, 85);
	echo $yaml_parse_errors;
	// echo '</pre>';

	?>
</div>
<!-- </div> -->
