<?php
/**
 * Single post template for Kratos4Radio child theme.
 * Routes status post format to a dedicated layout; falls back to parent theme for all other formats.
 *
 * @package Kratos4Radio
 */

if (get_post_format() !== 'status') {
    require get_template_directory() . '/single.php';
    return;
}

get_header();

$col_array = [
    'one_side' => 'col-lg-12',
    'two_side'  => 'col-lg-8',
];
$sidebar_setting = kratos_option('g_article_widgets', 'two_side');
$main_col        = $col_array[$sidebar_setting] ?? 'col-lg-8';
?>
<div class="k-main <?php echo kratos_option('top_img_switch', true) ? 'banner' : 'color'; ?>"
     style="background:<?php echo kratos_option('g_background', '#f5f5f5'); ?>">
    <div class="container">
        <div class="row">
            <div class="<?php echo $main_col; ?> details">
                <?php if (have_posts()) : the_post();
                    update_post_caches($posts); ?>

                    <div class="k-status k-status-single">
                        <div class="k-status-body">
                            <div class="k-status-content" id="lightgallery">
                                <?php the_content(); ?>
                            </div>
                            <div class="k-status-footer">
                                <span class="k-status-date"><?php echo get_the_date('Y年n月j日 H:i'); ?></span>
                                •
                                <span class="k-status-views"><?php echo get_post_views(); ?>次阅读</span>
                                •
                                <a class="k-status-love btn-thumbs<?php echo isset($_COOKIE['love_' . $post->ID]) ? ' done' : ''; ?>"
                                   data-action="love"
                                   data-id="<?php echo $post->ID; ?>"
                                   href="javascript:;">
                                    <?php echo get_post_meta($post->ID, 'love', true) ?: 0; ?>人点赞
                                </a>
                                •
                                <a class="k-status-comments"
                                   href="<?php echo get_permalink() . '#respond'; ?>">
                                    <?php echo get_comments_number(); ?>条评论
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                <nav class="navigation post-navigation clearfix" role="navigation">
                    <?php
                    $prev_post = get_previous_post();
                    if (!empty($prev_post)) {
                        echo '<div class="nav-previous clearfix"><a title="' . esc_attr($prev_post->post_title) . '" href="' . get_permalink($prev_post->ID) . '">' . __('< 上一篇', 'kratos') . '</a></div>';
                    }
                    $next_post = get_next_post();
                    if (!empty($next_post)) {
                        echo '<div class="nav-next"><a title="' . esc_attr($next_post->post_title) . '" href="' . get_permalink($next_post->ID) . '">' . __('下一篇 >', 'kratos') . '</a></div>';
                    }
                    ?>
                </nav>

                <?php comments_template(); ?>
            </div>

            <?php if ($sidebar_setting === 'two_side') : ?>
                <div class="col-lg-4 sidebar sticky-sidebar d-none d-lg-block">
                    <?php dynamic_sidebar('single_sidebar'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
