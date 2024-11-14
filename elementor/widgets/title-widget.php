<?php
namespace Elementor;
if (!defined('ABSPATH')) {
    exit;
}
class Sanas_Title_Widget extends Widget_Base {
    public function get_name() {
        return 'sanas_title';
    }
    public function get_title() {
        return esc_html__('Sanas Title', 'sanas-core');
    }
    public function get_icon() {
        return 'eicon-t-letter-bold';
    }
    public function get_categories() {
        return ['sanas'];
    }
    protected function register_controls() {
        $this->start_controls_section(
            'sanas_title_options',
            [
                'label' => esc_html__('Sanas Title', 'sanas-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'sanas_title_text',
            [
                'label' => esc_html__('Title', 'sanas-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Enter Title Here', 'sanas-core'),
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display(); ?>
        <div class="section-title text-center">
            <h1><?php echo esc_html($settings['sanas_title_text']); ?></h1>
        </div>
        <?php
    }
}
Plugin::instance()->widgets_manager->register_widget_type(new Sanas_Title_Widget());