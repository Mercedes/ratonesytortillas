<?php get_header(); ?>
<div class="post">
<div class="entry">
<p><?php bloginfo('description'); ?></p><br/>
	<?php if (have_posts()) : ?>
	
			<?php while (have_posts()) : the_post(); ?>
	<ul class="archive">
		<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a>
				
					<?php the_excerpt(); ?>
			<p class="postmetadata">Posted on <?php the_time('M d.y') ?> to <?php the_category(', ') ?> &nbsp;&nbsp;<?php edit_post_link('Edit', '', ''); ?></p>
			
		</li>
	</ul>

	<?php endwhile; ?>
	<?php endif; ?>
	<div class="navigation">
		<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
		<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
	</div>
</div>
</div><!-- end post -->


<?php get_sidebar(); ?>

<?php get_footer(); ?>