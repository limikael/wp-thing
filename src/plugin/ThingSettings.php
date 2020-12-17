<?php

namespace thing;

class ThingSettings extends Singleton {
	private $thing_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'thing_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'thing_settings_page_init' ) );
	}

	public function thing_settings_add_plugin_page() {
		add_options_page(
			'Thing Settings', // page_title
			'Thing Settings', // menu_title
			'manage_options', // capability
			'thing-settings', // menu_slug
			array( $this, 'thing_settings_create_admin_page' ), // function
			'dashicons-admin-generic', // icon_url
			3 // position
		);
	}

	public function getBrokerUrl() {
		if (!$this->thing_settings_options)
			$this->thing_settings_options=get_option( 'thing_settings_option_name' );

		return $this->thing_settings_options['broker_url_0'];
	}

	public function thing_settings_create_admin_page() {
		$this->thing_settings_options = get_option( 'thing_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Thing Settings</h2>
			<?php /* settings_errors(); */ ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'thing_settings_option_group' );
					do_settings_sections( 'thing-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function thing_settings_page_init() {
		register_setting(
			'thing_settings_option_group', // option_group
			'thing_settings_option_name', // option_name
			array( $this, 'thing_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'thing_settings_setting_section', // id
			'Settings', // title
			array( $this, 'thing_settings_section_info' ), // callback
			'thing-settings-admin' // page
		);

		add_settings_field(
			'broker_url_0', // id
			'Broker URL', // title
			array( $this, 'broker_url_0_callback' ), // callback
			'thing-settings-admin', // page
			'thing_settings_setting_section' // section
		);
	}

	public function thing_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['broker_url_0'] ) ) {
			$sanitary_values['broker_url_0'] = sanitize_text_field( $input['broker_url_0'] );
		}

		return $sanitary_values;
	}

	public function thing_settings_section_info() {
		
	}

	public function broker_url_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="thing_settings_option_name[broker_url_0]" id="broker_url_0" value="%s">',
			isset( $this->thing_settings_options['broker_url_0'] ) ? esc_attr( $this->thing_settings_options['broker_url_0']) : ''
		);
	}
}
