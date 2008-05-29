=== Yet Another Photoblog ===

Contributors: jaroat
Donate link: http://johannes.jarolim.com/yapb/donate
Tags: photoblog, photo blog, photo blogging, images, yapb, yet another photoblog
Requires at least: 2.5
Tested up to: 2.5
Stable tag: 1.8.2

Convert your WordPress 2.5 installation into a full featured photoblog in virtually no time.





== Description ==

Convert your WordPress 2.5 installation into a full featured photoblog in virtually no time. Use the full range of WordPress functions and plugins: Benefit from the big community WordPress has to offer.

= What is YAPB / What can you expect? =

* A non invasive WordPress-plugin that converts wp into a easy useable photoblog system 
* Easy image upload - All wordpress post-features can be used 
* On the fly thumbnail generation - Use multiple thumbnail sizes where and when you need them: Thumbnail generation gets controlled from the template. 
* EXIF data processing and output 
* Self-learning EXIF filter - Your own cameras tags can be selected to be viewed. 
* Full i18n-Support through gnutext mo/po files 
* Ping additional update-service-sites when posting a photoblog entry. 
* Nearly every WP-theme can become a photoblog in virtually no time.
* Out of the box configurable "latest images" sidebar widget
* You'll get a photoblog system based on wordpress - Decide if you want to post a normal Wordpress article or a photoblog entry. Be free to use all available extensions / plugins of the WordPress platform ;-)
* Be the owner of your own photos on your own webhost

= YAPB is a photoblog plugin =

One post, one image, one description. Your image should be worth that. If you need to display multiple images in one post - Just download and use one of the several available gallery plugins for WordPress.

= More Information =

