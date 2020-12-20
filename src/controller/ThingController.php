<?php

namespace thing;

require_once __DIR__."/../utils/Template.php";
require_once __DIR__."/../model/Thing.php";

class ThingController extends Singleton {
	protected function __construct() {
		add_action("init",array($this,"init"));
		add_action("add_meta_boxes",array($this,"add_meta_boxes"));
		add_action("cmb2_admin_init",array($this,"cmb2_admin_init"));

		add_filter('cmb2_override_meta_value',array($this,'cmb2_override_meta_value'),10,4);
		add_filter('cmb2_override_meta_save',array($this,'cmb2_override_meta_save'),10,4);
		add_action('cmb2_save_post_fields',array($this,'cmb2_save_post_fields'),10,1);
	}

	function cmb2_admin_init() {
		$currentThing=Thing::getCurrent();
		if (!$currentThing)
			return;

		$box_options = array(
			'id' => 'thingsettings',
			'title' => "Thing Settings",
			'object_types' => array( 'thing' ),
			'show_names'   => true,
		);

		$cmb = new_cmb2_box($box_options);

		$tabs_setting = array(
			'config' => $box_options,
			'tabs'   => array()
		);

		foreach ($currentThing->getTabNames() as $tabName) {
			$tab=array(
				"id"=>sanitize_title($tabName),
				"title"=>$tabName,
				"fields"=>array(),
			);

			foreach ($currentThing->getFieldsByTabName($tabName) as $field)
				$tab["fields"][]=$field->getCmbDef();

			$tabs_setting["tabs"][]=$tab;
		}

		$cmb->add_field(array(
			'id'   => '__tabs',
			'type' => 'tabs',
			'tabs' => $tabs_setting,
		));
	}

	public function cmb2_save_post_fields($id) {
		if ($id!=Thing::getCurrent()->getId())
			return;

		Thing::getCurrent()->save();
	}

	public function cmb2_override_meta_value($data, $id, $a, $field) {
		if ($id!=Thing::getCurrent()->getId())
			return $data;

		$field=Thing::getCurrent()->getFieldByKey($a["field_id"]);
		if (!$field)
			return $data;

		return $field->getCmb2Value();
	}

	public function cmb2_override_meta_save($override, $a, $args, $field) {
		if ($a["id"]!=Thing::getCurrent()->getId())
			return NULL;

		$field=Thing::getCurrent()->getFieldByKey($a["field_id"]);
		$field->updateValueWithCmb2Data($a["value"]);
		return TRUE;
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