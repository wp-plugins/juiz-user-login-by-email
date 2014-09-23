<?php
/*
	Plugin Name: Juiz User Login by email
	Plugin URI: http://www.creativejuiz.fr/blog/wordpress/wordpress-autoriser-acces-admin-grace-adresse-e-mail
	Description: Allows user to log-in with its email address OR its username
	Version: 1.0.1
	Author: CreativeJuiz
	Author URI: http://creativejuiz.com
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
	Copyright 2014 Geoffrey Crofte - Creative Juiz

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


define( 'JULBE_LANG', 'julbe_lang' );
define( 'JULBE_DIRNAME', basename( dirname( __FILE__ ) ) );

// plugin translatable
add_action( 'init', 'make_juiz_ulbe_multilang' );
function make_juiz_ulbe_multilang() {
	load_plugin_textdomain( JULBE_LANG, false, JULBE_DIRNAME.'/languages' );
}

// allow auth by email
if ( !function_exists('juiz_allow_email_login')) {
	add_filter('authenticate', 'juiz_allow_email_login', 20, 3);
	/**
	 * juiz_allow_email_login filter to the authenticate filter hook, to fetch a username based on entered email
	 * @param  obj $user 			the WP user object
	 * @param  string $username 	the data of username input
	 * @param  string $password 	the data of password input
	 * @return boolean
	 */
	function juiz_allow_email_login( $user, $username, $password ) {
		if ( is_email( $username ) ) {
			$user = get_user_by_email( $username );
			if ( $user ) {
				$username = $user->user_login;
			}
		}
		return wp_authenticate_username_password( null, $username, $password );
	}
}
// allow auth by email text
if ( !function_exists('juiz_add_email_to_login')) {
	add_filter( 'gettext', 'juiz_add_email_to_login', 20, 3 );
	/**
	 * juiz_add_email_to_login function adds "or email" to the "username" label
	 * @param string $translated_text   translated text
	 * @param string $text              original text
	 * @param string $domain            text domain
	 */
	function juiz_add_email_to_login( $translated_text, $text, $domain ) {

		if ( 'wp-login.php' != basename( $_SERVER['SCRIPT_NAME'] ) || isset($_GET['action']) ) {
			return $translated_text;
		}

		if ( "Username" == $text ) {
			$translated_text .= ' '.__( 'or Email', JULBE_LANG);
		}
		return $translated_text;
	}
}
