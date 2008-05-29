<?php get_header(); ?>
<div class="entry">
	<?php if (have_posts()) : ?>

		<h2 style="color:#00CC00;">Search Results</h2>

	<?php while (have_posts()) : the_post(); ?>

<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
<div class="info">Written on <abbr title="<?php the_time('Y-m-d\TH:i:sO'); ?>"><?php unset($previousday); printf(__('%1$s &#8211; %2$s'), the_date('', '', '', false), get_the_time()) ?></abbr> | by Bob | <?php if(function_exists('the_views')) { the_views(); } ?></div>


	<div class="postinfo">Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>


		<?php endwhile; ?>

		<div class="navigation">
			<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
		</div>

	<?php else : ?>
	
		<h2 style="color:#FF0000">No posts found. Try a different search?</h2>

	<?php endif; ?>

	</div>


<?php get_footer(); ?>