<?php
if (!defined('ABSPATH')) {
	exit;
}
function sanas_plugin_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1 Inner 1', 'sanas' ),
			'id'            => 'footer-1-inner-1-widget',
			'description'   => esc_html__( 'Add widgets here.', 'sanas' ),
			'before_title'  => '<h4>',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1 Inner 2', 'sanas' ),
			'id'            => 'footer-1-inner-2-widget',
			'description'   => esc_html__( 'Add widgets here.', 'sanas' ),
			'before_title'  => '<h2 class="widget-title footer-widget__title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1 Inner 3', 'sanas' ),
			'id'            => 'footer-1-inner-3-widget',
			'description'   => esc_html__( 'Add widgets here.', 'sanas' ),
			'before_title'  => '<h2 class="widget-title footer-widget__title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1 Inner 4', 'sanas' ),
			'id'            => 'footer-1-inner-4-widget',
			'description'   => esc_html__( 'Add widgets here.', 'sanas' ),
			'before_title'  => '<h2 class="widget-title footer-widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init','sanas_plugin_widgets_init');