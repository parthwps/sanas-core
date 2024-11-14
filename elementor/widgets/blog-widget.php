<?php
namespace Elementor;
if (!defined('ABSPATH')) {
    exit;
}
class Sanas_Blog_Widget extends Widget_Base {
    public function get_name() {
        return 'sanas_blog';
    }
    public function get_title() {
        return esc_html__('Sanas Blog', 'sanas-core');
    }
    public function get_icon() {
        return 'eicon-posts-grid';
    }
    public function get_categories() {
        return ['sanas'];
    }
    protected function register_controls() {
        $this->start_controls_section(
            'sanas_title_options',
            [
                'label' => esc_html__('Sanas Blog', 'sanas-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control('sanas_blogs_per_page', [
            'label'         => esc_html__('Posts Per Page','sanas-core'),
            'type'          => Controls_Manager::NUMBER,
            'description'   => esc_html__('Leave Empty or type "-1" to show all posts','sanas-core'),
            'default'       => -1
        ]);
        $this->add_control('sanas_blog_image_enable', [
            'label'     => esc_html__('Show Image','sanas-core'),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes'
        ]);
        $this->add_control('sanas_blog_title_enable', [
            'label'     => esc_html__('Show Title','sanas-core'),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes'
        ]);
        $this->add_control('sanas_blog_excerpt_enable', [
            'label'     => esc_html__('Show Excerpt','sanas-core'),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes'
        ]);
        $this->add_control('sanas_blog_date_enable', [
            'label'     => esc_html__('Show Date','sanas-core'),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes'
        ]);
        $this->add_control('sanas_blog_author_enable', [
            'label'     => esc_html__('Show Author','sanas-core'),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes'
        ]);
        $this->add_control('sanas_blog_all_show_enable', [
            'label'     => esc_html__('Show Author','sanas-core'),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes'
        ]);        

        $this->add_control('sanas_blog_all_show_text', [
            'label'     => esc_html__('Show ALL Text','sanas-core'),
            'type'      => Controls_Manager::TEXT,
            'default'   => 'View all'
        ]);        
        $this->add_control('sanas_blog_all_show_url', [
            'label'     => esc_html__('Show ALL URL','sanas-core'),
            'type'      => Controls_Manager::TEXT,

        ]);        

        $this->add_control('sanas_blog_heading', [
            'label'     => esc_html__('Heading Text','sanas-core'),
            'type'      => Controls_Manager::TEXT,
            'default'   => 'Featured Blogs'
        ]);        

        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display(); 
        $sanas_blog_heading=$settings['sanas_blog_heading'];
        $sanas_blog_all_show_enable=$settings['sanas_blog_all_show_enable'];
        $sanas_blog_all_show_text=$settings['sanas_blog_all_show_text'];
        $sanas_blog_all_show_url=$settings['sanas_blog_all_show_url'];

        $posts_per_page = $settings['sanas_blogs_per_page'] ? $settings['sanas_blogs_per_page'] : -1;
        $args = array(
            'post_type' => 'post',
            'posts_per_page'    => $posts_per_page,
            'order' => 'ASC'
        );
        $query = new \WP_Query($args);
        ?>
    <section class="blog-section">
    	<div class="container-fluid">
        <div class="sec-title style-two">
          <h2><?php echo $sanas_blog_heading;?></h2>
          <?php  if($sanas_blog_all_show_enable=='yes') { ?>
          <a href="<?php echo esc_url($sanas_blog_all_show_url); ?>" class="btn-outline btn-secondary"><?php echo $sanas_blog_all_show_text;?></a>
      	<?php } ?>
        </div>
        <div class="row">
        <?php if ($query->have_posts()) {
           while ($query->have_posts()) {
           $query->the_post(); ?>
          <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="blog-box">
            <?php if (has_post_thumbnail() && $settings['sanas_blog_image_enable'] == 'yes'): ?>
              <div class="image">
                <a href="<?php echo get_the_permalink() ?>">
                  <img src="<?php echo get_the_post_thumbnail_url() ?>" alt="">
                </a>
              </div>
            <?php endif; ?>
              <div class="blog-detaile">
                <?php if ($settings['sanas_blog_title_enable']): ?>
                <a href="<?php echo get_the_permalink() ?>" class="blog-hedding">
                <h4><?php echo get_the_title(); ?></h4>
                  <span class="blog-icon">
                    <i class="icon-Left-Up-Arrwo"></i>
                  </span>
                </a>
                <?php endif; ?>
                <?php if ($settings['sanas_blog_excerpt_enable']): ?><p><?php echo get_the_excerpt(); ?><?php endif; ?>
                </p>
              </div>
              <div class="blog-poster">
                <?php echo get_avatar(get_the_author_meta('ID'),40); ?>
                <div class="blog-poster-detaile">
                  <h6><?php echo get_the_author_meta('display_name'); ?></h6>
                  <span><?php echo get_the_date();  ?></span>
                </div>
              </div>
            </div>
          </div>
          <?php 
             }
            }
         ?>
        </div>
        </div>
    </section>
        <?php
    }
}
Plugin::instance()->widgets_manager->register_widget_type(new Sanas_Blog_Widget());