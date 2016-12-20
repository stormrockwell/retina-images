=== WP Retina Images ===
Contributors: stormrockwell
Tags: retina,images,support,wp,2x
Requires at least: 4.0
Tested up to: 4.5
Stable tag: 0.1
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

For developers as a versatile and easy way to support retina image sizes in WordPress


== Description ==

This plugin was created for developers as a versatile and easy way to support retina image sizes in WordPress. 

**Features:**

* Creates the wpri_retina($image_size) function to return retina/non-retina image size depending on the device
* Adds a class to the body "is-retina" or "is-not-retina" to easily add retina support in CSS if your theme is using the body_class function

**Usage:**

Note: If you already have images uploaded you will need to run the plugin "Regenerate Thumbnails"
* Upload an image at least twice the size of the image size you want to WordPress
`<?php 
$img = wp_get_attachment_image($attachment_id, wpri_retina('thumbnail')); 
?>`
This can be used easily with many plugins such as ACF
`<?php 
$img = get_field('image');
$img_url = $img['sizes'][wpri_retina('thumbnail')];
?>`

Note: The image uploaded must be at least double the size that you are trying to include.

== Installation ==

**Installation**

1. Upload the folder "wp-retina-images" to your WordPress Plugins Directory (typically "/wp-content/plugins/")
2. Activate the plugin on your Plugins Page.
4. Done!