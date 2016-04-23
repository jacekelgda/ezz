<?php
get_header();
global $unf_options;
?>
<div id="content-wrapper" class="row clearfix">
	<div id="content" class="col-md-12 column">
		<?php get_template_part( 'loop-single' ); ?>
	</div>
</div>

<?php get_footer(); ?>