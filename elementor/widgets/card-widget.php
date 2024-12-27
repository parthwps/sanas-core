<?php
namespace Elementor;
if (!defined('ABSPATH')) {
    exit;
}
class Sanas_card_Widget extends Widget_Base {
    public function get_name() {
        return 'sanas_card';
    }
    public function get_title() {
        return esc_html__('Sanas Card', 'sanas-core');
    }
    public function get_icon() {
        return 'eicon-tools';
    }
    public function get_categories() {
        return ['sanas'];
    }
    protected function register_controls() {
        $this->start_controls_section(
            'sanas_card_options',
            [
                'label' => esc_html__('Sanas Card', 'sanas-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control('sanas_card_posts_per_page', [
            'label'     => esc_html__('Posts Per Page','sanas-core'),
            'type'      => Controls_Manager::NUMBER,
            'description' => esc_html__('Leave Empty or type "-1" to show all posts','sanas-core'),
            'default'   => -1
        ]);
        $this->add_control('sanas_card_mobile_all_url', [
            'label'     => esc_html__('All categories Mobile URL','sanas-core'),
            'type'      => Controls_Manager::TEXT,
        ]);   
        $this->end_controls_section();
    }
    protected function render() {
      global $wpdb;
      $settings = $this->get_settings_for_display();
      $posts_per_page = $settings['sanas_card_posts_per_page'] ? $settings['sanas_card_posts_per_page'] : -1;
      $args = array(
          'post_type' => 'sanas_card',
          'posts_per_page' => $posts_per_page,
          'order' => 'ASC'
      );

      $settings = $this->get_settings_for_display();
      $posts_per_page = $settings['sanas_card_posts_per_page'] ? $settings['sanas_card_posts_per_page'] : -1;
      $sanas_card_mobile_all_url = $settings['sanas_card_mobile_all_url'];
     ?>
     <section class="tab-section">
      <div class="container">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
          <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pills-bestloved-tab" data-bs-toggle="pill" data-bs-target="#pills-bestloved" type="button" role="tab" aria-controls="pills-bestloved" aria-selected="true">Best Loved</button>
          </li>
            <?php
            $terms = get_terms(
                array(
                    'taxonomy'   => 'sanas-card-category', // Replace with your taxonomy name
                    'hide_empty' => false,
                    'meta_query' => array(
                        array(
                            'key'     => 'card_category_home',
                            'value'   => '1',
                            'compare' => '='
                        )
                    )
                )
            );
            if (!empty($terms) && !is_wp_error($terms)) {
               $j=1;
                foreach ($terms as $term): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="pills-<?php echo esc_attr($term->slug); ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-<?php echo esc_attr($term->slug); ?>" type="button" role="tab" aria-controls="pills-<?php echo esc_attr($term->slug); ?>" aria-selected="true"><?php echo esc_html($term->name); ?></button>
                    </li>
                <?php $j++; endforeach;
            } else {
                echo '<li class="nav-item">No categories found.</li>';
            }
            ?>
        </ul>
        <div class="all-tamplate">
          <a href="<?php echo $sanas_card_mobile_all_url; ?>" class="all-tamplate-btn">View all categories <i class="icon-chevron-down"></i></a>
        </div>
        <div class="tab-content" id="pills-tabContent">
          <div class="tab-pane fade show active" id="pills-bestloved" role="tabpanel" aria-labelledby="pills-bestloved">
            <div class="row" id="cardBoxContainer">
<?php 
$args = array(
    'post_type' => 'sanas_card',
    'posts_per_page' => -1, // Retrieve all posts
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'sanas_bestloved_homepage',
            'value' => '1',
            'compare' => '='
        )
    )
);

$query = new \WP_Query($args);

if ($query->have_posts()) :
    while ($query->have_posts()) : $query->the_post();
        $sanas_portfolio_meta = get_post_meta(get_the_ID(), 'sanas_metabox', true);

        $is_in_wishlist = $wpdb->get_var($wpdb->prepare(
          "SELECT id FROM {$wpdb->prefix}sanas_wishlist WHERE user_id = %d AND card_id = %d",
          get_current_user_id(),
          get_the_ID()
        ));
        echo "<script>console.log(".$is_in_wishlist.");</script>";

