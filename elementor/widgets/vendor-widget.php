<?php
namespace Elementor;

class Sanas_Vendor_Widget extends Widget_Base {
	public function get_name() {
		return 'since_post';
	}
	public function get_title() {
		return esc_html__('Featured Vendors','sanas');
	}
	public function get_icon() {
		return 'eicon-button';
	}
	public function get_categories() {
		return ['sanas'];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'sanas_options', [
				'label'	=> esc_html__('sanas items','sanas'),
				'tab'	=> Controls_Manager::TAB_CONTENT
			]
		);
        $this->add_control(
			'sanas_title_text',
			[
				'label' => esc_html__( 'Title', 'sanas' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Featured Vendors', 'sanas' ),
			]
		);
		$this->add_control(
			'sanas_button_text', [
				'label'		=> esc_html__('Button Text','sanas'),
				'type'		=> Controls_Manager::TEXT,
				'default'	=> esc_html__('View More','sanas')
			]
		);
		$this->add_control(
			'sanas_button_link', [
				'label'		=> esc_html__('Button Link','sanas'),
				'type'		=> Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'sanas' ),
			]
		);
		
		$repeater = new \Elementor\Repeater();
        

        $repeater->add_control(
            'sanas_vendor_text', [
                'label'     => esc_html__('Text','sanas-core'),
                'type'      => Controls_Manager::TEXT,             
                'default'   => esc_html__('Diamond Jewellery','sanas-core')
            ]
        );
        $repeater->add_control(
			'sanas_btn_text', [
				'label'		=> esc_html__('Button Text','sanas'),
				'type'		=> Controls_Manager::TEXT,
				'default'	=> esc_html__('View More','sanas')
			]
		);
		$repeater->add_control(
			'sanas_btn_link', [
				'label'		=> esc_html__('Button Link','sanas'),
				'type'		=> Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'sanas' ),
			]
		);
        $repeater->add_control(
        'sanas_vendor_img',
        [
            'label'     => esc_html__('Image','sanas'),
            'type'      => Controls_Manager::MEDIA,
            'default'   => [
                'url'   => \Elementor\Utils::get_placeholder_image_src(),
            ]
        ]
        );
        $this->add_control('sanas_vendor_details', [
            'label'     => esc_html__('Sanas Vendor Details','sanas'),
            'type'      => Controls_Manager::REPEATER,
            'fields'    => $repeater->get_controls(),
            'title_field'   => '{{{sanas_vendor_text}}}',
            'default'   => [
                [
                    'sanas_vendor_text'   => esc_html__('Diamond Jewellery'),
                ]
            ]
            ]);
		$this->end_controls_section();

		
	}
	protected function render() {
	$settings = $this->get_settings_for_display();

	?>
    <section class="featured-section">
        <div class="sec-title style-two">
          <h2><?php echo esc_html($settings['sanas_title_text']); ?></h2>
        </div>
        <div class="featured-vendors">
<?php foreach($settings['sanas_vendor_details'] as $vendor) : ?>        	
          <div class="vendors-item">
            <div class="featured-box">
			<?php if(!empty($vendor['sanas_vendor_img']['url'])) :?>
              <img src="<?php echo esc_url($vendor['sanas_vendor_img']['url']) ?>" alt="">
          	  <?php endif; ?>
              <div class="lower-content-2">
                <h4><?php echo esc_html($vendor['sanas_vendor_text']) ?></h4>
                <a class="btn btn-primary" href="#">View More</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        </div>
    </section>
	<?php 
}

}
Plugin::instance()->widgets_manager->register_widget_type(new Sanas_Vendor_Widget);