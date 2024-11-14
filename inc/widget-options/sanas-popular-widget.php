<?php 
CSF::createWidget('sanas_popular_posts_widget', array(
    'title' => esc_html__('Sanas Popular Posts Widget', 'sanas-core'),
    'classname' => '',
    'description' => esc_html__('A widget which shows All Page', 'sanas-core'),
    'fields' => array(
        array(
            'id' => 'sanas_page_menu_widget_title',
            'title' => esc_html__('Title', 'sanas-core'),
            'type' => 'text',
            'default' => esc_html__('Website', 'sanas-core'),
        ),
        array(
        'id'      => 'sanas_widget_blog_number',
        'type'    => 'number',
        'title'   =>  esc_html__( 'Show Post', 'sanas-core' ),
        'default' => 2,
        ),
    ),
));
if (!function_exists('sanas_popular_posts_widget')) {
    function sanas_popular_posts_widget($args, $instance)
    {
        echo wp_kses_post($args['before_widget']);?>
        
            <h4 class="blog-sidebar-heading"><?php echo esc_html($instance['sanas_page_menu_widget_title']) ?></h4>
            <div class="blog-latest-news">
            <?php
            $posts = $instance['sanas_widget_blog_number'] ? $instance['sanas_widget_blog_number'] : '-1';
            query_posts('posts_per_page=' . $posts);
            while (have_posts()):
                the_post();?>
              <div class="blog-news">
                <div class="row">
                <?php if(has_post_thumbnail()): ?>
                  <div class="blog-news-img w-auto">
                    <img width="100" height="100" src="<?php echo get_the_post_thumbnail_url(); ?>" class="img-responsive wp-post-image"
                      alt="sanas">
                  </div>
                <?php endif; ?>
                  <div class="blog-news-dtl">
                    <h6 class="blog-news-dtl-heading mb-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                    <div class="date">
                      <p class="entry-date published"><?php echo get_the_date(); ?></p>
                    </div>
                  </div>
                </div>
              </div>
            <?php endwhile;
                wp_reset_query();?>
            </div>
            
       
       <?php echo wp_kses_post($args['after_widget']);
    }
}