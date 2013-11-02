Jenbucket
=========

Allows per-branch deployment from bitbucket to a jenkins server.

Receives an incoming POST service request from Bitbucket (https://bitbucket.org)
and connects this through to a jenkins build.

1 Install this script on a php webserver somewhere where bitbucket can access it (e.g. mydomain.com/jenbucket/index.php)
3 Point bitbucket to this script instead of using the jenkins plugin.
This is done by doing a post commit hook action (type POST).
Set the following url as your POST url: http://mydomain.com/jenbucket/index.php?project=JENKINS_PROJECTNAME
4 setup the correct settings
- $jenkins_url
- $jenkins_token inside the index.php file.
5 You're up and running!
