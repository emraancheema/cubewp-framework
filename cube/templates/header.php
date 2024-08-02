<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title>
			<?php
				// PHPCS - already escaped by WordPress.
				echo wp_get_document_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</title>
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
	wp_body_open();

	if ( is_singular( 'cubewp-tb' ) ) {
		$template = get_post_meta(get_the_ID(), 'template_type', true);
		if($template != 'header'){
			do_action( 'cubewp/theme_builder/header' );
		}
	}else{
		do_action( 'cubewp/theme_builder/header' );
	}
?>