        if ($sanas_portfolio_meta) {
            $currentURL = site_url();
            $dashQuery = '/user-dashboard';
            $dashpage = '/?dashboard=cover';

            // Build the URL with proper query parameter
            $dashboardURL = add_query_arg('card_id', get_the_ID(), esc_url($currentURL . $dashQuery . $dashpage));

            // Check if background color is set
            if (isset($sanas_portfolio_meta['sanas_bg_color'])) {
                $bg_color = 'style="background:' . esc_attr($sanas_portfolio_meta['sanas_bg_color']) . '"';
                $bg_color_code=esc_attr($sanas_portfolio_meta['sanas_bg_color']);
            }

            ?>

            <div class="card-box col-lg-3 col-md-4 col-sm-6 card-preview" data-card-id="<?php echo get_the_ID(); ?>" data-bg-color="<?php echo isset($bg_color) ? $bg_color : ''; ?>"
                    data-front-img="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_front_Image']['url']); ?>" data-href="<?php echo esc_url($dashboardURL); ?>"
                    data-back-img="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_back_Image']['url']); ?>" data-card-title="<?php echo esc_html(get_the_title()); ?>"
                    data-bgcolor-code="<?php echo esc_attr($sanas_portfolio_meta['sanas_bg_color']) ?>">
                <div class="inner-box">
                    <a 
                    href="javascript:void(0)" 
                    class="<?php echo is_user_logged_in() ? '' : 'login-in sanas-login-popup'; ?> flip-container" 
                    <?php echo !is_user_logged_in() ? 'data-href="' . esc_url($dashboardURL) . '" data-card-id="' . get_the_ID() . '"' : ''; ?> 
                    <?php echo isset($bg_color) ? $bg_color : ''; ?>
                    data-bg-color="<?php echo isset($bg_color_code) ? $bg_color_code : ''; ?>"
                    >
                        <div class="flipper">
                            <div class="front">
                                <img src="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_front_Image']['url']); ?>" alt="template">
                            </div>
                            <div class="middel-card">
                                <img src="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_back_Image']['url']); ?>" alt="template">
                            </div>
                            <div class="back">
                                <img src="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_back_Image']['url']); ?>" alt="template">
                            </div>
                        </div>
                    </a>
                    <div class="lower-content">
                        <a href="javascript:void(0);" class="card-box-title">
                            <h4><?php echo esc_html(get_the_title()); ?></h4>
                        </a>
                        <a href="javascript:void(0);">Free</a>
                        <?php if (is_user_logged_in()) : ?>
                          <div class="heart-icon <?php echo $is_in_wishlist ? 'active' : ''; ?>" 
                            data-card-id="<?php echo get_the_ID(); ?>" 
                            data-is-in-wishlist="<?php echo $is_in_wishlist ? 'true' : 'false'; ?>">
                            <i class="icon-Heart"></i>
                          </div>
                        <?php else : ?>
                          <div class="heart-icon sanas-login-popup">
                            <i class="icon-Heart"></i>
                          </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php
        }
    endwhile;
    wp_reset_postdata();
endif;
?>


