<?php

namespace thing;

class ThingSettings extends Singleton {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	public function admin_init() {
		register_setting("thing","thing_brokerurl");
		register_setting("thing","thing_apikey");
	}

	public function admin_menu() {
		add_options_page(
			'Thing Settings', // page_title
			'Thing Settings', // menu_title
			'manage_options', // capability
			'thing_settings', // menu_slug
			array($this,'settings_page' ) // function
		);
	}

	public function settings_page() {
		$t=new Template(__DIR__."/../tpl/thing_settings.tpl.php");
		$t->display();
	}
}
