Jenbucket
=========

Allows per-branch deployment from bitbucket to a jenkins server.

Gets an incoming POST service request from Bitbucket (https://bitbucket.org)
and connects this trough to a jenkins build.

1 Install a php webserver somewhere were bitbucket can access it (e.g. mydomain.com/index.php)
2 Install this script
3 Point bitbucket to this script in stead of using the jenkins plugin.
This is done by doing a post commit action (type POST).
Put in the following url in your POST url: http://mydomain.com/index.php?project_to_build=JENKINS_PROJECTNAME&branch=JENKINS_BRANCH
4 setup the correct settings
- $jenkins_url
- $jenkins_token inside the index.php file.
5 You're up and running!


TODO:
- input validation.
