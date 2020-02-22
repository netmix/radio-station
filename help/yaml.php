<?php
/*
 * Help file for import/export
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */
?>
<style type="text/css">
  .tg  {border-collapse:collapse;border-spacing:0;border:none;border-color:#aaa;}
  .tg td{font-family:Arial, sans-serif;font-size:14px;padding:5px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#aaa;color:#333;background-color:#fff;}
  .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:5px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-color:#aaa;color:#fff;background-color:#f38630;}
  .tg .tg-cly1{text-align:left;vertical-align:middle}
  .tg .tg-89io{background-color:#FCFBE3;font-weight:bold;text-align:right;vertical-align:middle}
  .tg .tg-wa1i{font-weight:bold;text-align:center;vertical-align:middle}
  .tg .tg-axdc{background-color:#FCFBE3;font-weight:bold;text-align:right;vertical-align:top}
  .tg .tg-yla0{font-weight:bold;text-align:left;vertical-align:middle}
  .tg .tg-hb2y{background-color:#FCFBE3;text-align:left;vertical-align:middle}
  .tg .tg-zt7h{font-weight:bold;text-align:right;vertical-align:middle}
  .tg .tg-l2oz{font-weight:bold;text-align:right;vertical-align:top}
  .tg .tg-0lax{text-align:left;vertical-align:top}
  .tg .tg-dg7a{background-color:#FCFBE3;text-align:left;vertical-align:top}

  .left-header{
    display: inline-block;
    width: 140px;
    margin:4px, 4px;
    padding:4px;
    /* border: 1px solid red; */
    text-align: right;
  }
  .right-header{
    display: inline-block;
    margin: 4px, 4px;
    margin-left: 4px;
    padding:4px;
  }
  .left-column{
    width: 140px;
  }
  .right-column{
  }
  div.scroll {
      margin:4px, 4px;
      padding:4px;
      /* background-color: green; */
      border-top: 1px;
      border-top-style: solid;
      border-top-color: #CBD0D4;
      width: 90%;
      height: 200px;
      overflow-x: hidden;
      overflow-x: auto;
      text-align:justify;
  }
</style>

<h2>Introduction</h2>
<p>
YAML files are used to import and export show data. You can read more about the YAML specification Radio Station uses
<a href="https://symfony.com/doc/current/components/yaml/yaml_format.html#collections">here</a>.
Each show is expressed in the YAML data file as a nameless array element at the root level. Within that element, all a
show's properties are expressed as key->value pairs. All fields are simple values (text, strings, boolean, etc.) except for
<strong>show-schedule:</strong>, which is a more complex structure and has its own help tab (see left). What follows is a
description of each of the fields supported in the definition of shows. Only <strong>show-title:</strong> is required.
</p>

<h2>Supported Fields</h2>
<div class="left-header"><strong style="font-size: 16px;">Field Key</strong></div>
<div class="right-header">
  <strong style="font-size: 16px;">Description</strong> &nbsp;&nbsp;
</div>
<div class="scroll">
  <table class="tg">
    <!-- <tr>
      <th class="tg-wa1i">Field<br></th>
      <th class="tg-yla0">Description</th>
    </tr> -->
    <tr>
      <td class="tg-89io left-column">show-title:</td>
      <td class="tg-hb2y right-column">A string describing the show. Limited HTML is supported. This is the only required field.</td>
    </tr>
    <tr>
      <td class="tg-zt7h left-column">show-description:</td>
      <td class="tg-cly1">A multi-line text block describing the show in detail. Limited HTML is supported</td>
    </tr>
    <tr>
      <td class="tg-89io left-column">show-excerpt:</td>
      <td class="tg-hb2y">A multi-line text block with a short summary of the show. Limited HTML is supported</td>
    </tr>
    <tr>
      <td class="tg-zt7h left-column">show-image:</td>
      <td class="tg-cly1">A valid URL reference to an existing image</td>
    </tr>
    <tr>
      <td class="tg-89io left-column">show-avatar:</td>
      <td class="tg-hb2y">A valid URL reference to an existing image</td>
    </tr>
    <tr>
      <td class="tg-zt7h left-column">show-header:</td>
      <td class="tg-cly1">A valid URL reference to an existing image</td>
    </tr>
    <tr>
      <td class="tg-89io left-column">upload-images:</td>
      <td class="tg-hb2y">Boolean value. "1", "true", "on", and "yes" equate to TRUE. Anything else including null is FALSE.</td>
    </tr>
    <tr>
      <td class="tg-zt7h left-column">show-tagline</td>
      <td class="tg-cly1">A short sentence or string about the show. May contain basic HTML.</td>
    </tr>
    <tr>
      <td class="tg-89io left-column">show-schedule:</td>
      <td class="tg-hb2y">An associative array of days and time-slots when the program runs (see separate help tab on left).</td>
    </tr>
    <tr>
      <td class="tg-zt7h left-column">show-url:</td>
      <td class="tg-cly1">A valid URL link to a separate web page about the show</td>
    </tr>
    <tr>
      <td class="tg-89io left-column">show-podcast:</td>
      <td class="tg-hb2y">A valid URL link to a separate web page about the show</td>
    </tr>
    <tr>
      <td class="tg-zt7h left-column">show-user-list:</td>
      <td class="tg-cly1">An array of email addresses corresponding to existing WordPress users. Non-matching entries are ignored.</td>
    </tr>
    <tr>
      <td class="tg-89io left-column">show-producer-list:</td>
      <td class="tg-hb2y">An array of email addresses corresponding to existing WordPress users. Non-matching entries are ignored.</td>
    </tr>
    <tr>
      <td class="tg-zt7h left-column">show-email:</td>
      <td class="tg-cly1">Email address for the show.</td>
    </tr>
    <tr>
      <td class="tg-89io left-column">show-active:</td>
      <td class="tg-hb2y">Boolean value. "1", "true", "on", and "yes" equate to TRUE. Anything else including null is FALSE.</td>
    </tr>
    <tr>
      <td class="tg-zt7h left-column">show-patreon:</td>
      <td class="tg-cly1">A show's patreon ID (see https://www.patreon.com)</td>
    </tr>
  </table>
</div>
&nbsp;<small>(Scroll to see all fields)</small>

<p>
  <small>
  <strong>Note:</strong> source URLs for show-image, avatar-image, and show-header, are stored in the show's metadata as show_image_url,
  avatar_image_url, and header_image_url.
  </small>
</p>
