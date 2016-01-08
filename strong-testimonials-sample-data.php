<?php
/**
 * Plugin Name: Strong Testimonials Sample Data
 * Plugin URI: http://www.wpmission.com
 * Description: Sample data for the Strong Testimonials plugin.
 * Author: Chris Dillon
 * Version: 0.3
 * Author URI: http://www.wpmission.com
 * Text Domain: strong-testimonials-sample-data
 * Requires: 3.3 or higher
 * License: GPLv3 or later
 *
 * Copyright 2015  Chris Dillon  chris@wpmission.com
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

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'insert_posts' ) );
	}

	public function insert_posts() {

		$posts = $this->get_posts();

		foreach ( $posts as $apost ) {
			$post = $apost['post'];
			$post['post_date'] = date( 'Y-m-d H:i:s' );
			$post['post_date_gmt'] = gmdate( 'Y-m-d H:i:s' );

			if ( null == get_page_by_title( $post['post_title'], OBJECT, 'wpm-testimonial' ) ) {

				// Create testimonial post
				$post_id = wp_insert_post( $post, true );
				if ( is_wp_error( $post_id ) ) {
					error_log( print_r( $post_id, true ) );
				}

				// Add client fields
				if ( isset( $apost['meta'] ) && !empty( $apost['meta'] ) ) {
					$success = $this->add_meta( $post_id, $apost['meta'] );
				}

				// Add thumbnail image
				if ( isset( $apost['thumbnail'] ) && !empty( $apost['thumbnail'] ) ) {
					$success = $this->add_thumbnail( $post_id, $apost['thumbnail']['name'] );
				}

				sleep( 5 );
			}

		}

	}

	public function add_meta( $post_id, $fields ) {
		foreach ( $fields as $key => $value ) {
			$meta_id = add_post_meta( $post_id, $key, $value );
			if ( !$meta_id )
				return false;
		}
		return true;
	}

	// Copy thumbnails to uploads directory and attach to this post.
	// Thanks https://tommcfarlin.com/upload-files-wordpress-media-library/
	public function add_thumbnail( $post_id, $filename ) {
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


	public function get_posts() {

		$posts = array();

		/*
		$posts[] = array(
			'post' => array(
				'post_content'   => '',
				'post_name'      => '',
				'post_title'     => '',
				'post_excerpt'   => '',
				'post_type'      => 'wpm-testimonial',
				'post_status'    => 'publish',
			),
			'thumbnail' => array(
				'name' => '',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name'     => '',
				'email'           => '',
				'company_name'    => '',
				'company_website' => '',
			),
		);
		*/

		$posts[] = array(
			'post' => array(
				'post_content' => 'Kevin is a total professional. I had used him before for some accounting work, but when our company had a paperwork mini-crisis he worked hard and fast with us to pull things around.',
				'post_name' => 'a-total-professional',
				'post_title' => 'A total professional',
				'post_excerpt' => 'He worked hard and fast with us to pull things around.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-15021271-small-business-owner-at-work.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Patrick Stern',
				'email' => '',
				'company_name' => 'Actos & Stern',
				'company_website' => 'http://demos.wpmission.com',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'His attention to detail is what separates him from the rest. Today’s accountants don’t seem to have that level of accuracy that they did when I first started out in this business. Kevin, however, is a cut above and delivers the kind of quality that I used to produce when I started out as accountant for my Pop.',
				'post_name' => 'a-cut-above',
				'post_title' => 'A cut above',
				'post_excerpt' => 'His attention to detail is what separates him from the rest.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-6803641-business-men-portrait-father-and-son.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Edgar Wright',
				'email' => '',
				'company_name' => 'Wright & Sons',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Most accountants do a good job but Kevin goes the extra mile and makes sure he delivers quality work every time. I know I can count on him for my company.',
				'post_name' => 'the-extra-mile',
				'post_title' => 'The extra mile',
				'post_excerpt' => 'I know I can count on him.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-29044632-male-florist-working-in-garden-center.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Allen Bradley',
				'email' => '',
				'company_name' => 'Home, Garden & Beyond',
				'company_website' => 'http://demos.wpmission.com',
			),
		);


		$posts[] = array(
			'post' => array(
				'post_content' => 'When I was starting up my business I didn’t know where to begin. A friend suggested I see Kevin and I have never regretted it. Kevin sat me down and we had a chat about the kind of business and what he could provide for me. He is a trustworthy advisor, a confidant, a patient soul who takes the time to understand your history, challenges, and dreams. He is a master of keeping everything in context, as you would expect from someone with his level of experience. I would recommend him to anyone starting up a business.',
				'post_name' => 'a-trustworthy-advisor',
				'post_title' => 'A trustworthy advisor',
				'post_excerpt' => 'I would recommend him to anyone starting up a business.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-19050849-hawaiian-surfer-with-surfboard.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Finn',
				'email' => '',
				'company_name' => 'Surf Nation',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Kevin gives us practical advice and shows us how we could be doing things in a more cost efficient way. He is a good guy to have around in a crisis and it is reassuring to know that he is in our corner.',
				'post_name' => 'a-good-guy-to-have-around',
				'post_title' => 'A good guy to have around',
				'post_excerpt' => 'He is a good guy to have around in a crisis.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-25417363-african-male-auto-mechanic.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Justin',
				'email' => '',
				'company_name' => 'We Fix, U Drive',
				'company_website' => 'http://www.wpmission.com',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Payroll was a big headache for us. Bringing in Kevin was smooth and painless. He just made it happen for us.',
				'post_name' => 'no-more-headache',
				'post_title' => 'No more headache',
				'post_excerpt' => 'Kevin just made it happen for us.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-12629072-mature-business-woman-smiling.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Janet Walton',
				'email' => '',
				'company_name' => 'Walton Marketing & Sales',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Luckily we found Kevin and he was able to step in really quickly and take over the whole payroll issue for us.',
				'post_name' => 'ended-our-payroll-nightmare',
				'post_title' => 'Ended our payroll nightmare',
				'post_excerpt' => 'He was able to step in really quickly.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-31044616-this-team-is-the-best.jpg',
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
				'post_content' => 'We have worked with Kevin in the past and we recommend him wholeheartedly. The most important thing for us is that he listens. The last project he worked with us on was to organize QuickBooks. We had no clue where to start! We only knew we needed a simple, straightforward process that just worked so we could focus on our business. Kevin tailored a plan for us and walked us through it. He delivered on time with no issues.',
				'post_name' => 'we-had-no-clue',
				'post_title' => 'We had no clue',
				'post_excerpt' => 'The most important thing for us is that he listens.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-24302778-a-woman-in-an-antique-store-in-a-small-town-with.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Linda',
				'email' => '',
				'company_name' => 'The Cherrywood Shop',
				'company_website' => 'http://wpmission.com',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'Kevin organized everything and he made our paperwork process more efficient.',
				'post_name' => 'organized-everything',
				'post_title' => 'Organized everything',
				'post_excerpt' => 'Kevin organized everything.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-11245255-restaurant-team.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Mr. Klark',
				'email' => '',
				'company_name' => 'Klark’s Restaurant',
				'company_website' => '',
			),
		);

		$posts[] = array(
			'post' => array(
				'post_content' => 'We had an accountant working in-house for us, which was a disaster. He hadn’t input any of the figures from our receipts for our business purchases for the whole year. End of year taxes had to be done and he had no figures for this. We called Kevin who came in to help. He sorted out the whole mess and made sure everything was good to go. He has done all our accounting ever since. Kevin rocks!',
				'post_name' => 'sorted-out-the-whole-mess',
				'post_title' => 'Sorted out the whole mess',
				'post_excerpt' => 'He sorted out the whole mess and made sure everything was good to go.',
				'post_type' => 'wpm-testimonial',
				'post_status' => 'publish',
			),
			'thumbnail' => array(
				'name' => 'stock-photo-23061638-portrait-real-people-high-definition-green-background.jpg',
				'type' => 'jpg',
			),
			'meta' => array(
				'client_name' => 'Jason',
				'email' => '',
				'company_name' => '',
				'company_website' => 'http://jasonstereo.com',
			),
		);


		return $posts;
	}

}

new Strong_Testimonials_Sample_Data();
