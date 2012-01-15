# About Aurora WebUI GPL

Although starting off with modifying Aurora WebUI to run under php5, it became clear that starting a new project using the backPress library for the front-end was going to be better in the long-term.

## www-examples

www-examples is not for public consumption. It's intended as a developer-only "live" reference to test API calls. DO NOT PLACE ON A PUBLIC WEB SERVER.

## utils

minify.sh uses the YUI compressor which is available from http://yuilibrary.com/download/yuicompressor/

## Contributors
SignpostMarv

# Installation

## Apache

This example uses "D:/github/Aurora-WebUI-GPL/" as the location of the repository.

### Installing in root directory of a domain

1. Edit the virtual hosts configuration to point a website's DocumentRoot at the D:/github/Aurora-WebUI-GPL/www directory *(see below for example)*.
2. Copy config.example.php to config.php _do not move to www directory_
3. Change the webui URL and password in D:/github/Aurora-WebUI-GPL/config.php to match the port number and password in the aurora ini files

#### httpd-vhosts.conf

	<VirtualHost *:80>
		ServerAdmin aurorawebuigpl@localhost
		DocumentRoot "D:/github/Aurora-WebUI-GPL/www"
		ServerName aurorawebuigpl.localhost
		ErrorLog "logs/aurorawebuigpl.localhost-error.log"
		CustomLog "logs/aurorawebuigpl.localhost-access.log" common

		<Directory "D:/github/Aurora-WebUI-GPL/www">
			Options Indexes FollowSymLinks
			AllowOverride All
			Order allow,deny
			Allow from all
		</Directory>
	</VirtualHost>