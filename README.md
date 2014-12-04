Jenbucket
=========

It is a bit improved version of original Jenbucket with additional:
  + passing through trigger cause
  + some more input validation
  + triggering for all branches available in post commit hook data
  + logging stuff

Allows per-branch deployment from bitbucket to a jenkins server.

Receives an incoming POST service request from Bitbucket (https://bitbucket.org)
and connects this through to a jenkins build.

#### Setup
1. Put this script on a PHP webserver somewhere where bitbucket can access it (e.g. mydomain.com/jenbucket/index.php)

3. Setup the correct settings in connector script
	+ $jenkins_url
	+ $jenkins_token
  + $logging_enabled = True/False
  + $log_file = "/path/to/log/file" - needto be existing file with valid write permissions

2. Point bitbucket to this script instead of using the jenkins plugin.

    This is done by doing a post commit hook action (type POST). Set the following url as your POST url: http://mydomain.com/jenbucket/index.php?project=JENKINS_PROJECTNAME

4. You're up and running! Enjoy!
