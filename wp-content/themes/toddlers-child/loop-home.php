<div id="content" class="col-md-8 column">
	<div class="article clearfix">
		<div class="well well-lg">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<?php the_content();?>
		    <?php endwhile;
		    endif; ?>
		</div>
	</div>
</div>