<?php
namespace Elementor;
if (!defined('ABSPATH')) {
    exit;
}
class Sanas_divider_Widget extends Widget_Base {
    public function get_name() {
        return 'sanas_divider';
    }
    public function get_title() {
        return esc_html__('Sanas Divider', 'sanas-core');
    }
    public function get_icon() {
        return 'eicon-info-circle-o';
    }
    public function get_categories() {
        return ['sanas'];
    }
    protected function register_controls() {
        $this->start_controls_section(
            'sanas_search_options',
            [
                'label' => esc_html__('Sanas Divider', 'sanas-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display(); ?>
       <div class="container-fluid">
        <span class="hero-sec-divaider">
          <hr>
        </span>
      </div>
        <?php
    }
}
Plugin::instance()->widgets_manager->register_widget_type(new Sanas_divider_Widget());