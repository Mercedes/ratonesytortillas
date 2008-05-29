<?php get_header(); ?>
<div class="entry">
<div align="center" style="padding:10px;">
<script type="text/javascript">
/* <![CDATA[ */
function affiliateLink(str){ str = unescape(str); var r = ''; for(var i = 0; i < str.length; i++) r += String.fromCharCode(2^str.charCodeAt(i)); document.write(r); }
affiliateLink('%3Ec%22jpgd%3F%20jvvr8--uuu%2Cvgzv/nkli/cfq%2Camo-%3Dpgd%3F%3B2407%20%3C%3Ekoe%22qpa%3F%20jvvr8--uuu%2Cvgzv/nkli/cfq%2Camo-kocegq-vgzv%5Dnkli%5Dcfq%5DC%5D64%3Az42%2Cekd%20%22%60mpfgp%3F%202%20%22cnv%3F%20Vgzv%22Nkli%22Cfq%20%3C%3E-c%3C');
/* ]]> */
</script>
</div>
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
							
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
<div class="info">Written on <abbr title="<?php the_time('Y-m-d\TH:i:sO'); ?>"><?php unset($previousday); printf(__('%1$s &#8211; %2$s'), the_date('', '', '', false), get_the_time()) ?></abbr> | by Bob | <?php if(function_exists('the_views')) { the_views(); } ?></div>

				
					<?php the_content('<br />Read the rest of this entry &raquo;'); ?>
<?php if ( function_exists('the_tags') ) { the_tags('<p>Tags: ', ', ', '</p>'); } ?>

				<div class="postinfo"><?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?> <img src="<?php bloginfo('template_directory'); ?>/images/cm.gif" alt="comment" /> | Posted in &raquo; <?php the_category(', ') ?> | <?php edit_post_link('| Edit', '', ' | '); ?> </div>

			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
		</div>

	<?php else : ?>

		<h2>Not Found</h2>
		<p>Sorry, but you are looking for something that isn't here.</p>


	<?php endif; ?>
	</div>

<?php get_footer(); ?>
