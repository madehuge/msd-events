<?php
interface MSD_Meta_Box_Registrable {
    public function register_meta_boxes();
    public function save_event_meta( $post_id, $post );
}