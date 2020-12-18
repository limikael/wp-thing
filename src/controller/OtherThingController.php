<?php

namespace thing;

require_once __DIR__."/../utils/Template.php";
require_once __DIR__."/../utils/HtmlUtil.php";

class OtherThingController extends Singleton {
	protected function __construct() {
		add_action("init",array($this,"init"));
		add_action("cmb2_admin_init",array($this,"cmb2_admin_init"));
	}

	public function cmb2_admin_init() {
		$cmb=new_cmb2_box(array(
			"id"=>"other_thing_settings",
			"title"=>__("Other Thing Settings","thing"),
			"object_types"=>array("otherthing"),
			'context'       => 'normal',
		));

		/*$cmb->add_field( array(
			'name'       => __( 'Test Text', 'cmb2' ),
			'id'         => 'otherthing_bla',
			'type'       => 'text',
		));*/

		$cmb->add_field( array(
			'name'       => __( 'Test Pligg', 'cmb2' ),
			'id'         => 'otherthing_pligg',
			'type'       => 'intervaltimer',
			'repeatable' => TRUE,
			'options' => array(
				'add_row_text' => "+",
			),
		));
	}

	public function init() {
		register_post_type("otherthing",array(
			'labels'=>array(
				'name'=>__( 'Other Things' ),
				'singular_name'=>__( 'Other Thing' ),
			),
			'supports'=>array('title'),
			'public'=>false,
			'show_ui'=>true
		));
	}
}