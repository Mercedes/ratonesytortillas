<?php
/*
 * Plugin Name: Preview Theme
 * Plugin URI: http://dougal.gunters.org/blog/2005/03/09/theme-preview-plugin/
 * Description: Allows themes to be previewed without activation
 * Author: Dougal Campbell
 * Author URI: http://dougal.gunters.org/
 * Version: 1.0
 */

/*
 * USAGE:
 *
 * Add query variables 'preview_theme' and/or 'preview_css' to
 * your query string. Example:
 *  http://example.com/index.php?preview_theme=default&preview_css=my-theme
 * 
 */

if(isset($_GET['preview_theme'])){
	$gr_preview_theme = $_GET['preview_theme'];
	setcookie('preview_theme_' . COOKIEHASH, stripslashes($gr_preview_theme), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
}
else if(isset($_COOKIE['preview_theme_' . COOKIEHASH]) && file_exists(get_theme_root() . "/" . $_COOKIE['preview_theme_' . COOKIEHASH])){
	$gr_preview_theme = $_COOKIE['preview_theme_' . COOKIEHASH];
}

$gr_preview_css = $_GET['preview_css'];

if (! $gr_preview_css ) 
	$gr_preview_css = $gr_preview_theme;

if($gr_preview_theme && file_exists(get_theme_root() . "/$gr_preview_theme")) {
	add_filter('template','use_preview_theme');
}

if($gr_preview_css && file_exists(get_theme_root() . "/$gr_preview_css")) {
	add_filter('stylesheet','use_preview_css');
}

function use_preview_theme($themename) {
	global $gr_preview_theme;

	return $gr_preview_theme;
}

function use_preview_css($cssname) {
	global $gr_preview_css;

	return $gr_preview_css;
}

function theme_box(){
	global $gr_preview_theme;
	?>
	<style type="text/css">
		#show_box{
			background-color: #F7ECD4;
			border-bottom: 1px dotted #B0C036;
			border-right: 1px dotted #B0C036;
			position: absolute;
			left: 0;
			top: 0;
			z-index:1000;
			font: 9px "Lucida Grande", "Trebuchet MS", Verdana, sans-serif;
			color: #757562;
		}
	
		#show_box a{
			color: #C23D00;
			text-decoration:none;
		}
		
		#theme_box{
			background-color: #F7ECD4;
			border-bottom: 1px dotted #B0C036;
			border-right: 1px dotted #B0C036;
			position: absolute;
			left: 0;
			top: 0;
			display: block;
			height: 25px;
			padding: 3px;
		 	text-decoration: none;
			z-index:1000;
			font: 9px "Lucida Grande", "Trebuchet MS", Verdana, sans-serif;
			color: #757562;
			width:auto;
			text-align:left;
		}
		
		#theme_box a{
			color: #C23D00;
			text-decoration:none;
		}
		
		#hide_box{
			float:right;
			position:absolute;
			bottom:0px;
			right:0px;
		}
		
		#list-themes{
			position:relative;
			top:10px;
			border-bottom: 1px dotted #B0C036;
		}
		
		#theme_box .other_theme{
			background-color: #F7ECD4;
			border-top: 1px dotted #B0C036;
			border-right: 1px dotted #B0C036;
			border-left: 1px dotted #B0C036;
		}
		
		#theme_box img{
			height: 45px;
			padding: 2px;
			vertical-align: middle;
			width: 60px;
			border:0px;
		}
	</style>
	
	<div id='show_box' style='display:none'>[<a onClick='showBox()' style='cursor:pointer;'>X</a>]</div>
	<div id='theme_box'>
		<script type='text/javascript' src='./wp-includes/js/prototype.js'></script>
		<script type="text/javascript" charset="utf-8">
			function showBox()
			{
				$('show_box').hide();
				$('theme_box').show();
			}
			function hideBox()
			{
				$('theme_box').hide();
				$('show_box').show();
			}
			
			function showThemes()
			{
				$('showThemes').hide()
				$('list-themes', 'hideThemes').invoke('show');
			}
			
			function hideThemes()
			{
				$('list-themes', 'hideThemes').invoke('hide');
				$('showThemes').show()
			}
		</script>
		<div id='hide_box'>[<a onClick='hideBox()' style='cursor:pointer;'>X</a>]</div>
	<?php
		
	// Get list of themes
	$themes = get_themes();
	
	$theme_names = array_keys($themes);
	natcasesort($theme_names);
	
	foreach ($theme_names as $theme_name) {
		$template = $themes[$theme_name]['Template'];
		$title = $themes[$theme_name]['Title'];
			
		$template_dir = $themes[$theme_name]['Template Dir'];
		$screenshot = $themes[$theme_name]['Screenshot'];
			
		if($template == $gr_preview_theme 
			|| (strlen($gr_preview_theme) == 0 && $title == get_current_theme())){ ?>
			Current theme: <?php echo $title; ?> 
			
			<?php if (file_exists(ABSPATH."wp-content/themes/".$template.".zip")): ?>
				[<a href='wp-content/themes/<?php echo $template; ?>.zip'>dl</a>]
			<? endif; ?>
			<br/>
			<span id='showThemes'>(<a onClick='showThemes()' style='cursor:pointer;'>more</a>) </span>
			<span id='hideThemes' style='display:none'>(<a onClick='hideThemes()' style='cursor:pointer;'>less</a>) </span>
		<?php }
			
		$other_themes .= "<div class='other_theme'>";
		$other_themes .= "<a href='?preview_theme=$template'>";
		if($screenshot)
			$other_themes .= "<img src='$template_dir/$screenshot' width=60px height=45px/>";
				
 		$other_themes .= "$title</a>";
		if (file_exists(ABSPATH."wp-content/themes/".$template.".zip"))
			$other_themes .= " [<a href='wp-content/themes/$template.zip'>dl</a>]";
		$other_themes .= "</div>";
	} ?>
		<div id='list-themes' style='display:none'><?php echo $other_themes; ?></div>
	</div>
<?php
}

add_action('wp_head', 'theme_box');

?>