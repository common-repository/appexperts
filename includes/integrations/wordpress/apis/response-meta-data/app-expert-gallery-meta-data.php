<?php
class App_Expert_Gallery_Meta_Data
{

    public function __construct(){
        add_filter('ae_post_response',[$this,'attach_data']);
    }


    public  function attach_data(WP_REST_Response $response){
        $data = $response->get_data();
        if (!isset($data['id']) || empty($data['id'])) return $response;

        $data['has_gallery'] = false;
        if (!function_exists('parse_blocks')) {
            $data['gallery'] = 	[
                'total' => 0,
                'images' => []
            ];
            $response->set_data($data);
            return $response;
        }

        $all_img_ids  = [];
        $all_img_captions  = [];
        $all_videos = [];

        $post = get_post($data['id']);
        foreach (parse_blocks($post->post_content) as $index => $block) {
            switch ($block['blockName']):
                case 'core/gallery':
                    $block_img_captions = $this->parse_gallery_block_captions($block['innerHTML']);
                    if(isset($block['attrs']['ids'])) $all_img_ids = array_merge($all_img_ids, $block['attrs']['ids']);
                    else {
                        foreach ($block['innerBlocks'] as $inner_block){
                            $all_img_ids[]=$inner_block['attrs']['id'];
                        }
                    }
                    $all_img_captions = array_merge($all_img_captions, $block_img_captions);
                    break;

                case 'core/video':
                    $video_data = $this->parse_video_block($block['innerHTML']);
                    $all_videos[] = $video_data;
                    break;


            endswitch;
        }

        $gallery = $this->get_gallery_array($all_img_ids, $all_img_captions);
        $videos =  $this->get_video_array($all_videos);


        $data['gallery'] = $gallery;
        $data['videos'] = $videos;
        $response->set_data($data);
        return $response;
    }


    public function get_gallery_array($all_img_ids, $all_img_captions)
    {
        $gallery = [];
        $has_next = false;
        $image_ids = $this->apply_pagination($all_img_ids, $has_next);
        $img_captions = $this->apply_pagination($all_img_captions, $has_next);
        foreach ($image_ids as $image_index => $image_id) {
            $img_data = wp_get_attachment_metadata($image_id);
            $attachment = get_post($image_id);
            $thumbnail = wp_get_attachment_image_url($image_id, 'thumbnail');
            $medium = $large = $thumbnail;

            if (isset($img_data['sizes']['medium'])) {
                $medium = str_replace($img_data['sizes']['thumbnail']['file'], $img_data['sizes']['medium']['file'], $thumbnail);
                $large = $medium;
            }
            if (isset($img_data['sizes']['large'])) {
                $large = str_replace($img_data['sizes']['thumbnail']['file'], $img_data['sizes']['large']['file'], $thumbnail);
            }

            $gallery[] = [
                'details' => $attachment,
                'caption' => $img_captions[$image_index] ?? "",
                'sizes' => [
                    'thumbnail' => $thumbnail,
                    'medium' 	=> $medium,
                    'large' 	=> $large,
                    'full'		=> $attachment->guid ? $attachment->guid : $large
                ]
            ];
        }

        return [
            'total' => count($all_img_ids),
            'has_next' => $has_next,
            'data' => $gallery
        ];

    }

    public function get_video_array($all_videos)
    {
        $has_next = false;
        $videos =  $this->apply_pagination($all_videos, $has_next);
        return [
            'total' => count($all_videos),
            'has_next' => $has_next,
            'data' => $videos
        ];
    }


    /**
     * Apply pagination on the given array using gallery pagination query variables
     */
    protected function apply_pagination($data_array, &$has_next)
    {

        $page = isset($_GET['gallery_page']) ? intval($_GET['gallery_page']) : 1;
        $limit = isset($_GET['gallery_per_page']) ? intval($_GET['gallery_per_page']) : 20;

        $offset = ($page - 1) * $limit;
        $array_part = array_slice($data_array, $offset, $limit);
        $has_next = (count($data_array) > ($offset + $limit));
        return array_values($array_part); // fix indices
    }

    protected function parse_gallery_block_captions($content)
    {
        $captions = [];
        $gallery_content = $this->get_string_part($content, "<li", "</ul>");
        $img_tags = explode('</li>', $gallery_content);
        foreach ($img_tags as $img_tag) {
            $captions[] = strip_tags($img_tag);
        }
        return $captions;
    }

    protected function parse_video_block($content){

        preg_match( '@src="([^"]+)"@' , $content, $match);
        $src = array_pop($match);

        $caption_part = $this->get_string_part($content, "<figure", "</figcaption>");
        $caption = strip_tags($caption_part);

        return [
            'url' => $src,
            'caption' => $caption
        ];
    }

    protected function get_string_part($string, $startStr, $endStr)
    {
        $startpos = strpos($string, $startStr);
        $endpos = strpos($string, $endStr, $startpos);
        $endpos = $endpos - $startpos;
        $string = substr($string, $startpos, $endpos);

        return $string;
    }
}
new App_Expert_Gallery_Meta_Data();