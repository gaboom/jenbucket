<?php

  /**
   * @file
   * jenbucket main file.
   *
   * Receives an incoming POST service request from Bitbucket (https://bitbucket.org)
   * and connects this through to a jenkins build.
   *
   */

  // Settings
  //

  // Logging
  $logging_enabled = True;
  $log_file = "/var/log/apache2/jenkins_connector.log";

  // The location of your jenkins deployment server
  $jenkins_url = 'http://username:password@my_jenkins.url:8080';

  // Jenkins connection token. If empty will not be used.
  $jenkins_token = '';

  // Validation function
  function validate($param) {
    return !empty($param) && strlen($param) <= 128 && !preg_match('/[^a-zA-Z.-_]/', $param);
  }
  // Log function
  function print_in_log($string) {
    if($logging_enabled) {
      error_log("$string\n", 3, $log_file);
    }
  }

  // Initialization
  //

  // Get incoming.
  $incoming = json_decode($_POST["payload"]);

  // Validate build.
  $project = $_GET['project'];
  if (!validate($_GET['project'])) {
      print 'No build found';
      print_in_log(" No build found... quiting");
    exit();
  }
  $project = $_GET['project'];
  print_in_log("Request to build: $project");

  // repository URL:
  if ((isset($incoming->canon_url)) && (isset($incoming->repository))) {
    $repo_url = $incoming->canon_url . $incoming->repository->absolute_url;
  } else {
    $repo_url = '';
  }

  // Detect branches to build
  $branches = array();

  if(!isset($incoming->commits)) {
    print 'No commits found';
    print_in_log(" No commits found... quiting");
    exit();
  }
  foreach ($incoming->commits as $commit) {
    if (validate($commit->branch)) {
      $revision = ($commit->node ?: '');
      $commit_message = (isset($commit->message) ? preg_replace('/\s+/', ' ', $commit->message) : '');
      $author = ($commit->author ?: '');
      $branches[$commit->branch] = "Triggered by push of revision $revision: \"$commit_message\" to $repo_url by $author";
    }
    if (isset($commit->branches)) {
      foreach ($commit->branches as $branch) {
        if (validate($branch)) {
          $revision = ($commit->node ?: '');
          $commit_message = (isset($commit->message) ? preg_replace('/\s+/', ' ', $commit->message) : '');
          $author = ($commit->author ?: '');
          $branches[$branch] = "Triggered by push of revision $revision: \"$commit_message\" to $repo_url by $author";
        }
      }
    }
  }


  // Start build(s)
  //

  foreach ($branches as $branch=>$cause) {
    print_in_log(" - found branch: $branch");
    // Construct a request to jenkins.
    $url = "$jenkins_url/job/$project/buildWithParameters?GIT_BRANCH=$branch";
    if (!empty($cause)) {
      $url .= "&cause=".urlencode($cause);
    }
    if (!empty($jenkins_token)) {
      $url .= "&token=$jenkins_token";
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array());
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    print_in_log("---- BUILD $url");
    // echo "---- BUILD $url<br/>\n";
    echo "OK\n";
    $response = curl_exec($ch);
    curl_close($ch);
  }
?>