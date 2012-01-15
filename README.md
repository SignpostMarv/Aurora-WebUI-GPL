# About Aurora WebUI GPL

Although starting off with modifying Aurora WebUI to run under php5, it became clear that starting a new project using the backPress library for the front-end was going to be better in the long-term.

## www

Several pages in the website will return "404 Not Found" errors when there is no content to be displayed, such as groups and news.

## www-examples

www-examples is not for public consumption. It's intended as a developer-only "live" reference to test API calls. DO NOT PLACE ON A PUBLIC WEB SERVER.

## utils

minify.sh uses the YUI compressor which is available from http://yuilibrary.com/download/yuicompressor/

## Contributors
SignpostMarv

# Installation

This example uses "D:/github/Aurora-WebUI-GPL/" as the location of the repository.

## Template CSS


### Manual method
1. Create D:/github/Aurora-WebUI-GPL/www/css/templates/default/style.css
2. Copy and paste the contents of D:/github/Aurora-WebUI-GPL/css/reset.css into D:/github/Aurora-WebUI-GPL/www/css/templates/default/style.css
	* The contents of reset.css *must* stay at the top
3. Copy and paste the contents of all CSS files in D:/github/Aurora-WebUI-GPL/css/templates/default/ into D:/github/Aurora-WebUI-GPL/www/css/templates/default/style.css

### minify.sh
The minify.sh script in the utils directory attempts to "minify" and pre-gzip the template styles with maximum compression in order to help save bandwidth.

1. Download [http://yuilibrary.com/download/yuicompressor/](YUI Compressor version 2.4.2)
2. Extract the .jar file to D:/github/yuicompressor-2.4.2.jar
3. Run minify.sh
	* If running on windows, [http://code.google.com/p/msysgit/downloads/list](install Git Bash) and run the script through there. It has not been tested on cygwin yet.

## Apache

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