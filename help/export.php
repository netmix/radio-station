<?php
/*
 * Help file for import/export
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */
?>

<h1>Exporting All Show Data</h1>
<p>
To export your show data, simply click the <strong>Export</strong> button. Under <strong>Advanced</strong> you can optionally
fill in the two additional fields below to specify a custom export file name, and the URL to a location on the Internet,
where you will stage the show image files for re-import.
</p>
<p>
When the system is done processing, you'll see a message box with links to download the files. Either one file, or three files are generated
depending on the optional data supplied, and must be downloaded seperately. The YAML file contains the show's metadata (title,
description, schedule, etc..). The image archive is is made available as a zip file, or tgz file of all the images each show references.
Download the one you wish to work with.
</p>
<p>
Image file archives are not generated unless an image location URL is provided.
If an image location URL is supplied, this url will be prefixed to the file name in the YAML file against each URL. Otherwise,
no images will be exported, and the resulting YAML file will not contain any image references. The assumption is made, that
if this data is to be re-imported, all images will be found at the one base URL provided.
</p>
