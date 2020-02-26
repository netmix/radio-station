/* --------------------------- */
/* Radio Station Admin Scripts */
/* --------------------------- */
/* note: admin scripts are currently enqueued using wp_add_inline_script */
/* this file is necessary to ensure they are printed in the right place */
window.onload = function(){
  // console.log("jQuery is " + typeof jQuery );

  del = jQuery('#del-checkbox-div');
  adv = jQuery('#advanced-checkbox-div');

  //set the initial state of the checkboxes
  del.checkbox('uncheck');
  adv.checkbox('uncheck');

  //show/hide the warning based on the state of the delete data checkbox
  del.on("change", function(){
      if(del.checkbox('is checked') === true){
        jQuery('#delete-data-warning').attr('style', 'display: block;');
      }else{
        jQuery('#delete-data-warning').attr('style', 'display: none;');
      }
  });

  //show/hide the advanced optiosn based on the state of the Advanced checkbox
  adv.on("change", function(){
      if(adv.checkbox('is checked') === true){
        jQuery('#advanced-options').attr('style', 'display: block;');
      }else{
        jQuery('#advanced-options').attr('style', 'display: none;');
      }
  });


  //populate the filename next to the Import button once a file is selected
  jQuery("#yamlfileinput").on( "change", function () {
    // alert();
    string = jQuery(this).val();
    string = string.replace(/^.+fakepath\\/, "")
    jQuery("#upload-file-name").html(string);
    jQuery("#upload-button").addClass('green');
  });

}//window.onload...

function enable_spinner(spinner_to_enable) {
  if (spinner_to_enable == 'import'){
    jQuery('#import-spinner').addClass('active');
  }
  if (spinner_to_enable == 'export'){
    jQuery('#export-spinner').addClass('active');
  }
}
