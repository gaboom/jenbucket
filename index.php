<?php

/**
 * @file
 * jenbucket main file.
 *
 * Receives an incoming POST service request from Bitbucket (https://bitbucket.org)
 * and connects this through to a jenkins build.
 *
 * TODO:
 * - input validation.
 */

// Settings.
//
// The location of your jenkins deployment server. Something like
// http:/blabla.be/job/.
$jenkins_url = '';

// Jenkins connection token.
$jenkins_token = '';

// Get incoming.
$incoming = json_decode($_POST['payload']);

// Validate build.
if (empty($_GET['project_to_build'])) {
  print 'No build found';
  exit();
}
$build_name = $_GET['project_to_build'];

// Detect if we're allowed to build.
$build_allow = FALSE;

if (empty($_GET['branch'])) {
  // No branch default means build always.
  $build_allow = TRUE;
}
else {
  foreach ($incoming->commits as $commit) {
    if ($commit->branch == $_GET['branch']) {
      $build_allow = TRUE;
      break;
    }
  }
}

// Only build if we're allowed to build.
if ($build_allow) {
  // Construct a request to jenkins.
  $url = $jenkins_url . '' . $_GET['project_to_build'] . '/build?token=' . $jenkins_token;

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $response = curl_exec($ch);
  curl_close($ch);
}
