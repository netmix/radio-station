<?php
/*
 * Help file for show-schedule:
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */
?>
<style type="text/css">
  .code-block{
    padding-top: 5px;
    background-color: #282C35;
    /* border: 1px solid red; */
    width: 70%;
  }
  mark.red {
    color: #B84E45;
    background: none;
  }
  mark.green{
    color: #98C379;
    background: none;
  }
  pre {
    color: #A9AFBC;
  }
</style>
<h2>show-schedule:</h2>
<p>
  Here is an example of a properly formatted show schedule block:
</p>
<div class="code-block">
  <pre>
    <mark class="red">show-schedule</mark>:
      <mark class="red">mon</mark>:
       - <mark class="green">["05:30", "06:00", "disabled", "encore"]</mark>
       - <mark class="green">["05:00", "17:30"]</mark>
      <mark class="red">wednesday</mark>:
       - <mark class="green">["05:30", "06:00"]</mark>
       - <mark class="green">["17:00", "17:30"]</mark>
      <mark class="red">Friday</mark>:
       - <mark class="green">["05:30", "06:00"]</mark>
       - <mark class="green">["17:00", "17:30"]</mark>
  </pre>
</div>
<p>
  A single <strong>show-schedule:</strong> key has an associative array of
  week-days. Each day of the week may appear once, but is supported in three forms
  as shown (three letter abreviation, full word lower case, full word with first letter capitalized).
  Each day is also a key, having an associated sequential array of time blocks.
</p>
<p>
  Each time block expresses a single start/end time pair for the show (during which time block the show
  is presumed to run). Times are 0 padded and expressed
  in 24h format. In addition to a start and end time, time block arrays may have two optional parameters as shown above,
  consisting of the keywords "disabled" and "encore", which mark it as disabled, or flag the time block as an encore of something else.
</p>
