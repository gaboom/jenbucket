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

// The location of your jenkins deployment server. 
$jenkins_url = 'http://localhost:8080/jenkins';

// Jenkins connection token.
$jenkins_token = '';

// Validation function
function validate($param) {
  return !empty($param) && strlen($param) <= 128 && !preg_match('/[^a-zA-Z.-]/', $param);
}


// Initialization
//

// Get incoming.
$incoming = json_decode($_POST["payload"]);

// Validate build.
if (!validate($_GET['project'])) {
  print 'No build found';
  exit();
}
$project = $_GET['project'];

// Detect branches to build
$branches = array();

foreach ($incoming->commits as $commit) {
  if (validate($commit->branch)) {
    $branches[] = $commit->branch;
  }
  if (isset($commit->branches)) {
    foreach ($commit->branches as $branch) {
      if (validate($branch)) {
        $branches[] = $branch;
      }
    }
  }
}


// Start build(s)
//

foreach ($branches as $branch) {
  // Construct a request to jenkins.
  $url = "$jenkins_url/job/$project/buildWithParameters?GIT_BRANCH=$branch";
  if (!empty($jenkins_token)) {
    $url .= "&token=$jenkins_token";
  }

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, array());
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  echo "---- BUILD $url<br/>\n";
  $response = curl_exec($ch);
  curl_close($ch);
}
