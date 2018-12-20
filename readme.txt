This is a development version, currently not fully useable yet. Work in progress. Expected release in january.

Known issues:
Category page not yet modified, so CEON URI mapping field won't show when editing or creating a category

For any other issues you may find please create issues

CEON URI Mapping 4.6.0 for Zen Cart 1.5.6

Changelog 4.6.0:
2018-12-20
webchills

Changed files updated to reflect changes in Zen Cart 1.5.6 
Code changes for PHP 7.3 and 7.2 compatibility

This version is for Zen Cart 1.5.6 only!
If you are using Zen Cart 1.5.5 use version 4.5.5!

Changelog 4.5.5:
2018-06-17
webchills

changed wrong code in YOURADMIN/includes/classes/class.CeonURIMappingInstallOrUpgrade.php
see
https://www.zen-cart.com/showthread.php?184548-Ceon-URI-Mapping-v4-x&p=1346786#post1346786

Changelog 4.5.4:
2018-05-31
webchills

removed unsuitable code sections in:
YOURADMIN/ezpages.php
YOURADMIN/includes/modules/product/collect_info.php

Changelog 4.5.3:
2018-04-06
webchills (www.webchills.com)

Code rewritten for PHP 7 and 7.1 compatibility
Obsolete tell a friend functions removed
Changed files updated to reflect changes in Zen Cart 1.5.5f
Removed support for Zen Cart version older than 1.5.5

This version is for Zen Cart 1.5.5 only!

____________________________________________________________________________________________________________________

BEFORE YOU BEGIN

____________________________________________________________________________________________________________________

This plugin is changing many very important core files.
This is not the kind of plugin that you can easily upload in 2 minutes.

To install this module, you should have the following tools ready:

1) A good text editor.
This does not mean the Notepad included in Windows or even Microsoft Word.
You need a text editor that understands utf-8 and can also save in utf-8 format without BOM.
Recommendation:
UltraEdit (free 30-day trial available)
Also well suited is the free text editor Notepad ++

2) A tool for comparing files
Installing this module requires that you compare the contents of some of your existing Zen Cart files with the contents of the new module files and merge the changes.
Recommendation: 
BeyondCompare (free 30-day trial available)
Also well suited is the free program WinMerge

____________________________________________________________________________________________________________________

INSTALLATION/UPGRADE

____________________________________________________________________________________________________________________

UPDATE from version 4.5.1

If you already using version 4.5.1 in Zen Cart 1.5.5 -  which was not compatible with PHP 7 - and want to update:

First delete the ceon_uri_mapping_configs table

To do this, you can use the following command in phpMyAdmin or under Tools> Install SQL Patches

DROP TABLE IF EXISTS ceon_uri_mapping_configs

Important: Delete the following file from the server:

includes/classes/class.String.php

Then proceed as described under NEW INSTALLATION.

All your already defined URIs are preserved!

____________________________________________________________________________________________________________________

NEW INSTALLATION

First install this module in a test system and configure / test it there and finally adapt it to your own wishes. 
Only then use in a live shop! 
Be sure to back up all files of your shop via FTP and save the database with phpMyAdmin or other suitable tools!

IMPORTANT
Before installing these changes:
BACKUP of store files and database!
No liability, use at your own risk!
Made BACKUP? Ok, then read on ...


The installation is done in the following steps. Follow this procedure!

1)
Open the install.sql in the SQL folder with a text editor and copy the content.
Copy the content into the field in the Zen Cart Administration in the menu Tools> Install SQL Patches and submit it.
This creates 3 new database tables

2)
In the folder MODIFIED CORE FILES rename the folder YOURADMIN to the name of the admin directory.
If you have just recently installed Zen Cart 1.5.6 and have not made any changes to the files yet, you can now upload all the files / folders from the MODIFIED CORE FILES folder in the default structure to your Zen Cart installation. 
This will overwrite many files.
If you've been using Zen Cart 1.5.6 for some time, and you've ever made changes to files or built-in other modules, then do not upload the files.
Compare all the files in the MODIFIED CORE FILES folder with the corresponding files in your store and make the changes manually via WinMerge or BeyondCompare.
Then you opload the changed files in the structure shown.

3)
In the folder NEW FILES rename the folder YOURADMIN to the name of the admin directory.
Then upload in the given structure, no existing files will be overwritten

4)
Log in to Zen Cart Administration and click on any menu item.
You should now have the new menu item CEON URI Mapping (SEO) Configuration under Modules.
Click on it, you should see success messages that the module has been successfully installed

5)
Go to Installation Check
Below is the content for the required .htaccess file
Create a .htaccess with the content displayed
Upload this .htaccess to the shop directory.

Under URI, set Auto-generation Settings as you like it


6)
Under Configuration a new menu item CEON URI Mapping (SEO) is now available, with which the rewriting of the URLs can be switched on and off


IMPORTANT:
The URLs are not automatically rewritten.
If the store already contains categories, then you have to edit EVERY category and assign a desired URL below in URI Mapping.
Or simply leave the automatic assignment of a URL checked (tick this box to have the URIs auto-generated for this category).
If the store already contains articles, then you have to edit EVERY article and assign a desired URL below in URI Mapping.
Or simply leave the automatic assignment of a URL checked (tick this box to have the URIs auto-generated for this product).
If the store already contains EZ Pages, then you have to edit EACH EZ Page and assign a desired URL below in URI Mapping.
Or simply leave the automatic assignment of a URL checked (tick this box to have the URIs auto-generated for this EZ-Page).
If the store already contains manufacturers, then you have to edit EVERY manufacturer and assign a desired URL below in URI mapping.
Or simply leave the automatic assignment of a URL checked (tick this box to have the URIs auto-generated for this manufacturer).

It is possible to rewrite Define Pages as well (for example index.php?main_page= privacy), but there is no admin option for this.
The desired links for Define Pages can be set directly in the database (see the documentation in the _docs folder)

Full configuration instructions can be found in the _docs/ directory.
The license file can be found in the main folder alongside this notice.
