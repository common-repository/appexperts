<?php
class App_Expert_Taxonomy_Image_Helper {
    // get taxonomy image url for the given term_id (Place holder image by default)
    public static function get_taxonomy_image_url($term_id = NULL, $size = 'full', $return_placeholder = FALSE) {
        if (!$term_id) {
            if (is_category())
                $term_id = get_query_var('cat');
            elseif (is_tag())
                $term_id = get_query_var('tag_id');
            elseif (is_tax()) {
                $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                $term_id = $current_term->term_id;
            }
        }

        $taxonomy_image_url = get_option('ae_taxonomy_image'.$term_id);
        if(!empty($taxonomy_image_url)) {
            $attachment_id = self::get_attachment_id_by_url($taxonomy_image_url);
            if(!empty($attachment_id)) {
                $taxonomy_image_url = wp_get_attachment_image_src($attachment_id, $size);
                $taxonomy_image_url = $taxonomy_image_url[0];
            }
        }
        //todo : find a better way
        $aeci_placeholder = APP_EXPERT_URL . 'includes/integrations/wordpress/assets/images/placeholder.png';
        if ($return_placeholder)
            return ($taxonomy_image_url != '') ? $taxonomy_image_url : $aeci_placeholder;
        else
            return $taxonomy_image_url;
    }

    // get attachment ID by image url
    public static function get_attachment_id_by_url($image_src) {
        global $wpdb;
        $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = %s", $image_src);
        $id = $wpdb->get_var($query);
        return (!empty($id)) ? $id : NULL;
    }
    public static function get_taxonomy_image($term_id = NULL, $size = 'full', $attr = NULL, $echo = TRUE) {
        if (!$term_id) {
            if (is_category())
                $term_id = get_query_var('cat');
            elseif (is_tag())
                $term_id = get_query_var('tag_id');
            elseif (is_tax()) {
                $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                $term_id = $current_term->term_id;
            }
        }

        $taxonomy_image_url = get_option('ae_taxonomy_image'.$term_id);
        if(!empty($taxonomy_image_url)) {
            $attachment_id = self::get_attachment_id_by_url($taxonomy_image_url);
            if(!empty($attachment_id))
                $taxonomy_image = wp_get_attachment_image($attachment_id, $size, FALSE, $attr);
            else {
                $image_attr = '';
                if(is_array($attr)) {
                    if(!empty($attr['class']))
                        $image_attr .= ' class="'.$attr['class'].'" ';
                    if(!empty($attr['alt']))
                        $image_attr .= ' alt="'.$attr['alt'].'" ';
                    if(!empty($attr['width']))
                        $image_attr .= ' width="'.$attr['width'].'" ';
                    if(!empty($attr['height']))
                        $image_attr .= ' height="'.$attr['height'].'" ';
                    if(!empty($attr['title']))
                        $image_attr .= ' title="'.$attr['title'].'" ';
                }
                $taxonomy_image = '<img src="'.$taxonomy_image_url.'" '.$image_attr.'/>';
            }
        }
        else{
            $taxonomy_image = '';
        }

        if ($echo)
            echo $taxonomy_image;
        else
            return $taxonomy_image;
    }
    public static function is_taxonomy_image_enabled($taxonomy) {
        $check = false;
        $aeci_options = get_option('aeci_options');

        if (!is_array($aeci_options)){
            $aeci_options = array();
        }

        if (empty($aeci_options['included_taxonomies'])){
            $aeci_options['included_taxonomies'] = array();
        }

        if (in_array($taxonomy, $aeci_options['included_taxonomies'])) {
            $check = true;
        }

        return $check;
    }
}
