<?php 
CSF::createWidget('sanas_footer_page_menu_widget', array(
    'title' => esc_html__('Sanas Page Menu Widget', 'sanas-core'),
    'classname' => 'col-lg-2 col-md-4 col-sm-12 footer-item',
    'description' => esc_html__('A widget which shows All Page', 'sanas-core'),
    'fields' => array(
        array(
            'id' => 'sanas_page_menu_widget_title',
            'title' => esc_html__('Title', 'sanas-core'),
            'type' => 'text',
            'default' => esc_html__('Website', 'sanas-core'),
        ),
        array(
            'id' => 'sanas_page_menu_widget_menu_choose',
            'title' => esc_html__('Choose Menu', 'sanas-core'),
            'type' => 'select',
            'options' => 'menus',
        ),
    ),
));
if (!function_exists('sanas_footer_page_menu_widget')) {
    function sanas_footer_page_menu_widget($args, $instance)
    {
        $selected_menu = $instance['sanas_page_menu_widget_menu_choose'];
        echo wp_kses_post($args['before_widget']);?>
        
            <h4><?php echo esc_html($instance['sanas_page_menu_widget_title']) ?></h4>
            <?php
             if (!empty($selected_menu)) {
                    wp_nav_menu(array('menu' => $selected_menu, 'menu_class' => 'footer-link', 'fallback_cb' => false));
                  }   
            ?>
            
       
       <?php echo wp_kses_post($args['after_widget']);
    }
}