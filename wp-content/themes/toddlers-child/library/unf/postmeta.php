<?php global $unf_options; ?>
<p class="byline vcard post-meta">
	<time class="updated" datetime="<?php echo get_the_time(get_option('date_format')) ?>">
		<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
			<?php echo get_the_time(get_option('date_format')) ?>
		</a>
	</time>
</p>
