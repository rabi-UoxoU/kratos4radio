<?php
/**
 * Kratos4Radio Child Theme Functions
 *
 * Adds Status post format support to the Kratos parent theme.
 */

// Register Status post format support
add_action( 'after_setup_theme', function () {
    add_theme_support( 'post-formats', array( 'status' ) );
} );

/**
 * Shared query args for the child-theme aggregation widget.
 *
 * @param int|string $number Number of posts to fetch.
 * @return array<string, mixed>
 */
function kratos4radio_aggregate_query_args( $number ) {
    return array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => (int) $number,
        'ignore_sticky_posts' => true,
        'tax_query'           => array(
            array(
                'taxonomy' => 'post_format',
                'field'    => 'slug',
                'terms'    => array( 'post-format-status' ),
                'operator' => 'NOT IN',
            ),
        ),
    );
}

if ( class_exists( 'WP_Widget' ) && ! class_exists( 'Kratos4Radio_Widget_Posts' ) ) {
    /**
     * Child-theme widget: article aggregation without status-format posts.
     */
    class Kratos4Radio_Widget_Posts extends WP_Widget {
        public function __construct() {
            parent::__construct(
                'kratos4radio_widget_posts',
                __( 'Kratos4Radio - 文章聚合', 'kratos' ),
                array(
                    'classname'   => 'widget_posts',
                    'description' => __( '展示最热、随机、最新文章的工具（忽略 Status）', 'kratos' ),
                )
            );
        }

        /**
         * @param array<string, mixed> $args
         * @param array<string, mixed> $instance
         */
        public function widget( $args, $instance ) {
            $number = ! empty( $instance['number'] ) ? $instance['number'] : '6';
            $days   = ! empty( $instance['days'] ) ? $instance['days'] : '30';
            $order  = ! empty( $instance['order'] ) ? $instance['order'] : 'hot';

            $widget_id = 'kratos4radio-widget-' . esc_attr( $this->number );
            $new_posts = get_posts( kratos4radio_aggregate_query_args( $number ) );
            $hot_posts = get_posts(
                kratos4radio_aggregate_query_args( $number ) + array(
                    'orderby'    => 'comment_count',
                    'order'      => 'DESC',
                    'date_query' => array(
                        array(
                            'after'     => (int) $days . ' days ago',
                            'before'    => 'now',
                            'inclusive' => true,
                        ),
                    ),
                )
            );
            $random_posts = get_posts(
                kratos4radio_aggregate_query_args( $number ) + array(
                    'orderby' => 'rand',
                )
            );

            echo '<div class="widget w-recommended">';
            ?>
            <div class="nav nav-tabs d-none d-xl-flex" id="<?php echo $widget_id; ?>-nav" role="tablist">
                <a class="nav-item nav-link <?php echo ( $order == 'new' ) ? 'active' : ''; ?>" id="<?php echo $widget_id; ?>-new-tab" data-toggle="tab" href="#<?php echo $widget_id; ?>-new" role="tab" aria-controls="<?php echo $widget_id; ?>-new" aria-selected="<?php echo ( $order == 'new' ) ? 'true' : 'false'; ?>"><i class="kicon i-tabnew"></i><?php _e( '最新', 'kratos' ); ?></a>
                <a class="nav-item nav-link <?php echo ( $order == 'hot' ) ? 'active' : ''; ?>" id="<?php echo $widget_id; ?>-hot-tab" data-toggle="tab" href="#<?php echo $widget_id; ?>-hot" role="tab" aria-controls="<?php echo $widget_id; ?>-hot" aria-selected="<?php echo ( $order == 'hot' ) ? 'true' : 'false'; ?>"><i class="kicon i-tabhot"></i><?php _e( '热点', 'kratos' ); ?></a>
                <a class="nav-item nav-link <?php echo ( $order == 'random' ) ? 'active' : ''; ?>" id="<?php echo $widget_id; ?>-random-tab" data-toggle="tab" href="#<?php echo $widget_id; ?>-random" role="tab" aria-controls="<?php echo $widget_id; ?>-random" aria-selected="<?php echo ( $order == 'random' ) ? 'true' : 'false'; ?>"><i class="kicon i-tabrandom"></i><?php _e( '随机', 'kratos' ); ?></a>
            </div>
            <div class="nav nav-tabs d-xl-none" id="<?php echo $widget_id; ?>-nav-mobile" role="tablist">
                <a class="nav-item nav-link <?php echo ( $order == 'new' ) ? 'active' : ''; ?>" id="<?php echo $widget_id; ?>-new-tab-mobile" data-toggle="tab" href="#<?php echo $widget_id; ?>-new" role="tab" aria-controls="<?php echo $widget_id; ?>-new" aria-selected="<?php echo ( $order == 'new' ) ? 'true' : 'false'; ?>"><?php _e( '最新', 'kratos' ); ?></a>
                <a class="nav-item nav-link <?php echo ( $order == 'hot' ) ? 'active' : ''; ?>" id="<?php echo $widget_id; ?>-hot-tab-mobile" data-toggle="tab" href="#<?php echo $widget_id; ?>-hot" role="tab" aria-controls="<?php echo $widget_id; ?>-hot" aria-selected="<?php echo ( $order == 'hot' ) ? 'true' : 'false'; ?>"><?php _e( '热点', 'kratos' ); ?></a>
                <a class="nav-item nav-link <?php echo ( $order == 'random' ) ? 'active' : ''; ?>" id="<?php echo $widget_id; ?>-random-tab-mobile" data-toggle="tab" href="#<?php echo $widget_id; ?>-random" role="tab" aria-controls="<?php echo $widget_id; ?>-random" aria-selected="<?php echo ( $order == 'random' ) ? 'true' : 'false'; ?>"><?php _e( '随机', 'kratos' ); ?></a>
            </div>
            <div class="tab-content" id="<?php echo $widget_id; ?>-content">
                <div class="tab-pane fade <?php echo ( $order == 'new' ) ? 'show active' : ''; ?>" id="<?php echo $widget_id; ?>-new" role="tabpanel" aria-labelledby="<?php echo $widget_id; ?>-new-tab">
                    <?php foreach ( $new_posts as $post ) : ?>
                        <a class="bookmark-item" rel="bookmark" title="<?php echo esc_attr( strip_tags( $post->post_title ) ); ?>" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><i class="kicon i-book"></i><?php echo esc_html( $post->post_title ); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane fade <?php echo ( $order == 'hot' ) ? 'show active' : ''; ?>" id="<?php echo $widget_id; ?>-hot" role="tabpanel" aria-labelledby="<?php echo $widget_id; ?>-hot-tab">
                    <?php foreach ( $hot_posts as $post ) : ?>
                        <a class="bookmark-item" rel="bookmark" title="<?php echo esc_attr( strip_tags( $post->post_title ) ); ?>" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><i class="kicon i-book"></i><?php echo esc_html( $post->post_title ); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane fade <?php echo ( $order == 'random' ) ? 'show active' : ''; ?>" id="<?php echo $widget_id; ?>-random" role="tabpanel" aria-labelledby="<?php echo $widget_id; ?>-random-tab">
                    <?php foreach ( $random_posts as $post ) : ?>
                        <a class="bookmark-item" rel="bookmark" title="<?php echo esc_attr( strip_tags( $post->post_title ) ); ?>" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><i class="kicon i-book"></i><?php echo esc_html( $post->post_title ); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
            echo '</div>';
        }

        /**
         * @param array<string, mixed> $new_instance
         * @param array<string, mixed> $old_instance
         * @return array<string, string>
         */
        public function update( $new_instance, $old_instance ) {
            return array(
                'number' => ! empty( $new_instance['number'] ) ? sanitize_text_field( $new_instance['number'] ) : '',
                'days'   => ! empty( $new_instance['days'] ) ? sanitize_text_field( $new_instance['days'] ) : '',
                'order'  => ! empty( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : '',
            );
        }

        /**
         * @param array<string, mixed> $instance
         */
        public function form( $instance ) {
            $number = ! empty( $instance['number'] ) ? $instance['number'] : '6';
            $days   = ! empty( $instance['days'] ) ? $instance['days'] : '30';
            $order  = ! empty( $instance['order'] ) ? $instance['order'] : 'hot';
            ?>
            <div class="media-widget-control">
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( '展示数量：', 'kratos' ); ?></label>
                    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" />
                </p>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>"><?php _e( '统计天数：', 'kratos' ); ?></label>
                    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'days' ) ); ?>" type="text" value="<?php echo esc_attr( $days ); ?>" />
                </p>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php _e( '默认显示：', 'kratos' ); ?></label>
                    <select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
                        <option value="new" <?php selected( $order, 'new' ); ?>><?php _e( '最新', 'kratos' ); ?></option>
                        <option value="hot" <?php selected( $order, 'hot' ); ?>><?php _e( '热点', 'kratos' ); ?></option>
                        <option value="random" <?php selected( $order, 'random' ); ?>><?php _e( '随机', 'kratos' ); ?></option>
                    </select>
                </p>
            </div>
            <?php
        }
    }
}

add_action(
    'widgets_init',
    function () {
        register_widget( 'Kratos4Radio_Widget_Posts' );
    },
    20
);
