<?php
/**
 * Plugin Name: Sane HTML Newsletters
 * Plugin URI:  http://doejo.com
 * Description: A simple plugin to maintain sanity when dealing with HTML email templates for your theme
 * Version:     0.1.0
 * Author:      Rinat Khaziev, doejo
 * Author URI:  http://doejo.com
 * License:     GPLv2+
 */

/**
 * Copyright (c) 2015 Rinat Khaziev (email : rinat.khaziev@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
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

// Useful global constants
define( 'SHNL_VERSION', '0.1.0' );
define( 'SHNL_URL',     plugin_dir_url( __FILE__ ) );
define( 'SHNL_PATH',    dirname( __FILE__ ) . '/' );

require_once SHNL_PATH . 'vendor/autoload.php';

// Specify the dep
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class Sane_HTML_Newsletters {

	// Plugin properties

	function __construct() {
		// Hook the init
		add_action( 'init', array( $this, 'action_init' ) );
		register_activation_hook( __FILE__, array( $this, 'action_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'action_deactivate' ) );
	}

	function action_init() {

		// Wire actions
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
		add_action( 'parse_query', array( $this, 'action_parse_query' ), 20 );

		$this->set_rewrite_rules();
	}

	// Setup
	function action_activate() {
		flush_rewrite_rules();
	}

	// Cleanup
	function action_deactivate() {
		flush_rewrite_rules();
	}

	function action_admin_init(){
		// no-op
	}

	/**
	 * Add a couple of extra actions to automatically convert output to HTML with inlined styles
	 *
	 * @return [type] [description]
	 */
	function action_parse_query() {
		// Use this filter to determine whether the output should be auto-converted
		if ( false === apply_filters( 'shnl_should_auto_convert', false ) )
			return;

		// Let's do some magic
		add_action( 'wp', array( $this, 'action_wp' ) );
		add_action( 'shutdown', array( $this, 'action_shutdown' ) );

	}

	/**
	 * Start output buffer before inclusion of template
	 * @return [type] [description]
	 */
	function action_wp() {
		ob_start( 'Sane_HTML_Newsletters::convert' );
	}

	/**
	 * Flush output buffer and trigger the ob callback
	 * @return [type] [description]
	 */
	function action_shutdown() {
		if ( ob_get_length() )
			ob_clean();
	}

	/**
	 * TODO: maybe remove completely, needs to be figured out based on approach for auto converting output
	 * (ie query var vs just a filter)
	 * @param  [type] $qv [description]
	 * @return [type]     [description]
	 */
	function filter_query_vars( $qv ) {
		$qv[] = 'dnl_auto_convert';
		return $qv;
	}

	function set_rewrite_rules() {
		// TODO: Add a rewrite endpoint maybe
		// no-op for now
	}

	/**
	 * Converts $html regular HTML to HTML with inline styles
	 * @param  string $html regular HTML
	 * @return string       newsletter-ready HTML with inline styles
	 */
	public static function convert( $html ) {
		$css = file_get_contents( apply_filters( 'shnl_stylesheet_path', get_stylesheet_directory() . '/style.css' ) );
		$htmldoc = new CssToInlineStyles();
		$htmldoc->setHTML( $html );
		$htmldoc->setCSS( $css );
		return $htmldoc->convert();
	}
}

global $sane_html_newsletters;
$sane_html_newsletters = new Sane_HTML_Newsletters;