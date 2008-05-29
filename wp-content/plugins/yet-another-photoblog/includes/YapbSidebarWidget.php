<?php

	/*
	Plugin Name: YAPB Widget
	Plugin URI: http://johannes.jarolim.com
	Description: Adds a sidebar widget to display the latest images
	Author: J.P.Jarolim
	License: GPL
	Version: 1.0
	Author URI: http://johannes.jarolim.com
	*/

	// This gets called at the plugins_loaded action
	
	function yapbSidebarWidgetInit() {

		// check for yapb and sidebar existance

		if (
			!array_key_exists('yapb', $GLOBALS) ||			// Yapb instanced?
			!function_exists('register_sidebar_widget') 	// Sidebar widgets possible?
		) return;

		function yapbSidebarWidgetDraw($args) {

			extract($args);

			
			$imagecount = get_option('yapb_sidebarwidget_imagecount');
			$maxsize = get_option('yapb_sidebarwidget_maxsize');
			$restrict = get_option('yapb_sidebarwidget_restrict');
			$title = get_option('yapb_sidebarwidget_title');
			
			switch(get_option('yapb_sidebarwidget_displayas')) {
				
				case 'ul' :
					
					$beforeBlock = '<ul class="yapb-latest-images">';
					$beforeItem = '<li>';
					$afterItem = '</li>';
					$afterBlock = '</ul>';
					break;
				
				case 'div' :
				default:

					$beforeBlock = '<div class="yapb-latest-images">';
					$beforeItem = '';
					$afterItem = '';
					$afterBlock = '</div>';
					break;
					
			}
			
			
		?>
			
			<?php echo $before_widget ?>

				<?php if (trim($title) != ''): ?>
					<?php echo $before_title . $title . $after_title ?>
				<?php endif ?>

				<?php 
					global $wpdb;
					$latest_posts = $wpdb->get_results('SELECT p.* FROM ' . $wpdb->posts . ' p LEFT JOIN ' . YAPB_TABLE_NAME . ' yi ON p.ID = yi.post_id WHERE p.post_type = \'post\' AND yi.URI IS NOT NULL ORDER BY p.post_date DESC LIMIT 0,' . $imagecount);
					$thumbConfig = array(($restrict=='v' ? 'h=' : 'w=') . $maxsize,'q=100','fltr[]=usm|60|0.5|3');
				?>
				<?php if (!empty($latest_posts)): ?>
					<?php echo $beforeBlock ?>
					<?php foreach($latest_posts as $current_post): ?>
						<?php if (!is_null($image = YapbImage::getInstanceFromDb($current_post->ID))): ?>
							<?php echo $beforeItem ?><a title="<?php echo $current_post->post_title ?>" style="border:0;padding:0;margin:0;" href="<?php echo get_permalink($current_post->ID) ?>"><img border="0" style="padding-right:2px;padding-bottom:2px;" src="<?php echo $image->getThumbnailHref($thumbConfig) ?>" width="<?php echo $image->getThumbnailWidth($thumbConfig) ?>" height="<?php echo $image->getThumbnailHeight($thumbConfig) ?>" alt="<?php echo $current_post->post_title ?>" /></a><?php echo $afterItem ?>
						<?php endif ?>
					<?php endforeach ?>
					<?php echo $afterBlock ?>
				<?php else: ?>
					<p class="yapb-no-latest-images">nothing yet</p>
				<?php endif ?>

			<?php echo $after_widget ?>

		<?php
			
		}

		register_sidebar_widget(array('YAPB Widget', 'widgets'), 'yapbSidebarWidgetDraw');

	}

	// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
	add_action('plugins_loaded', 'yapbSidebarWidgetInit');

?>
