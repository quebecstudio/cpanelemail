# cPanel Email plugin for Craft CMS (beta)

Email management using cPanel API

## Installation

To install cPanel Email, follow these steps:

1. Download & unzip the file and place the `cpanelemail` directory into your `craft/plugins` directory
2.  -OR- do a `git clone https://github.com/quebecstudio/cpanelemail.git` directly into your `craft/plugins` folder.  You can then update it with `git pull`
3.  -OR- install with Composer via `composer require quebecstudio/cpanelemail`
4. Install plugin in the Craft Control Panel under Settings > Plugins
5. The plugin folder should be named `cpanelemail` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

cPanel Email works on Craft 2.4.x and Craft 2.5.x.

## cPanel Email Overview

Manage your email accounts, forwarders and autoresponders right from Craft CP!

## Config

To configure this plugin, you will need:

* Your cPanel hostname or IP
* Your cPanel API port number
* Your cPanel account
* Your cPanel username
* Your cPanel password


## cPanel Email Roadmap

* Add permissions for each CRUD action.
* Complete french translation
* API connection test button
* Prevent to use some domains/subdomains for email account

Plugin development by [Québec Studio](http://quebecstudio.com)

Icon designed by [Madebyoliver](http://www.flaticon.com/authors/madebyoliver) from [Flaticon](http://www.flaticon.com/)
