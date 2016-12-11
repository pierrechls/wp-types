<?php
/**
 *
 *
 */
if (is_admin()) {
    ?>
    <div class="js-wpt-field-item wpt-field-item">
        <?php echo $out; ?>
        <?php if (@$cfg['repetitive']): ?>
            <div class="wpt-repctl">
                <div class="js-wpt-repdrag wpt-repdrag">&nbsp;</div>
                <a class="js-wpt-repdelete button button-small" data-wpt-type="<?php echo $cfg['type']; ?>" data-wpt-id="<?php echo $cfg['id']; ?>"><?php apply_filters('toolset_button_delete_repetition_text', printf(__('Delete %s', 'wpv-views'), strtolower($cfg['title'])), $cfg); ?></a>
            </div>
        <?php endif; ?>
    </div>
    <?php
} else {
    $toolset_repdrag_image = '';
    $button_extra_classnames = '';
    if ($cfg['repetitive']) {
        $toolset_repdrag_image = apply_filters('wptoolset_filter_wptoolset_repdrag_image', $toolset_repdrag_image);
        echo '<div class="wpt-repctl">';
        echo '<span class="js-wpt-repdrag wpt-repdrag"><img class="wpv-repdrag-image" src="' . $toolset_repdrag_image . '" /></span>';
    }
    echo $out;
    if ($cfg['repetitive']) {
        if (array_key_exists('use_bootstrap', $cfg) && $cfg['use_bootstrap']) {
            $button_extra_classnames = ' btn btn-default btn-sm';
        }
        $str = sprintf(__('%s repetition', 'wpv-views'), $cfg['title']);
        echo '<input type="button" href="#" class="js-wpt-repdelete wpt-repdelete' . $button_extra_classnames . '" value="';
        echo apply_filters('toolset_button_delete_repetition_text', esc_attr(__('Delete', 'wpv-views')) . " " . esc_attr($str), $cfg);
        echo '" />';
        echo '</div>';
    }
}
