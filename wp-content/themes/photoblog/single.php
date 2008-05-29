<?php get_header(); ?>

		<div class="entry">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
		<div class="info">Written on <abbr title="<?php the_time('Y-m-d\TH:i:sO'); ?>"><?php unset($previousday); printf(__('%1$s &#8211; %2$s'), the_date('', '', '', false), get_the_time()) ?></abbr> | by Bob | <?php if(function_exists('the_views')) { the_views(); } ?></div>
		
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
				<?php if ( function_exists('the_tags') ) { the_tags('<p>Tags: ', ', ', '</p>'); } ?>
		</div>
<br clear="all" />
<div class="entry">
	<?php comments_template(); ?>
	</div>
<div class="navigation">
			<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
		</div>
	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

	</div>

<?php get_footer(); ?>
