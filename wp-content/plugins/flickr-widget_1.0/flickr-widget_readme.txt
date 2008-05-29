
Flickr Badge Widget Plugin for Wordpress
By Ben Coleman
Updated: 26-02-2007

This is plugin for the Widget Sidebar in Wordpress. It will display your flickr photos in your blog's sidebar.
It's basically a wrapper round the badge functionality that flickr provide and publish.

Requirements
------------
Wordpress 2.x or higher - http://wordpress.org/
Sidebar Widgets Plugin  - http://automattic.com/code/widgets/

Installation
------------
Extract the flickr-widget.php file into your wp-content/plugins/widgets/ directory
Goto your wordpress admin pages and click on 'Plugins' activate the plugin called "Flickr Badge Widget"
Important. Ensure a picasacache sub-directory exists in the widgets directory and is writable by the webserver

Configuration
-------------
Goto your wordpress admin and click on 'Presentation' then 'Sidebar Widgets'
Drag the widget called "Flickr Badge" to your sidebar.
Click the square icon next to the widget to open the config screen.

The following options are available

Widget Title:	The title that will appear in the sidebar, optional
Source: 			Where to pick the photos from; can be a specific user, a group pool or all of flickr (everyone)
					Most people will select the "User" option to show their own photos
Flickr ID: 		If showing photos from a user or group enter the ID, Use http://idgettr.com/ to find your ID
					Note. This is NOT your flickr username, entering your username will not work. 
Tag: 				Use this if the selected source is (Tag) to only show photos with this tag
Photo Count: 	Number of photos to show
Layout: 			Layout orientation of the photos
Which Photos: 	How to select the photos to display; either randomly or the newest photos
Photo Size: 	Size of the photos displayed in the sidebar
Alignment: 		Alignment of the section (div) containing the photos


Good Luck & Have Fun!
Ben