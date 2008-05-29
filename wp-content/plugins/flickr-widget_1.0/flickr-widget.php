<?
/*
Plugin Name: Flickr Badge Widget
Description: Display your flickr photos in your widget sidebar, many options available
Author: Ben Coleman
Version: 1.0
Author URI: http://www.bencoleman.co.uk
*/
function widget_flickrbadge_init()
{
	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ) return;

	// main widget function
	function widget_flickrbadge($args) {
		extract($args);
			
		$options = get_option('widget_flickrbadge');
		$title = $options['title'];
		$flickrid = $options['user'];
		$count = $options['count'];
		$size = $options['size'];
		$display = $options['display'];
		$source = $options['source'];
		$tag = $options['tag'];
		$layout = $options['layout'];
		$align = $options['align'];
		if(!$title) $title = "Flickr Photos";
		
		echo $before_widget . $before_title . $title . $after_title;
		?>
		<div class="flickrbadge" align="<?=$align?>">
		<!-- Start of Flickr Badge -->
		<style type="text/css">
		#flickr_badge_source_txt {padding:0; font: 11px Arial, Helvetica, Sans serif; color:#FFFFFF;}
		#flickr_badge_icon {display:block !important; margin:0 !important; border: 1px solid rgb(0, 0, 0) !important;}
		#flickr_icon_td {padding:0 5px 0 0 !important;}
		.flickr_badge_image {text-align:center !important;}
		.flickr_badge_image img {border: 1px solid black !important;}
		#flickr_www {display:block; padding:0 10px 0 10px !important; font: 11px Arial, Helvetica, Sans serif !important; color:#3993ff !important;}
		#flickr_badge_uber_wrapper a:hover,
		#flickr_badge_uber_wrapper a:link,
		#flickr_badge_uber_wrapper a:active,
		#flickr_badge_uber_wrapper a:visited {text-decoration:none !important; background:inherit !important;color:#FFFFFF;}
		#flickr_badge_wrapper {}
		#flickr_badge_source {padding:0 !important; font: 11px Arial, Helvetica, Sans serif !important; color:#FFFFFF !important;}
		</style>
		<table id="flickr_badge_uber_wrapper" cellpadding="0" cellspacing="5" border="0"><tr><td>
		<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?=$count?>&display=<?=$display?>&size=<?=$size?>&layout=<?=$layout?>&source=<?=$source?>&user=<?=$flickrid?>&tag=<?=$tag?>&group=<?=$flickrid?>"></script>
		</td></tr></table>
		<a href="http://www.flickr.com" id="flickr_www">www.<strong style="color:#3993ff">flick<span style="color:#ff1c92">r</span></strong>.com</a>
		</div>
		<!-- End of Flickr Badge -->
		<?
		echo $after_widget;
	}
	
	// control panel
	function widget_flickrbadge_control() {
		$options = $newoptions = get_option('widget_flickrbadge');
		if ( $_POST["flickrbadge-submit"] ) {
			$newoptions['title'] = trim(strip_tags(stripslashes($_POST["flickrbadge-title"])));
			$newoptions['user'] = trim(strip_tags(stripslashes($_POST["flickrbadge-user"])));
			$newoptions['source'] = trim(strip_tags(stripslashes($_POST["flickrbadge-source"])));
			$newoptions['tag'] = trim(strip_tags(stripslashes($_POST["flickrbadge-tag"])));
			$newoptions['count'] = trim(strip_tags(stripslashes($_POST["flickrbadge-count"])));
			$newoptions['layout'] = trim(strip_tags(stripslashes($_POST["flickrbadge-layout"])));
			$newoptions['display'] = trim(strip_tags(stripslashes($_POST["flickrbadge-display"])));
			$newoptions['size'] = trim(strip_tags(stripslashes($_POST["flickrbadge-size"])));
			$newoptions['align'] = trim(strip_tags(stripslashes($_POST["flickrbadge-align"])));
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_flickrbadge', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$user = htmlspecialchars($options['user'], ENT_QUOTES);
		$source = htmlspecialchars($options['source'], ENT_QUOTES);
		$tag = htmlspecialchars($options['tag'], ENT_QUOTES);
		$count = htmlspecialchars($options['count'], ENT_QUOTES);
		$layout = htmlspecialchars($options['layout'], ENT_QUOTES);
		$display = htmlspecialchars($options['display'], ENT_QUOTES);
		$size = htmlspecialchars($options['size'], ENT_QUOTES);
		$align = htmlspecialchars($options['align'], ENT_QUOTES);
		
		if (empty($count)) $count = '3';
		
		?>
		<table>
		<tr><td><strong>Widget Title:</strong></td>
		<td><input id="flickrbadge-title" name="flickrbadge-title" type="text" size="30" value="<?php echo $title; ?>" /></td>
		<td style="font-size:0.75em">Optional</td>
		</tr>
		<tr><td><strong>Source:</strong></td>
		<td>
		<select id="flickrbadge-source" name="flickrbadge-source" size="1" value="" />
		   <option value="user" <?=($source=="user")?'selected':''?>>Specific User</option>
		   <option value="user_tag" <?=($source=="user_tag")?'selected':''?>>Specific User (Tag)</option>
		   <option value="group" <?=($source=="group")?'selected':''?>>Group Pool</option>
		   <option value="group_tag" <?=($source=="group_tag")?'selected':''?>>Group Pool (Tag)</option>
		   <option value="all" <?=($source=="all")?'selected':''?>>Everyone</option>
		   <option value="all_tag" <?=($source=="all_tag")?'selected':''?>>Everyone (Tag)</option>
   	</select>
		</td>
		<td style="font-size:0.75em">Where to pick the photos from; can be a specific user, a group pool or all of flickr (everyone)</td>
		</tr>
		<tr><td><strong>Flickr ID:</strong></td>
		<td><input id="flickrbadge-user" name="flickrbadge-user" type="text" size="30" value="<?php echo $user; ?>" /></td>
		<td style="font-size:0.75em">If showing photos from a user or group enter the ID, Use <a href="http://idgettr.com/" target="idgettr">http://idgettr.com/</a> to find your ID</td>
		</tr>
		<tr><td><strong>Tag:</strong></td>
		<td><input id="flickrbadge-tag" name="flickrbadge-tag" type="text" size="10" value="<?php echo $tag; ?>" /></td>
		<td style="font-size:0.75em">Only use this if the selected source is (Tag) then it will only show photos with this tag</td>
		</tr>
		<tr><td><strong>Photo Count:</strong></td>
		<td><input id="flickrbadge-count" name="flickrbadge-count" type="text" size="10" value="<?php echo $count; ?>" /></td>
		<td style="font-size:0.75em">Number of photos to show</td>
		</tr>
		<tr><td><strong>Layout:</strong></td>
		<td>
		<select id="flickrbadge-layout" name="flickrbadge-layout" size="1" value="" />
		   <option value="v" <?=($layout=="v")?'selected':''?>>Vertical</option>
		   <option value="h" <?=($layout=="h")?'selected':''?>>Horizontal</option>
   	</select>
		</td>
		<td style="font-size:0.75em">Layout orientation of the photos</td>
		</tr>
		<tr><td><strong>Which Photos:</strong></td>
		<td>
		<select id="flickrbadge-display" name="flickrbadge-display" size="1" value="" />
		   <option value="random" <?=($display=="random")?'selected':''?>>Random</option>
		   <option value="latest" <?=($display=="latest")?'selected':''?>>Latest</option>
   	</select>
		</td>
		<td style="font-size:0.75em">How to select the photos to display; either randomly or the newest photos</td>
		</tr>
		<tr><td><strong>Photo Size:</strong></td>
		<td>
		<select id="flickrbadge-size" name="flickrbadge-size" size="1" value="" />
		   <option value="s" <?=($size=="s")?'selected':''?>>Small Square</option>
		   <option value="t" <?=($size=="t")?'selected':''?>>Thumbnail</option>
		   <option value="m" <?=($size=="m")?'selected':''?>>Medium</option>
   	</select>
		</td>
		<td style="font-size:0.75em">Size of the photos displayed in the sidebar</td>
		</tr>
		<tr><td><strong>Alignment:</strong></td>
		<td>
		<select id="flickrbadge-align" name="flickrbadge-align" size="1" value="" />
		   <option value="left" <?=($align=="left")?'selected':''?>>Left</option>
		   <option value="center" <?=($align=="center")?'selected':''?>>Center</option>
		   <option value="right" <?=($align=="right")?'selected':''?>>Right</option>
   	</select>
		</td>
		<td style="font-size:0.75em">Alignment of the section (div) containing the photos</td>
		</tr>
		</table>
		<input type="hidden" id="flickrbadge-submit" name="flickrbadge-submit" value="1" /></div>
		<?
	}
	
	register_sidebar_widget('Flickr Badge', 'widget_flickrbadge');
	register_widget_control('Flickr Badge', 'widget_flickrbadge_control', 600, 330);
}

// Tell Dynamic Sidebar about our new widget and its control
add_action('plugins_loaded', 'widget_flickrbadge_init');

?>