<?php
/**
 * Block Name: Site Screenshot
 * Description: Display a screenshot of the site.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Showcase_2022\Site_Screenshot;

use function WordPressdotorg\Theme\Showcase_2022\site_screenshot_src;

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/site-screenshot',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}

/**
 * Render the block content.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the block markup.
 */
function render( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_ID = $block->context['postId'];
	$post = get_post( $post_ID );
	$img_content = '';

	$screenshot = site_screenshot_src( $post );

	if ( isset( $attributes['useHiRes'] ) && true === $attributes['useHiRes'] ) {
		$srcSet = add_query_arg( 'scale', 2, $screenshot );
		$img_content = '<picture>';
		$img_content .= "<source media='(max-width: 600px)' srcset='{$screenshot}' />";
		$img_content .= "<img src='{$srcSet} alt='" . the_title_attribute( array( 'echo' => false ) ) . "' loading='lazy' />";
		$img_content .= '</picture>';
	} else {
		$img_content = "<img src='{$screenshot}' alt='" . the_title_attribute( array( 'echo' => false ) ) . "' loading='lazy' />";

	}

	if ( isset( $attributes['isLink'] ) && true == $attributes['isLink'] ) {
		$img_content = '<a href="' . get_permalink( $post ) . '">' . $img_content . '</a>';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %s>%s</div>',
		$wrapper_attributes,
		$img_content
	);
}