            </div>
         </div>     
        <?php 
      $args = array(
        'post_type' => 'sanas_card',
        'posts_per_page'  => -1,
        'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'sanas_show_homepage',
            'value' => '1',
            'compare' => '='
        )
    )        
      );
        if (!empty($terms) && !is_wp_error($terms)) {
          $i=1;
          foreach ($terms as $term): 
          $termss= $term->slug;
         ?>
          <div class="tab-pane fade" id="pills-<?php echo esc_attr($term->slug); ?>" role="tabpanel" aria-labelledby="pills-<?php echo esc_attr($term->slug); ?>">
            <div class="row" id="cardBoxContainer">
            <?php
            $query = new \WP_Query($args);
            if ($query->have_posts()) {
            while ($query->have_posts()) {
            $query->the_post();
            $category = get_the_terms( get_the_ID(),'sanas-card-category' );
            $categoryName = $category[0]->name;
            if (get_post_meta(get_the_ID(),'sanas_metabox',true)) {
              $sanas_portfolio_meta = get_post_meta(get_the_ID(),'sanas_metabox',true);
            }
            else {
              $sanas_portfolio_meta = array();
            }

            ?>
             <?php
              if ($category != false) {
                foreach ($category as $categorys) {
                 $sluglink = $categorys->slug; 
                 if($sluglink == $termss){

                $currentURL = site_url();
                $dashQuery = '/user-dashboard';
                $dashpage = '/?dashboard=cover';
                // Determine the correct permalink structure
                global $wp_rewrite;
                if ($wp_rewrite->permalink_structure == '') {
                    $perma = "&";
                } else {
                    $perma = "/";
                }

                // Construct the URL with proper formatting
                $dashboardURL = esc_url($currentURL .'/'. $dashQuery . $dashpage. '&card_id='.get_the_id()  );

                if($sanas_portfolio_meta['sanas_bg_color'])
                {
                    $bg_color='style="background:'.$sanas_portfolio_meta['sanas_bg_color'].'"';
                }
                ?>
                  <div class="card-box col-lg-3 col-md-4 col-sm-6" data-card-id="<?php echo get_the_ID(); ?>">
                    <div class="inner-box" >
                      <a href="<?php echo $dashboardURL;?>" class="flip-container" <?php echo $bg_color;?>>
                        <div class="flipper">
                          <div class="front">
                            <img src="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_front_Image']['url']) ?>" alt="template">
                          </div>
                          <div class="middel-card">
                            <img src="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_back_Image']['url']) ?>" alt="template">
                          </div>
                          <div class="back">
                            <img src="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_back_Image']['url']) ?>" alt="template">
                          </div>
                        </div>
                      </a>
                      <div class="lower-content">
                        <a href="<?php echo $dashboardURL; ?>" class="card-box-title"><h4><?php echo get_the_title();?></h4></a>
                        <a href="<?php echo $dashboardURL; ?>">Free</a>
                        <?php if (is_user_logged_in()) : ?>
                          <div class="heart-icon <?php echo $is_in_wishlist ? 'active' : ''; ?>" 
                            data-card-id="<?php echo get_the_ID(); ?>" 
                            data-is-in-wishlist="<?php echo $is_in_wishlist ? 'true' : 'false'; ?>">
                            <i class="icon-Heart"></i>
                          </div>
                        <?php else : ?>
                          <div class="heart-icon sanas-login-popup">
                            <i class="icon-Heart"></i>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                 <div class="card-box col-lg-4 col-md-6 col-sm-12" style="display: none;">
                <div class="inner-box">
                  <div class="image">
                <?php 
                $currentURL = site_url();
                $dashQuery = '/user-dashboard';
                $dashpage = '/?dashboard=cover';
                // Determine the correct permalink structure
                global $wp_rewrite;
                if ($wp_rewrite->permalink_structure == '') {
                    $perma = "&";
                } else {
                    $perma = "/";
                }
                // Construct the URL with proper formatting
                $dashboardURL = esc_url($currentURL . '/'. $dashQuery . $dashpage. '&card_id='.get_the_id()  );
                ?>
                    <a href="<?php echo $dashboardURL; ?>">
                        <img class="front-side" src="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_front_Image']['url']) ?>" alt="blog">
                        <img class="back-side" src="<?php echo esc_url($sanas_portfolio_meta['sanas_upload_back_Image']['url']) ?>" alt="blog">
                    </a>
                  </div>
                  <div class="lower-content">
                    <h4><?php echo get_the_title() ?></h4>
                    <a href="<?php echo get_the_permalink() ?>">Free</a>
                  </div>
                </div>
              </div> 
              <?php } }
              }
              ?>
            <?php }  } ?>        
            </div>
          </div>
         <?php $i++; endforeach; } ?>
        </div>
      </div>
    </section>
        <?php
    }
}
Plugin::instance()->widgets_manager->register_widget_type(new Sanas_card_Widget());