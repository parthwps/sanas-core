<?php
CSF::createWidget('sanas_about_widget', array(
    'title' => esc_html__('Sanas About Widget', 'sanas-core'),
    'classname' => 'col-lg-4 col-md-6 col-sm-12 footer-item',
    'description' => esc_html__('A Widget which shows info about site', ',sanas-core'),
       'fields' => array(
        array(
            'id' => 'sanas_about_widget_title',
            'title' => esc_html__('Title', 'sanas-core'),
            'type' => 'text',
            'default' => esc_html__('About', 'sanas-core')
        ),
        array(
            'id' => 'sanas_about_widget_text',
            'title' => esc_html__('Text', 'sanas-core'),
            'type' => 'textarea',
            'default' => esc_html__('Finding Vendors is easy by searching our trusted network of top-rated Vendors.', 'sanas-core')
        ),
        ),
));
if (!function_exists('sanas_about_widget')) {
    function sanas_about_widget($args, $instance)
    {
    echo wp_kses_post($args['before_widget']);?>
        <h4><?php echo esc_html($instance['sanas_about_widget_title']) ?></h4>
        <p><?php echo esc_html($instance['sanas_about_widget_text']) ?></p>
    <?php echo wp_kses_post($args['after_widget']);                                
    }
}


