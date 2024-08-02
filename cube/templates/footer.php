<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( is_singular( 'cubewp-tb' ) ) {
	$template = get_post_meta(get_the_ID(), 'template_type', true);
	if($template != 'footer'){
		do_action( 'cubewp/theme_builder/footer' );
	}
}else{
	do_action( 'cubewp/theme_builder/footer' );
}

?>

<?php wp_footer(); ?>

</body>
</html>
