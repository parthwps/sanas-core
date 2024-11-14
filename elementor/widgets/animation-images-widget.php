<?php
namespace Elementor;
if (!defined('ABSPATH')) {
    exit;
}
class Sanas_Animation_Image_Widget extends Widget_Base {
    public function get_name() {
        return 'sanas_animation_image';
    }
    public function get_title() {
        return esc_html__('Sanas Animation Image', 'sanas-core');
    }
    public function get_icon() {
        return 'eicon-image-bold';
    }
    public function get_categories() {
        return ['sanas'];
    }
    protected function register_controls() {        
        $this->start_controls_section(
            'sanas_animation_image_options',
            [
                'label' => esc_html__('Sanas Animation Image', 'sanas-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'sanas_animation_image_options_choose', [
                'label' => esc_html__('Choose Image Style', 'sanas-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'one' => esc_html__('Style One', 'sanas-core'),
                    'two' => esc_html__('Style Two', 'sanas-core'),
                ],
                'default' => 'one',
            ]
        );
       $this->add_control(
            'sanas_animation_image_one_select', [
                'label'     => esc_html__('Select Image One','sanas-core'),
                'type'      => Controls_Manager::MEDIA,
                'default'   => [
                    'url'   => \Elementor\Utils::get_placeholder_image_src(),
                ]
            ]           
        );
        $this->add_control(
            'sanas_animation_image_two_select', [
                'label'     => esc_html__('Select Image Two','sanas-core'),
                'type'      => Controls_Manager::MEDIA,
                'default'   => [
                    'url'   => \Elementor\Utils::get_placeholder_image_src(),
                ]
            ]           
        );
        $this->add_control(
            'sanas_animation_image_three_select', [
                'label'     => esc_html__('Select Image Three','sanas-core'),
                'type'      => Controls_Manager::MEDIA,
                'default'   => [
                    'url'   => \Elementor\Utils::get_placeholder_image_src(),
                ]
            ]           
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display(); 
        if ($settings['sanas_animation_image_options_choose'] == 'one') { ?>
        <div class="card-img col-lft">
              <img class="card-img-1" src="<?php echo esc_url($settings['sanas_animation_image_one_select']['url']) ?>" alt="">
              <img class="card-img-2" src="<?php echo esc_url($settings['sanas_animation_image_two_select']['url']) ?>" alt="">
              <img class="card-img-3" src="<?php echo esc_url($settings['sanas_animation_image_three_select']['url'])?>" alt="">
        </div>
       <?php } 
       elseif($settings['sanas_animation_image_options_choose'] == 'two'){ ?>
       <div class="card-img col-rht">
              <img class="card-img-1" src="<?php echo esc_url($settings['sanas_animation_image_one_select']['url']) ?>" alt="">
              <img class="card-img-2" src="<?php echo esc_url($settings['sanas_animation_image_two_select']['url']) ?>" alt="">
              <img class="card-img-3" src="<?php echo esc_url($settings['sanas_animation_image_three_select']['url'])?>" alt="">
        </div>
       <?php
       }
    }
}
Plugin::instance()->widgets_manager->register_widget_type(new Sanas_Animation_Image_Widget());