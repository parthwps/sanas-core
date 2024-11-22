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

        ?>
        <section class="tab-section">
            <div class="container">
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'sanas-card-category',
                        'hide_empty' => false,
                        'number' => 3,
                    ));
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $j = 0;
                        foreach ($terms as $term): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php if($j == 0) { echo 'active'; } ?>" id="pills-<?php echo esc_attr($term->slug); ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-<?php echo esc_attr($term->slug); ?>" type="button" role="tab" aria-controls="pills-<?php echo esc_attr($term->slug); ?>" aria-selected="true">
                                    <?php echo esc_html($term->name); ?>
                                </button>
                            </li>
                        <?php $j++; endforeach;
                    } else {
                        echo '<li class="nav-item">No categories found.</li>';
                    }
                    ?>
                </ul>
                <div class="all-tamplate">
                    <a href="filter.html" class="all-tamplate-btn">View all categories <i class="icon-chevron-down"></i></a>
                </div>
                <div class="tab-content" id="pills-tabContent">
                    <?php if (!empty($terms) && !is_wp_error($terms)) {
                        $i = 0;
                        foreach ($terms as $term):
                            $term_slug = $term->slug;
                            ?>
                            <div class="tab-pane fade <?php if($i == 0) { echo 'show active'; } ?>" id="pills-<?php echo esc_attr($term_slug); ?>" role="tabpanel" aria-labelledby="pills-<?php echo esc_attr($term_slug); ?>">
                                <div class="row" id="cardBoxContainer">
                                    <?php
                                    $query = new \WP_Query($args);
                                    if ($query->have_posts()) {
                                        while ($query->have_posts()) {
                                            $query->the_post();
                                            $category = get_the_terms(get_the_ID(), 'sanas-card-category');
                                            $categoryName = $category[0]->name;
                                            $sanas_portfolio_meta = get_post_meta(get_the_ID(), 'sanas_metabox', true) ?: [];

                                            // Check if the card is in the user's wishlist
                                            $is_in_wishlist = $wpdb->get_var($wpdb->prepare(
                                                "SELECT id FROM {$wpdb->prefix}sanas_wishlist WHERE user_id = %d AND card_id = %d",
                                                get_current_user_id(),
                                                get_the_ID()
                                            ));

                                            if ($category) {
                                                foreach ($category as $categorys) {
                                                    if ($categorys->slug == $term_slug) {
                                                        $dashboardURL = esc_url(site_url() . "/user-dashboard/?dashboard=cover&card_id=" . get_the_ID());

                                                        $bg_color = $sanas_portfolio_meta['sanas_bg_color'] ? 'style="background:' . $sanas_portfolio_meta['sanas_bg_color'] . '"' : '';
                                                        ?>
                                                        <div class="card-box col-lg-3 col-md-4 col-sm-6">
                                                            <div class="inner-box">
                                                                <a href="<?php echo $dashboardURL; ?>" class="flip-container" <?php echo $bg_color; ?>>
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
                                                                    <a href="<?php echo $dashboardURL; ?>" class="card-box-title"><h4><?php echo get_the_title(); ?></h4></a>
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
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    wp_reset_postdata();
                                    ?>
                                </div>
                            </div>
                            <?php $i++; endforeach;
                    } ?>
                </div>
            </div>
        </section>
        <?php
    }
}
Plugin::instance()->widgets_manager->register_widget_type(new Sanas_card_Widget());
