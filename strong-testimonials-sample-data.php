<?php
/**
 * Plugin Name: Strong Testimonials - Sample Data
 * Plugin URI: https://strongplugins.com
 * Description: Sample data for the Strong Testimonials plugin.
 * Author: Chris Dillon
 * Version: 0.8.1
 * Author URI: https://strongplugins.com
 * Text Domain: strong-testimonials-sample-data
 * Requires: 3.3 or higher
 * License: GPLv3 or later
 *
 * Copyright 2015-2017  Chris Dillon  chris@strongplugins.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class Strong_Testimonials_Sample_Data {

	/**
	 * Custom upload directory
	 *
	 * @param array $uploads
	 *
	 * @return array
	 */
	public static function custom_upload_dir( $uploads ) {
		$uploads['subdir'] = '/testimonials';
		$uploads['path']   = $uploads['basedir'] . $uploads['subdir'];
		$uploads['url']    = $uploads['baseurl'] . $uploads['subdir'];

		return $uploads;
	}

	/**
	 * Insert the testimonials.
	 */
	public static function insert_posts() {

		add_filter( 'upload_dir', array( 'Strong_Testimonials_Sample_Data', 'custom_upload_dir' ) );

		$posts = self::get_posts();
		$now = time();
		$date_format = 'Y-m-d H:i:s';

		$i = 12;
		foreach ( $posts as $apost ) {
			$post = $apost['post'];

			$i--;
			$newtime = $now - $i * 60 * 60 * 24 * 7; // 1 week

			$post['post_date'] = date( $date_format, $newtime );
			$post['post_date_gmt'] = gmdate( $date_format, $newtime );

			$existing_post = get_page_by_title( $post['post_title'], OBJECT, 'wpm-testimonial' );

			if ( null === $existing_post ) {

				$post_id = wp_insert_post( $post, true );

			} else {

				$post_id = wp_update_post( $existing_post, true );
				delete_post_thumbnail( $existing_post );

			}

			if ( is_wp_error( $post_id ) || ! 0 === $post_id ) {
				error_log( print_r( $post_id, true ) );
			}

			// Add client fields
			if ( isset( $apost['meta'] ) && !empty( $apost['meta'] ) ) {
				self::add_meta( $post_id, $apost['meta'] );
			}

			// Add thumbnail image
			if ( isset( $apost['thumbnail'] ) && !empty( $apost['thumbnail'] ) ) {
				self::add_thumbnail( $post_id, $apost['thumbnail']['name'] );
			}

		}

		remove_filter( 'upload_dir', array( 'Strong_Testimonials_Sample_Data', 'custom_upload_dir' ) );

	}

	/**
	 * @param $post_id
	 * @param $fields
	 *
	 * @return bool
	 */
	public static function add_meta( $post_id, $fields ) {
		foreach ( $fields as $key => $value ) {
			$meta_id = update_post_meta( $post_id, $key, $value );
			if ( !$meta_id )
				return false;
		}
		return true;
	}


	/**
	 * Copy thumbnails to uploads directory and attach to this post.
	 * Thanks https://tommcfarlin.com/upload-files-wordpress-media-library/
	 *
	 * @param $post_id
	 * @param $filename
	 *
	 * @return bool
	 */
	public static function add_thumbnail( $post_id, $filename ) {
		// Locate the file.
		$file = plugin_dir_path( __FILE__ ) . 'images/' . $filename;
		if ( !file_exists( $file ) || 0 === strlen( trim( $filename ) ) ) {
			error_log( 'The file you are attempting to upload, ' . $file . ', does not exist.' );
			return false;
		}

		// Get the uploads directory info.
		$uploads = wp_upload_dir();
		$uploads_dir = $uploads['path'];
		$uploads_url = $uploads['url'];

		// Copy the thumbnail.
		copy( $file, trailingslashit( $uploads_dir ) . $filename );

		// Add to Media Library and attach to this post.
		$url = trailingslashit( $uploads_url ) . $filename;
		$result = media_sideload_image( $url, $post_id, $filename );
		if ( is_wp_error( $result ) ) {
			error_log( print_r( $result, true ) );
			return false;
		}

		// Find this new attachment and set as thumbnail.
		$media = get_attached_media( 'image', $post_id );
		foreach ( $media as $media_id => $media_post ) {
			if ( $media_post->post_title == $filename ) {
				set_post_thumbnail( $post_id, $media_id );
				break;
			}
		}

		return true;
	}


	/**
	 * @return array
	 */
	public static function get_posts() {

		$posts = array();

		$posts[] = array(
			'post' => array(
				'post_content' => 'We have worked with Kevin in the past and we recommend him whole-heartedly! The most important thing for us is that he listens. The last project he worked with us on was to organize QuickBooks. We had no clue where to start! We only knew we needed a simple, straightforward process that just worked so we could focus on our business. Kevin tailored a plan for us and walked us through it. He delivered on time with no issues.',
				'post_title' => 'He delivered on time',
				'post_excerpt' => 'We recommend him whole-heartedly!',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-07.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Hitaka Nakamura',
				'email' => '',
				'company_name' => 'Komorebi Studios',
				'company_website' => 'https://demos.strongplugins.com',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'We have worked with many accountants over the years. Kevin is a cut above and delivers the quality that we need. His attention to detail is what separates him from the rest. I highly recommend him.',
				'post_title' => 'A cut above',
				'post_excerpt' => 'His attention to detail is what separates him from the rest.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-01.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Aatik Tasneem',
				'email' => '',
				'company_name' => 'The EXIM Conference',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Most accountants do a good job but Kevin goes the extra mile and makes sure he delivers quality work every time. I know I can count on him for my company.',
				'post_title' => 'The extra mile',
				'post_excerpt' => 'I know I can count on him.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-05.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Ellen Bradley',
				'email' => '',
				'company_name' => 'I Heart Gardens',
				'company_website' => 'https://demos.strongplugins.com',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'When I was starting up my business I didn’t know where to begin. A friend suggested I see Kevin and I have never regretted it. Kevin sat me down and we talked about what I needed and what he could provide for me. He is a trustworthy advisor, a confidant, a patient soul who takes the time to understand your history, challenges, and dreams. He is a master of keeping everything in context, as you would expect from someone with his level of experience. I would recommend him to anyone starting up a business.',
				'post_title' => 'A trustworthy advisor',
				'post_excerpt' => 'I would recommend him to anyone starting up a business.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-10.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Finn',
				'email' => '',
				'company_name' => 'Beer Nation',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Kevin gives us practical advice and shows us how we could be doing things in a more cost efficient way. He is a good guy to have around in a crisis and it is reassuring to know that he is in our corner. Ask him about the time we almost set fire to my office!',
				'post_title' => 'A good guy to have around',
				'post_excerpt' => 'He is a good guy to have around in a crisis.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-06.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Justine',
				'email' => '',
				'company_name' => 'Butterfly Candles',
				'company_website' => 'https://demos.strongplugins.com',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'The end of every year was always a big headache for us. Bringing in Kevin was smooth and painless. He just made it happen for us. As an NGO expert, he knew exactly what we needed and put everyone\'s mind at ease. I highly recommended this accounting master!',
				'post_title' => 'No more headache',
				'post_excerpt' => 'Kevin just made it happen for us.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-02.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Mbele Oyelowo',
				'email' => '',
				'company_name' => '',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Luckily we found Kevin and he was able to step in really quickly and take over the whole payroll issue for us.',
				'post_title' => 'Ended our payroll nightmare',
				'post_excerpt' => 'He was able to step in really quickly.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-08.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Luke',
				'email' => '',
				'company_name' => 'Birchley Advertising',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Kevin is a total professional. I had used him before for some accounting work, but when our company had a paperwork mini-crisis he worked hard and fast with us to pull things around.',
				'post_title' => 'A total professional',
				'post_excerpt' => 'He worked hard and fast with us to pull things around.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-04.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Jessie',
				'email' => '',
				'company_name' => 'The Cherrywood Shop',
				'company_website' => 'https://demos.strongplugins.com',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Kevin organized everything and he made our paperwork process more efficient so we can focus on our business.',
				'post_title' => 'Organized everything',
				'post_excerpt' => 'Kevin organized everything.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-03.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Tina Clark',
				'email' => '',
				'company_name' => 'Clark Restaurant Supply',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'We had an accountant working in-house for us, which was a disaster. He hadn’t input any of the figures from our receipts for our business purchases for the whole year. End of year taxes had to be done and he had no figures for this. We called Kevin who came in to help. He sorted out the whole mess and made sure everything was good to go. He has done all our accounting ever since. Kevin rocks!',
				'post_title' => 'Sorted out the whole mess',
				'post_excerpt' => 'He made sure everything was good to go.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'photo-09.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Juanita Espinosa',
				'email' => '',
				'company_name' => '',
				'company_website' => 'http://mariposadesign.com',
			),
		);

		return $posts;
	}

}

register_activation_hook( __FILE__, array( 'Strong_Testimonials_Sample_Data', 'insert_posts' ) );
