<?php
/*
Template Name: Blog
*/
get_header();
global $unf_options;
?>
<div id="content-wrapper" class="row clearfix">
	<div id="content" class="col-md-12 column">
		<div class="article clearfix">
			<h1 class="post-title">Galeria</h1>
			<?php get_template_part( 'loop' ); ?>
			<?php get_template_part('pagination'); ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>