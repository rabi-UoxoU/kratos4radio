<?php
/**
 * Template part for displaying status post format in list/archive views.
 *
 * @package Kratos4Radio
 */

global $post;
?>
<div class="k-status">
    <div class="k-status-body">
        <div class="k-status-content">
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
