<?php

namespace thing;

require_once __DIR__."/../utils/Template.php";
require_once __DIR__."/../model/Thing.php";

class ThingController extends Singleton {
	protected function __construct() {
		add_action("init",array($this,"init"));
		add_action("add_meta_boxes",array($this,"add_meta_boxes"));
		add_action("cmb2_admin_init",array($this,"cmb2_admin_init"));
	}

	public function cmb2_admin_init() {
		$cmb=new_cmb2_box(array(
			"id"=>"thing_settings",
			"title"=>__("Settings","thing"),
			"object_types"=>array("thing"),
			'context'       => 'normal',
		));

		// Regular text field
		$cmb->add_field( array(
			'name'       => __( 'Test Text', 'cmb2' ),
			'desc'       => __( 'field description (optional)', 'cmb2' ),
			'id'         => 'yourprefix_text',
			'type'       => 'text',
		));

		$currentThing=Thing::getCurrent();
		if ($currentThing) {
			$currentThing->getSettingsFields();
		}
	}

	public function add_meta_boxes() {
		remove_meta_box('submitdiv','thing','side');

		add_meta_box(
			'thing-custom-submit','Save',
			array($this,"custom_submit_box"),
			'thing','side'
		);
	}

	public function custom_submit_box() { 
		$t=new Template(__DIR__."/../tpl/thing_submit_box.tpl.php");
		$t->display();
	}

	public function init() {
		register_post_type("thing",array(
			'labels'=>array(
				'name'=>__( 'Things' ),
				'singular_name'=>__( 'Thing' ),
				'not_found'=>__('No Things Configured.'),
				'add_new_item'=>__('Configure New Thing'),
				'edit_item'=>__('Configure Thing')
			),
			'supports'=>array('title'),
			'public'=>false,
			'show_ui'=>true,
			"menu_icon"=>"dashicons-editor-kitchensink"
		));
	}
}