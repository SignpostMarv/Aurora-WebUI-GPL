# About Aurora WebUI GPL

Although starting off with modifying Aurora WebUI to run under php5, it became clear that starting a new project using the backPress library for the front-end was going to be better in the long-term.

## www

Several pages in the website will return "404 Not Found" errors when there is no content to be displayed, such as groups and news.

## www-examples

www-examples is not for public consumption. It's intended as a developer-only "live" reference to test API calls. DO NOT PLACE ON A PUBLIC WEB SERVER.

## utils

minify.sh uses the [YUI compressor](http://yuilibrary.com/download/yuicompressor/)

## Contributors
SignpostMarv

# Installation

This example uses "D:/github/Aurora-WebUI-GPL/" as the location of the repository.

## Aurora-Sim Addon

Aurora-WebUI-GPL currently supports [Aurora WebAPI v2.0 rc6](https://github.com/aurora-sim/aurora-webapi/tree/v2.0-rc6). This module will need to be present and enabled on your [Aurora-Sim](https://github.com/aurora-sim/aurora-sim/) installation.

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

# Configuration

## config.php

### Default Timezone

PHP can complain if no timezone is set and WebUI-GPL will use your system timezone by default.

A [http://www.php.net/manual/en/timezones.php](full list of timezones is available on the PHP website).

```php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
```

### Default Content-Type header

The template system sets appropriate Content-Type for different pages, but we set the default content type to text/plain to make debugging things a little easier when errors occur outside the scope of the template system. Feel free to change it!

```php
	header('Content-Type: text/plain');
```

### WebUI Configs array

WebUI GPL configs need three arguments:
1. The API end-point url, using the Port number specified in [https://github.com/aurora-sim/Aurora-WebAPI/blob/master/WebAPI/WebAPI.ini](WebAPI.ini)
2. A username that has API access.
3. The API access token from the [https://github.com/aurora-sim/aurora-webapi/tree/master](WebAPI module)

An instance of the PHP class that provides an interface to the [https://github.com/SignpostMarv/mapapi.cs](mapapi.cs) Aurora Module can be attached to an instance of the WebUI interface. The example commented-out example in the config.php file attaches the map API to the last created WebUI instance.

```php
	$configs[] = WebUI::r(
		'http://localhost:8007/webapi',
		'Username'
		'AccessToken'
	);
//	$configs[$configs->count() - 1]->attachAPI(MapAPI::r(
//		'http://localhost:8007/mapapi'
//	));
```

### base URI

The template system uses absolute paths, so a URI needs to be provided for the HTML base element.

#### Installing in root directory of a domain

```php
	Globals::i()->baseURI = 'http://localhost/';
```

#### Installing in sub directory of a domain

```php
	Globals::i()->baseURI = 'http://localhost/webui';
```

### Link Style

WebUI-GPL currently supports two modes for links- "query" and "mod_rewrite". The Aurora::Addon::WebUI::Template::link() method accepts mod_rewrite-mode style links, automatically converting them to query mode style links if needed.

#### query

This mode should be used if you don't have mod_rewrite available (which is why it's the default).

```php
	Globals::i()->linkStyle = 'query';
```

#### mod_rewrite

The www/.httaccess.example file should be copied to www/.htaccess if mod_rewrite mode is to be used.

```php
	Globals::i()->linkStyle = 'mod_rewrite';
```

### Registration

#### Postal Information

This parameter should be set to TRUE if you need to store a user's postal information.

```php
	Globals::i()->registrationPostalRequired = false; // TRUE if postal address info is required for registration, FALSE otherwise.
```

#### Account Activation

If your grid is public, you will most likely want to set this to true.

However, WebUI-GPL currently does not support sending the activation link via email or other channels, so the link will get dumped straight to the web page.

```php
	Globals::i()->registrationActivationRequired = false; // TRUE if activation is required for registration, FALSE otherwise. NOTE: we're not specifying activation method here for a reason.
```

#### Email Address

Currently serves no purpose, but will be used for password resets and activation links when support is added.

```php
	Globals::i()->registrationEmailRequired = false; // TRUE if emails are required, FALSE if they're optional.
```

### reCAPTCHA

If you want to use the reCAPTCHA service on the registration page, you will need to uncomment and modify these 3 properties.
You will also need to [https://www.google.com/recaptcha/admin/create](obtain your public and private keys).


```php
	Globals::i()->recaptcha                 = true;
	Globals::i()->recaptchaPublicKey        = 'foo' ;
	Globals::i()->recaptchaPrivateKey       = 'bar' ;
```

#### reCAPTCHA JavaScript

To give you fine-grained control over what JavaScript runs on your website, WebUI-GPL will disable reCAPTCHA's scripts by default.
If you want to use the more user-friendly, JavaScript-powered reCAPTCHA interface, you'll need to set this property to TRUE.

```php
	Globals::i()->recaptchaEnableJavaScript = false ; // set to TRUE to enable the prettier but JavaScript-powered reCAPTCHA input
```