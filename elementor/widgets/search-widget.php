<?php
namespace Elementor;
if (!defined('ABSPATH')) {
    exit;
}
class Sanas_Search_Widget extends Widget_Base {
    public function get_name() {
        return 'sanas_search';
    }
    public function get_title() {
        return esc_html__('Sanas Search', 'sanas-core');
    }
    public function get_icon() {
        return 'eicon-t-letter-bold';
    }
    public function get_categories() {
        return ['sanas'];
    }
    protected function register_controls() {
        $this->start_controls_section(
            'sanas_search_options',
            [
                'label' => esc_html__('Sanas Search', 'sanas-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'sanas_search_text',
            [
                'label' => esc_html__('Search', 'sanas-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Search templates...', 'sanas-core'),
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display(); ?>
        <form class="search-form text-center" method="get" action="<?php echo esc_url(home_url( '/' )); ?>">
            <input type="search" id="search" name="s" autocomplete="off" value="<?php the_search_query(); ?>" placeholder="<?php echo esc_html($settings['sanas_search_text']); ?>">
            <ul id="suggestionlist"></ul>
            <button class="search-btn" type="button"><i class="icon-Search"></i></button>
        </form>
        <?php
    }
}
Plugin::instance()->widgets_manager->register_widget_type(new Sanas_Search_Widget());