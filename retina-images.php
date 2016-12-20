<?php 
	/*
		Plugin Name: Retina Images
		Description: For developers as an easy way to support retina image sizes server side
		Version: 0.1
		Author: Storm Rockwell
		Author URI: http://www.stormrockwell.com
		License: GPL2v2
		
		Retina Images is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		any later version.
		 
		Retina Images is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
		GNU General Public License for more details.
		 
		You should have received a copy of the GNU General Public License
		along with WP Retina Images. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
	*/

	// todo add max size for retina version

	if ( ! class_exists( 'Retina_Images' ) ) {
		
		class Retina_Images {
			private $prefix = 'wpri';
			private $is_retina_screen = false;

			public function __construct() {
				add_action( 'wp_enqueue_scripts', array( $this, 'set_retina_cookie' ) );
				add_action( 'init', array( $this, 'add_retina_image_sizes' ) );
				add_filter( 'body_class', array( $this, 'add_body_class' ) );
				add_filter( 'image_get_intermediate_size', array( $this, 'image_get_intermediate_size' ) );
				add_filter( 'wp_calculate_image_srcset', array( $this, 'wp_calculate_image_srcset' ) );
			}

			/**
			 * Set retina cookie
			 * Detects if the user's screen ratio is over 1 and sets it as a cookie
			 */
			public function set_retina_cookie() {

				if ( ! isset( $_COOKIE['device_pixel_ratio'] ) ) {
					$this->is_retina_screen = false;
					wp_enqueue_script( 'wpri_set_cookie', plugin_dir_url( __FILE__ ) . 'js/script.js' );
				} elseif ( 1.25 < $_COOKIE['device_pixel_ratio'] ) {
					$this->is_retina_screen = true;
				} else {
					$this->is_retina_screen = false;
				}

			}

			/**
			 * Add retina image sizes
			 * Creates retina image sizes for all sizes currently registered
			 */
			public function add_retina_image_sizes() {
				global $_wp_additional_image_sizes;

				$sizes = array();

				foreach ( get_intermediate_image_sizes() as $_size ) {

					if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
						$sizes[ $_size ]['width']  = get_option("{$_size}_size_w");
						$sizes[ $_size ]['height'] = get_option("{$_size}_size_h");
						$sizes[ $_size ]['crop']   = (bool) get_option("{$_size}_crop");
					} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
						$sizes[ $_size ] = array(
							'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
							'height' => $_wp_additional_image_sizes[ $_size ]['height'],
							'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
						);
					}

					// create retina size if it doesn't exist
					if ( false === strpos( $_size, '_retina' ) ) {
						add_image_size(
							$_size . '_retina', 
							$sizes[ $_size ]['width'] * 2, 
							$sizes[ $_size ]['height'] * 2, 
							$sizes[ $_size ]['crop']
						);
					}

				}

			}

			/**
			 * Add retina class to body_class
			 */
			public function add_body_class( $classes ) {

				if ( $this->is_retina_screen ) {
					$classes[] = 'is-retina';
				} else {
					$classes[] = 'is-not-retina';
				}

				return $classes;
			}

			public function str_lreplace( $search, $replace, $subject ) {
				$pos = strrpos( $subject, $search );

				if ( false !== $pos ) {
					$subject = substr_replace( $subject, $replace, $pos, strlen( $search ) );
				}

				return $subject;
			}

			public function url_exists( $url ) {
				$headers = get_headers( $url );
				return stripos( $headers[0], '200 OK' ) ? true : false;
			}


			public function image_get_intermediate_size( $data ) {

				if ( ! $this->is_retina_screen ) {
					return $data;
				}

				$standard_width = $data['width'];
				$standard_height = $data['height'];
				$standard_file_size = $standard_width . 'x' . $standard_height;
				$standard_file_name = $data['file'];

				$retina_width = $standard_width * 2;
				$retina_height = $standard_height * 2;
				$retina_file_size = $retina_width . 'x' . $retina_height;
				$retina_file_name = $this->str_lreplace( $standard_file_size, $retina_file_size, $data['file'] );

				$retina_url = str_replace( $standard_file_name, $retina_file_name, $data['url'] );

				if ( $this->url_exists( $retina_url ) ) {
					$data['url'] = $retina_url;
					$data['file'] = $retina_file_name;
					$data['path'] = str_replace( $standard_file_name, $retina_file_name, $data['path'] );
				} 

				return $data;
			}

			public function wp_calculate_image_srcset( $src_sets ) {

				if ( ! $this->is_retina_screen ) {
					return $src_sets;
				}

				foreach ( $src_sets as $key => $src ) {
					$src_sets[ $key ]['value'] /= 2;
				}

				return $src_sets;
			}

		}

		new Retina_Images();

	}