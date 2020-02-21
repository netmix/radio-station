/* --------------------------- */
/* Radio Station Admin Scripts */
/* --------------------------- */
/* note: admin scripts are currently enqueued using wp_add_inline_script */
/* this file is necessary to ensure they are printed in the right place */
window.onload = function(){
  console.log("jQuery is " + typeof jQuery );

}//window.onload...
function check() {
  if(document.getElementById("delete-data-checkbox").checked===true){
    console.log("delete checked");
    jQuery('#delete-data-warning').attr('style', 'display: block;');
  }else{
    console.log("delete UN-checked");
    jQuery('#delete-data-warning').attr('style', 'display: none;');
  }
}
