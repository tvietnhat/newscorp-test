<?php
class FB_Post_Importer {
	public function import_json($file_path) {
		global $wpdb;
		
		$import_results = array();

		$string = file_get_contents($file_path);
		$json_a = json_decode($string, true);
		
		$stories_a = !empty($json_a) && isset($json_a['data']) ? $json_a['data'] : NULL;
		
		if ( !empty($stories_a) ) {
			$posts_by_fb_id = array();
			foreach ($stories_a as $story) {
				$story_id = $story['id'];
				
				// check if the story has been imported earlier
				$query = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_fb_id' AND meta_value=%s", $story_id );
				$post_id = $wpdb->get_var($query);

				$post_arr = array(
					'post_title' => $story['name'],
					'post_content' => $story['message'],
					'post_date' => $story['created_time'],
					'post_modified' => $story['updated_time'],
					'post_status' => $story['is_hidden'] ? 'draft' : 'publish',
					'meta_input' => array(
						'_fb_id' => $story_id,
						'_fb_picture' => $story['picture'],
						'_fb_raw' => json_encode($story),
					),
				);
				
				if ( !empty($post_id) ) $post_arr['ID'] = $post_id;
				
				$import_results[] = wp_insert_post($post_arr);
			}
		}
	}
}