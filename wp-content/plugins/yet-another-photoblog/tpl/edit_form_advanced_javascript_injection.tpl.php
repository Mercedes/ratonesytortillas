
<script type="text/javascript">
/* <![CDATA[ */

	// Yapb JavaScript Injection
	// JP 2008-04-23: Migration from prototype.js to jQuery

	(function($) {

		function enhanceForm() {

			// Mutate the form to a fileupload form
			// As usual: Special code for IE
			if (jQuery.browser.msie) $('#post').attr('encoding', 'multipart/form-data');
			else $('#post').attr('enctype', 'multipart/form-data');

			// Ensure proper encoding
			$('#post').attr('acceptCharset', 'UTF-8');

			// Insert the fileupload field
			$('#titlediv').after('<?php echo YapbUtils::escape($this->content) ?>');

		}

		/* 
			
			We call the function right now, because wordpress already 
			generated all we need for this. We could also plug this in 
			as onLoad method via jQuery:
			
			$(document).ready(
				function() { 
					enhanceForm(); 
				}
			);

			But that's a little bit slow since the form addition
			shows after the completion of page loading

		*/

		enhanceForm();

	})(jQuery);

	// Plain simple javascript after this point

	var valueCache = new Array();

	function toggleCategory(onOff) {

		<?php if (get_option('yapb_default_post_category_activate')): ?>

			// This function unchecks all categories except the defined one
			// if the user choses an image

			var postFormElements = document.forms['post'].elements;

			// we cycle through all form elements
			for (var i=0, len=postFormElements.length; i<len; i++) {

				var currentElement = postFormElements[i];

				// if the current element has an id
				if (currentElement.id) {

					var match = currentElement.id.match(/category-(\d+)/);
					
					// if it is a category checkbox
					if (match && (match.length == 2)) {

						if (onOff) {

							// check chosen category, uncheck the rest
							valueCache[match[1]] = currentElement.checked;

							currentElement.checked = 
								(match[1] == <?php echo get_option('yapb_default_post_category') ?>) 
									? true
									: false;

						} else {

							// restore former categories
							currentElement.checked = valueCache[match[1]];

						}
					}
				}
			}

		<?php else: ?>
			
			// The function does nothing since the user doesn't want to 
			// check a default category when posting a new photoblog image

		<?php endif ?>
	
	}

/* ]]> */
</script>

<!-- /Yapb JavaScript Injection -->