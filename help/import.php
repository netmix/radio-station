<?php
/*
 * Help file for import/export
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */
?>
<style type="text/css">
  mark.w-red {
    color: red;
    background: none;
  }
  mark.warning{
    font-weight: bold;
    background: none;
  }
</style>

<h1>Import Show Data via YAML file</h1>
<p>
  To import and optionally replace all your show data with whatever is contained in a data file. Click the
  <strong>Select file</strong> button below and select the file you wish to import. Check the box to delete
  existing data if desired, then click the import button.
</p>
<p>
  <mark class="warning w-red">
  WARNING! </mark>If <strong>"Delete existing show data"</strong> is checked, importing will erase everything you currently have in your show database and
    replace it with whatever is in the file you import. There is no undo. Take a backup first.

</p>
