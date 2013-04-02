Jenbucket
=========

Allows per-branch deployment from bitbucket to a jenkins server.

Receives an incoming POST service request from Bitbucket (https://bitbucket.org)
and connects this through to a jenkins build.

1 Install a php webserver somewhere where bitbucket can access it (e.g. mydomain.com/index.php)
2 Install this script
3 Point bitbucket to this script instead of using the jenkins plugin.
This is done by doing a post commit action (type POST).
Set the following url as your POST url: http://mydomain.com/index.php?project_to_build=JENKINS_PROJECTNAME&branch=JENKINS_BRANCH
4 setup the correct settings
- $jenkins_url
- $jenkins_token inside the index.php file.
5 You're up and running!


TODO:
- input validation.