For more information see [http://johannes.jarolim.com/yapb](http://johannes.jarolim.com/yapb "The plugins homepage").





== Installation ==

The installation consists of three parts: Upload, setting a directory permission (on unix/linux systems) and activation of the plugin.

= Upload the files =

1. Unzip the content of the zip-file into an empty directory 
2. On your server, create a directory "yet-another-photoblog" below your "wp-content/plugins" folder
2. Upload the unzipped files directly into the new folder

= Set a directory permission =

1. The used open source thumbnailing engine phpThumb needs one directory access for storing its cache files. Set the permission of the directory "wp-content/plugins/yet-another-photoblog/cache" to 777. 

= Activate the plugin =

1. Go to "admin panel/plugins" and activate YAPB 

= Don't forget to set a backlink =

Yes - This plugin was released under the GPL and you’re nearly free to do all you want to do.
But i'd be pleased very much to see some backlinks to the plugins homepage in your footer or about page:

http://johannes.jarolim.com/yapb

= Enjoy and share your photography = 

Really: do and share some serious photography so everybody may discover your view and interpretation of the world.





== Frequently Asked Questions ==

Have a look at [http://johannes.jarolim.com/yapb](http://johannes.jarolim.com/yapb "The plugins homepage").





== Screenshots ==

1. YAPB integrates thightly into wordpress
2. You get extra functionality on your new post and edit post mask
3. Seamless integration in other areas of your admin panel
4. The quick info on your dashboard gives you a rough overview
5. The detailed info/options page gives you *alot* possibilities
6. Use the automattic image insertion feature or adapt your theme manually to show your images
7. Easely display EXIF data of your images
8. Out of the box configurable "latest images" sidebar widget





== Changelog ==

= 2008-04-29, Release 1.8.2 =

* Temporary phpExifRW Bug Workaround (Divizion by zero in line 857 in exifReader.inc)
* Deprecated prototype.js calls replaced with according jQuery code

= 2008-04-17, Release 1.8.1 =

Enhanced configurability

* More thumbnail flexibility in feeds: Define width and/or height, decide if you want the thumbnails to be cropped if you defined both.
* Small brushups at the configuration page

= 2008-03-31, Release 1.8 =

WordPress 2.5 Backend Integration Release

* Updated dashboard integration
* Updated upload form integration
* Updated options page

TODO's

* Migration prototype.js to jquery
* More tightly integration into wordpress

= 2008-03-21, Release 1.7.4 =

Bugfix:

* Thumbnails didn't get deleted on image replacement or deletion since release 1.7 (SEO thumbnail naming scheme change). Thanks to yeungda for providing a patch code snippet.

Due to major changes in WordPress 2.5, YAPB 1.7.4 is the last working release for WordPress 2.3.x

= 2008-02-07 =

Bugfix:

* Template functions yapb_thumbnail and yapb_get_thumbnail didn't use the class parameter (Thanks from Salzburg to Jorge Otero)

WordPress 2.3.3 release

* YAPB now tested up to WordPress 2.3.3

= 2008-01-09 =

Small Bugfix:

* Call to YapbImage->transform in unused code branch causes warnings (thanks to Sean): Codebranch commented out
* New global: YAPB_PLUGINDIR

= 2008-01-01 =

Multiple brushups and additions

* Heavy weight logging library (log4php) replaced by lightweight internal logging infrastructure thus hopefully minimizing memory footprint and disk usage of the plugin
* "YAPB Latest Images" sidebar widget (activated on presentation/widgets and administered via the general YAPB options page)
* SEO image names: thumbnails get prepended with the original image filename
* YAPB now tested up to WordPress 2.3.2

= 2007-11-21 = 

General infrastructural brushup

* Calculate plugins base path automatically so it may be installed to any direct subdirectory below wp-content/plugins
* Plugin information centralized in the readme.txt file so i have only to change 1 instead of 4 files for a release. 
* YAPB now reads needed information directly out of the readme.txt
* YapbDiagnostics output enhanced
* Update to [phpThumb 1.7.8](http://phpthumb.sourceforge.net "The libraries homepage").
* Disabled [phpExifRW](http://open.vinayras.com/phpexifrw_exif_reader_writer "The libraries homepage") thumbnail caching
* Reviewed and hardened the plugin activation call & hook

= 2007-11-16 = 

Some minor infrastructural changes

* Yapb Class and Instancing separated into two files

= 2007-10-02 = 

First WordPress 2.3 Release:

* THANKS DAVE: Adaption of the _options_categories_array method

= 2007-09-16 = 

Template functions:

* Change of yapb_thumbnail and yapb_get_thumbnail call
* Additional yapb_image and yapb_get_image functions

= 2007-08-06 = 

Multiple changes

* New LoggerAppenderCache for log4php so the YapbThumbnailer Script may return available errors again if called directly
* Bugfix in YapbDiagnostics: Check for is_executable not required on windows systems
* Additional global YAPB_EXECUTING_OS
* YapbDiagnostics: Additional Output of YAPB version

= 2007-06-27 = 

Thumbnails in feeds are now surrounded by a link to the post - Thanks to fsimo for the idea

= 2007-06-25 = 

Bugfix

* "Thumbnail generation on every request"

= 2007-06-22 = 

Added Log4PHP library for enhanced logging

= 2007-05-25 = 

Quick Info Display on Dashboard

= 2007-05-25 = 

Brushed up admin panel options page

= 2007-05-25 = 

First Bunch of Template Functions:

* yapb_is_photoblog_post
* yapb_get_thumbnail
* yapb_thumbnail
* yapb_get_exif
* yapb_exif
* yapb_get_alternative_image_formats
* yapb_alternative_image_formats

= 2007-05-23 = 

Update of YapbDiagnostics to perform some automatic testing

* Plugin Version and WordPress compatibility Testing

= 2007-04-11 = 

Update from phpThumb 1.7.6 to 1.7.7

* Wild hack in YapbThumbnailer.php for hosts not having a correct DOCUMENT_ROOT setting. Greets to oxoxo.

= 2007-04-11 = 

Extended YapbThumbnailer Debug Code:

* Output of phpThumb debug messages if thumbnail generation failed

= 2007-02-22 = 

Exact adjustment of all automatic insertion features in conjunction with the xhtml feature (theme and rss)

* width and height in rss and atom feed inclusions

= 2007-02-22 = 

Additional readme file in cache dir so WinZip will extract this directory too - Thanks to GREGK for that tip.

= 2007-02-20 = 

General code refactorings

* Semantical upgrade of comments
* Minor code refactorings
* Reinclusion and update of XHTML-Option (img tag now closed)
* little interface brushup (background-gif for upload form)

= 2007-02-08 = 

WP 2.1 Infrastructure adaption: 

* Inclusion of general js libraries on YAPB options page over WordPress wp-includes/script-loader.php

= 2007-02-08 = 

Additional feature

* Allow YAPB Image Upload for WordPress pages
* Additional code and options for the automatic image insertion on pages
* Change of plugin description
* Change of version number (forgot that last time)

= 2007-01-25 = 

First set of adaptions to make YAPB WP 2.1 compatible

* No use of $table_prefix anymore
* No double integration of prototype.js anymore 

Goals for the next time: 

* No use of deprecated WordPress infrastructure
* Better integration through use of new WP infrastructure

= 2007-01-25 = 

Bugfix

* GMT offset and delay before seeing post solved

= 2007-01-22 = 

Possible Bugfix

* YapbImage::getInstanceFromDb now returns null instead of error if no $post->ID was provided.

= 2007-01-18 = 

Additional feature

* Original image dimensions now available over YapbImage class (width and height attributes)

= 2007-01-18 = 

Additional feature 

* thumbnail dimensions now available over YapbImage class (Even if thumb wasn't generated yet)

= 2007-01-06 = 

I'm to stupid to fix a bug sheme at the first time:

* Bugfix: "Division by zero" Error on YAPB-Options-Page Section Statistics if no images where uploaded yet, but a file exists in thumbnails dir 

Thanks to torontobroad

= 2007-01-05 = 

Bugfix: 

* "Division by zero" Error on YAPB-Options-Page Section Statistics if no images where uploaded yet.

Thanks to Martin Ciastko and torontobroad

= 2006-10-31 = 

Bugfix

* "Division by zero" Error on YAPB-Options-Page Section Statistics if no images where uploaded yet.

= 2006-10-28 = 

Bugfix

* If defining a phpThumb single-usage parameter multiple times in method getThumbnailHref YAPB couldn't locate the according cachefile - Though generating it on every access.

= 2006-10-27 = 

Back to BETA: 1.2: Major infrastructure adaption for better performance

* Thighter phpThumb integration 
* Direct thumbnail creation and URL rendering
* Manual cache management
* Maintainance and Information part on Yapb-Options-Page
* Upgrade to phpThumb 1.7.4

= 2006-10-17 = 

Release Candidate 1

* Completed GnuText usage in sourcecode
* Activated GnuText usage
* Added a german language file
* Added image tag inline css input fields on options page
* Included some YAPB buttons

= 2006-10-09 = 

Added feature

* Control over rss2 and atom feed thumbnail embedding.

= 2006-10-02 =

Turned off EXIF thumbnail caching behaviour in ExifUtils usage of PHPExifRW so there's no need for the .cache_thumbs directory anymore

= 2006-10-02 =

Enhancement of XHTML BugFix

* No ampersand replacement in rss2 feed (Wordpress places a CDATA Block around content blocks so that's not needed)

= 2006-09-20 = 

XHTML BugFix in YapbImage.class.php

* Changed the Thumbnail URL generation to be XHTML compliant

Thanks for the tip to yovko at yovko dot net

= 2006-09-13 = 

Workaround in YapbImageFile and Yapb Class files 

* Method delivering correct system path of image file depended on CGI var "DOCUMENT_ROOT" - This may report wrong values in multi-hosting-enviroments. The wp-installation root get's calculated now on YAPB-Startup and is defined as three directories above .../wp-content/plugins/yet-another-photoblog/Yapb.class.php

= 2006-09-12 = 

Changed creation of YAPB_PLUGIN_PATH to use wp_option "siteurl" instead of "home"

= 2006-09-07 = 

JavaScript workaround in edit_form_advanced_javascript_injection.tpl.php, File upload didn't work in Safari

= 2006-09-06 = 

Inserted "Automatic Template Insertion" for newbies and Added a bunch of related options to make it a little bit more flexible

= 2006-08-29 =

Extended feeds (rss, rss2, atom): Every yapb-xml-item now contains an image-tag refering to a thumbnail of the image.

= 2006-08-29 =

Extended options page offers a set of phpThumb options now, JS-DBX-Folders included for better structure and usability

= 2006-08-29 =

Added flexible options engine - Options are stored in Yapb.class.php now

= 2006-08-27 =

JavaScript workaround in edit_form_advanced_javascript_injection.tpl.php

* File upload didn't work in IE:

> Node.enctype = 'multipart/form-data' just works in standard compatible browsers
> Node.encoding = 'multipart/form-data' works in IE too

= 2006-08-26 =

Little rearrangements on the options panel; Added option yapb_default_post_category_activate

= 2006-08-26 =

Added update services pinging if posting photoblog-entry

= 2006-08-26 =

Corrected bug in edit_publish_save_post():

* Extracting the needed URI of an uploaded image failed if the wp siteurl option didn't end with a slash.

= 2006-08-24 = 

Beta release

= 2006-05-27 = 

Alpha release

