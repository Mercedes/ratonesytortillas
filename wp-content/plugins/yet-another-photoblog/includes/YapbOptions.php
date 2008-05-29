<?php

	// We fill the options array
	$this->options = array(
		// OptionGroup(title, description)
		array(__('Yet Another Photoblog Options', 'yapb'), __('Welcome to YAPB and to it\'s numerous configuration possibilities.<br/>Don\'t panic ;-)', 'yapb'),
			// OptionSubGroups Array
			array(
				array(__('Writing Options', 'yapb'), __('These settings do alter the behaviour of the WordPress input mask for new articles.', 'yapb'),
					// Option Array
					array(
						// option_name => Option(type, text, [SELECT:optionfields,] defaultvalue)
						'yapb_check_post_date_from_exif' => array('CHECKBOX', __('Check by default: Post date from image exif data if available.', 'yapb'), true),
						'yapb_default_post_category' => array('CHECKBOX_SELECT', __('Assign post exclusivly to category # if attaching an YAPB-image.', 'yapb'), $this->_options_categories_array(), array(false, '')),
						'yapb_form_on_page_form' => array('CHECKBOX', __('Enable YAPB-Imageupload for content pages', 'yapb'), false)
					)
				),

	 		#	array(__('Image Resizing on upload','yapb'), '',
			#		array(
			#			'yapb_resize_on_upload_activate' => array('CHECKBOX', __('Enable Image Resizing on upload', 'yapb'), false),
			#			'yapb_resize_on_upload_max_dimesion' => array('INPUT', __('Maximum image side length: #5 px', 'yapb'), '1024')
			#		)
			#	),  
	
				array(__('EXIF Filtering Options', 'yapb'), '',
					array(
						'yapb_filter_exif_data' => array('CHECKBOX', __('Enable EXIF tags filtering', 'yapb'), false),
						'yapb_view_exif_tagnames' => array('CUSTOM_VIEW_EXIF_TAGNAMES', __('Show the following EXIF tags if available:<br /><i><small>(This list will be filled up after you uploaded your first image)</small></i>', 'yapb'), array())
					)
				),
				array('Update Services', '',
					array(
						'yapb_ping_sites' => array('TEXTAREA', __('YAPB notifies the following site update services if you publish a photoblog-post.<br />These services will be pinged additionally to the services defined on the options/write admin-panel.<br />Separate multiple service URIs with line breaks.', 'yapb'), '')
					)
				),
			)
		),

		array(__('Thumbnailer Library Options', 'yapb'), __('<a href="http://phpthumb.sourceforge.net/" target="_blank">phpThumb</a> is the thumbnailing library of my choice. For your comfort, i made available a selection of settings: For more Information please refer to <a href="http://phpthumb.sourceforge.net" target="_blank">http://phpthumb.sourceforge.net</a> - Especially this two pages: <a href="http://phpthumb.sourceforge.net/demo/docs/phpthumb.readme.txt" target="_blank">readme</a> and <a href="http://phpthumb.sourceforge.net/demo/docs/phpthumb.faq.txt" target="_blank">faq</a>.', 'yapb'),
			array(
				array(__('ImageMagick configuration', 'yapb'), __('If source image is larger than available memory limits AND <a href="http://www.imagemagick.org" target="_blank">ImageMagick\'s "convert" program</a> is available on your server, phpThumb() will call ImageMagick to perform the thumbnailing of the source image to bypass the memory limitation.', 'yapb'),
					array(
						'yapb_phpthumb_imagemagick_path' => array('INPUT', __('Absolute pathname to "convert": #20 Leave empty if "convert" is in the path.', 'yapb'), '')
					)
				),
				array(__('Default output configuration', 'yapb'), '',
					array(
						'yapb_phpthumb_output_format' => array('SELECT', __('Default output format: # Thumbnail will be output in this format (if available in your version of GD).', 'yapb'), array('JPG' => 'jpeg', 'PNG' => 'png', 'GIF' => 'gif'), 'JPG'),
						'yapb_phpthumb_output_interlace' => array('CHECKBOX', __('Interlaced output for GIF/PNG, progressive output for JPEG; if unchecked: non-interlaced for GIF/PNG, baseline for JPEG.', 'yapb'), false)
					)
				)
			)
		),

		array(__('Feed Options', 'yapb'), __('Here you may alter the behaviour of the automatic feed insertion.', 'yapb'),
			array(
				array('Embedding', __('YAPB may embed images/thumbnails in your RSS2 and ATOM feeds.<br/>You will have to turn on this feature if you want to subscribe to services like <a href="http://photos.vfxy.com" target="_blank">VFXY</a>.', 'yapb'),
					array(
						'yapb_display_images_xml' => array('CHECKBOX', __('<strong>Embed images in RSS2 and ATOM feeds content.</strong>', 'yapb'), true),
						'yapb_display_images_xml_inline_style' => array('INPUT', __('Inline CSS-Style for image tag: #40', 'yapb'), 'float:left;padding:0 10px 10px 0;'),
						'yapb_display_images_xml_html_before' => array('TEXTAREA', __('Custom HTML before image tag', 'yapb'), ''),
						'yapb_display_images_xml_html_after' => array('TEXTAREA', __('Custom HTML after image tag', 'yapb'), '')
					)
				),
				array('Format', __('Set the maximum width and height of the thumbnail inserted into your feed:<ul><li>If you set either width or height, the other value will be calculated based on the actual image size to preserve the image proportions.</li><li>If you set both, YAPB tries to define width and height so the entire image fits into your defined rectangle.</li><li>If you check the crop-option, YAPB crops the thumbnail (if neccessary) so it fills the rectangle entirely.</li></ul>', 'yapb'),
					array(
						'yapb_display_images_xml_thumbnail_activate' => array('CHECKBOX', __('Embed as thumbnail', 'yapb'), true),
						'yapb_display_images_xml_thumbnail' => array('INPUT', __('Maximum thumbnail width of #3 px', 'yapb'), '180'),
						'yapb_display_images_xml_thumbnail_height' => array('INPUT', __('Maximum thumbnail height of #3 px', 'yapb'), ''),
						'yapb_display_images_xml_thumbnail_crop' => array('CHECKBOX', __('Crop thumbnail to fill in the defined rectangle', 'yapb'), false)
					)
				)
			)
		),
		
		array(__('Yapb Sidebar Widget', 'yapb'), __('Happy christmas 2007 and a happy new year 2008! As a little present i included a little sidebar widget so you\'re able to present your latest images at your sidebar. To use this feature, your WordPress Theme has to support widgets.', 'yapb'),
			array(
				array(__('Widget Configuration', 'yapb'), '',
					array(
						'yapb_sidebarwidget_title' => array('INPUT', __('Widget Title: #20 Leave empty if you don\'t want to display a title.', 'yapb'), 'Latest photography'),
						'yapb_sidebarwidget_imagecount' => array('INPUT', __('Display the latest #10 images.', 'yapb'), '5'),
						'yapb_sidebarwidget_restrict' => array('SELECT', __('Restrict thumbnail size #.', 'yapb'), array('horizontally'=>'h','vertically'=>'v'), 'vertically'),
						'yapb_sidebarwidget_maxsize' => array('INPUT', __('Maximal thumbnail size: #10 px.', 'yapb')),
						'yapb_sidebarwidget_displayas' => array('SELECT', __('Display thumbnails as #.', 'yapb'), array('Bunch of linked images in a div container' => 'div','Bunch of list items in an unordered list' => 'ul'))
					)
				)
			)
		),

		array(__('Automatic Template Insertion', 'yapb'), __('Yapb does display uploaded images automatically on different sections of your site by default.<br/>That\'s just a help for first-time-users and evaluation purproses: To style your photoblog individually,<br/> turn off this option and have a look at <a target="_blank" href="http://johannes.jarolim.com/blog/wordpress/yet-another-photoblog/adapting-templates/">how to adapt themes manually</a>.', 'yapb'),
			array(
				array(__('General', 'yapb'), '',
					array(
						'yapb_display_images_activate' => array('CHECKBOX', '<strong>' . __('Activate automatic image rendering in general.', 'yapb') . '</strong>', true),
						'yapb_display_images_xhtml' => array('CHECKBOX', __('Embed XHTML compliant HTML', 'yapb'), true)
					)
				),
				array(__('Home page', 'yapb'), __('The homepage usually shows a number of previously published posts.<br />You probably want to show thumbnails only.', 'yapb'),
					array(
						'yapb_display_images_home' => array('CHECKBOX', __('<strong>Display images on HOME page listing.</strong>', 'yapb'), true),
						'yapb_display_images_home_thumbnail' => array('CHECKBOX_INPUT', __('Display as thumbnail with a width of #3 px', 'yapb'), array(true, '200')),
						'yapb_display_images_home_inline_style' => array('INPUT', __('Inline CSS-Style for image tag: #40', 'yapb'), ''),
						'yapb_display_images_home_html_before' => array('TEXTAREA', __('Custom HTML before image tag', 'yapb'), '<div style="float:left;border:10px solid silver;margin-right:10px;margin-bottom:10px;">'),
						'yapb_display_images_home_html_after' => array('TEXTAREA', __('Custom HTML after image tag', 'yapb'), '</div>')
					)
				),
				array(__('Single Pages', 'yapb'), __('A single page shows a published post on its own.<br />You probably want to show the whole image -<br />But you can use thumbnailing here too, if you have design restrictions for example.', 'yapb'),
					array(
						'yapb_display_images_single' => array('CHECKBOX', __('<strong>Display images on SINGLE pages.</strong>', 'yapb'), true),
						'yapb_display_images_single_thumbnail' => array('CHECKBOX_INPUT', __('Display as thumbnail with a width of #3 px', 'yapb'), array(true, '460')),
						'yapb_display_images_single_inline_style' => array('INPUT', __('Inline CSS-Style for image tag: #40', 'yapb'), ''),
						'yapb_display_images_single_html_before' => array('TEXTAREA', __('Custom HTML before image tag', 'yapb'), '<div style="margin-bottom:20px;">'),
						'yapb_display_images_single_html_after' => array('TEXTAREA', __('Custom HTML after image tag', 'yapb'), '</div>')
					)
				),
				array(__('Archive Pages', 'yapb'), __('Archive pages usually show an overview of all published posts in a category, date range, etc.<br />You probably want to use thumbnails here.', 'yapb'),
					array(
						'yapb_display_images_archive' => array('CHECKBOX', __('<strong>Display images on ARCHIVE overview page listings.</strong>', 'yapb'), true),
						'yapb_display_images_archive_thumbnail' => array('CHECKBOX_INPUT', __('Display as thumbnail with a width of #3 px', 'yapb'), array(true, '100')),
						'yapb_display_images_archive_inline_style' => array('INPUT', __('Inline CSS-Style for image tag: #40', 'yapb'), ''),
						'yapb_display_images_archive_html_before' => array('TEXTAREA', __('Custom HTML before image tag', 'yapb'), '<div style="float:left;border:10px solid silver;margin-right:10px;margin-bottom:10px;">'),
						'yapb_display_images_archive_html_after' => array('TEXTAREA', __('Custom HTML after image tag', 'yapb'), '</div>')
					)
				),
				array(__('Content Pages', 'yapb'), __('You may post images to your content pages if you activate the according option above.<br />On content pages you probably want to show the original image.', 'yapb'),
					array(
						'yapb_display_images_page' => array('CHECKBOX', __('<strong>Display images on CONTENT pages.</strong>', 'yapb'), true),
						'yapb_display_images_page_thumbnail' => array('CHECKBOX_INPUT', __('Display as thumbnail with a width of #3 px', 'yapb'), array(false, '100')),
						'yapb_display_images_page_inline_style' => array('INPUT', __('Inline CSS-Style for image tag: #40', 'yapb'), ''),
						'yapb_display_images_page_html_before' => array('TEXTAREA', __('Custom HTML before image tag', 'yapb'), '<div style="float:left;border:10px solid silver;margin-right:10px;margin-bottom:10px;">'),
						'yapb_display_images_page_html_after' => array('TEXTAREA', __('Custom HTML after image tag', 'yapb'), '</div>')
					)
				)
			)
		)
	);

?>