<?php
class App_Expert_Term_Meta_Data {

    /**
     * @param string $term_name
     * @param array $data
     *
     * @return array|int|WP_Error
     */
    public function get_term_data(string $term_name , array $data){
        return get_terms(
            array(
                'taxonomy' => $term_name,
                'include' => $data
            )
        );
    }

}

