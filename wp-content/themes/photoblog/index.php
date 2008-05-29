<?php get_header(); ?>
<div class="entry">
<div align="center" style="padding:10px;">

</div>
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
							
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
<div class="info">Written on <abbr title="<?php the_time('Y-m-d\TH:i:sO'); ?>"><?php unset($previousday); printf(__('%1$s &#8211; %2$s'), the_date('', '', '', false), get_the_time()) ?></abbr> | by Me | <?php if(function_exists('the_views')) { the_views(); } ?></div>

				
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
