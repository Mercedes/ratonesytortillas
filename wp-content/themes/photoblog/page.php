<?php get_header(); ?>
			<div class="entry">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
		<h2><?php the_title(); ?></h2>
		
				<?php the_content('Read the rest of this page &raquo;'); ?>

				

		</div>
		<?php endwhile; endif; ?>
<br clear="all" />
	<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>

</div>

<?php get_footer(); ?>