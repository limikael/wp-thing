<?php

namespace thing;

require_once __DIR__."/../utils/Template.php";
require_once __DIR__."/../model/Thing.php";

class ThingController extends Singleton {
	protected function __construct() {
		add_action("init",array($this,"init"));
		add_action("add_meta_boxes",array($this,"add_meta_boxes"));
		add_action("cmb2_admin_init",array($this,"cmb2_admin_init"));

		$this->addTestCmb2();
	}

	public function cmb2_admin_init() {
		/*$cmb=new_cmb2_box(array(
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

		$cmb->add_field(array(
			"id"=>"group_test",
			"type"=>"group",
			"options"=>array(
				"group_title"=>__("Hello {#}","thing"),
			)
		));

		$cmb->add_group_field("group_test",array(
			"group_title"=>"Hello {#}",
			"id"=>"group_field",
			"type"=>"text",
			"name"=>"Timer Text"
		));

		$cmb->add_group_field("group_test",array(
			"group_title"=>"Hello {#}",
			"id"=>"group_field_2",
			"type"=>"text",
			"name"=>"Hour"
		));*/

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

	private function addTestCmb2() {
			//add_action( 'cmb2_admin_init', array( $this, 'testCmb' ) );
			add_action( 'cmb2_admin_init', array( $this, 'testGroupCmb' ) );
		}

	public function testGroupCmb() {
		$prefix		 = '_yourgridprefix_group_';
		$cmb_group	 = new_cmb2_box( array(
			'id'			 => $prefix . 'metabox',
			'title'			 => __( 'Repeating Field Group using a Grid', 'cmb2' ),
			'object_types'	 => array( 'thing' ),
		) );
		/*$field1		 = $cmb_group->add_field( array(
			'name'	 => __( 'Test Text', 'cmb2' ),
			'desc'	 => __( 'field description (optional)', 'cmb2' ),
			'id'	 => $prefix . 'text',
			'type'	 => 'text',
		) );
		$field2		 = $cmb_group->add_field( array(
			'name'	 => __( 'Test Text Small', 'cmb2' ),
			'desc'	 => __( 'field description (optional)', 'cmb2' ),
			'id'	 => $prefix . 'textsmall',
			'type'	 => 'text',
		) );*/

		// $group_field_id is the field id string, so in this case: $prefix . 'demo'
		$group_field_id	 = $cmb_group->add_field( array(
			'id'		 => $prefix . 'demo',
			'type'		 => 'group',
			'options'	 => array(
				'group_title'	 => __( 'Timer {#}', 'cmb2' ), // {#} gets replaced by row number.
				'add_button'	 => __( 'Add Another Entry', 'cmb2' ),
				'remove_button'	 => __( 'Remove Entry', 'cmb2' ),
				"repeatable" => false
			),
		) );
		$gField1		 = $cmb_group->add_group_field( $group_field_id, array(
			'name'	 => __( 'Every', 'cmb2' ),
			'id'	 => 'title',
			'type'	 => 'select',
			'options'=> array(
				"day"=>"Day",
				"hour"=>"Hour",
				"minute"=>"Minute"
			)
		) );
		$gField2		 = $cmb_group->add_group_field( $group_field_id, array(
			'name'			 => __( 'Hour', 'cmb2' ),
			'id'			 => 'hour',
			'type'			 => 'text_small',
		));
		$gField3		 = $cmb_group->add_group_field( $group_field_id, array(
			'name'			 => __( 'Minute', 'cmb2' ),
			'id'			 => 'minute',
			'type'			 => 'text_small',
		));
		$gField4		 = $cmb_group->add_group_field( $group_field_id, array(
			'name'			 => __( 'Second', 'cmb2' ),
			'id'			 => 'second',
			'type'			 => 'text_small',
		));

		// Create a default grid.
		$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_group );

		// Create now a Grid of group fields.
		$cmb2GroupGrid	 = $cmb2Grid->addCmb2GroupGrid( $group_field_id );
		$row			 = $cmb2GroupGrid->addRow();
		$row->addColumns( array(
			$gField1,
			$gField2,
			$gField3,
			$gField4,
		) );

		// Now setup your columns like you generally do, even with group fields.
		/*$row = $cmb2Grid->addRow();
		$row->addColumns( array(
			$field1,
			$field2,
		) );*/
		$row = $cmb2Grid->addRow();
		$row->addColumns( array(
			$cmb2GroupGrid, // Can be $group_field_id also.
		) );
	}

	/*public function testCmb() {
		// Start with an underscore to hide fields from custom fields list.
		$prefix	 = '_yourgridprefix_demo_';
		$cmb	 = new_cmb2_box( array(
			'id'			 => $prefix . 'metabox',
			'title'			 => __( 'Test Metabox using a Grid', 'cmb2' ),
			'object_types'	 => array( 'thing' ), // Post type.
		));
		$field1	 = $cmb->add_field( array(
			'name'	 => __( 'Test Text', 'cmb2' ),
			'desc'	 => __( 'field description (optional)', 'cmb2' ),
			'id'	 => $prefix . 'text',
			'type'	 => 'text',
		));
		$field2	 = $cmb->add_field( array(
			'name'	 => __( 'Test Text Small', 'cmb2' ),
			'desc'	 => __( 'field description (optional)', 'cmb2' ),
			'id'	 => $prefix . 'textsmall',
			'type'	 => 'text',
		));
		$field3	 = $cmb->add_field( array(
			'name'	 => __( 'Test Text Medium', 'cmb2' ),
			'desc'	 => __( 'field description (optional)', 'cmb2' ),
			'id'	 => $prefix . 'textmedium',
			'type'	 => 'text',
		));
		$field4	 = $cmb->add_field( array(
			'name'	 => __( 'Website URL', 'cmb2' ),
			'desc'	 => __( 'field description (optional)', 'cmb2' ),
			'id'	 => $prefix . 'url',
			'type'	 => 'text',
		));
		$field5	 = $cmb->add_field( array(
			'name'	 => __( 'Test Text Email', 'cmb2' ),
			'desc'	 => __( 'field description (optional)', 'cmb2' ),
			'id'	 => $prefix . 'email',
			'type'	 => 'text',
		));

		$cmb2Grid	 = new \Cmb2Grid\Grid\Cmb2Grid( $cmb );
		$row		 = $cmb2Grid->addRow();
		$row->addColumns( array(
			//$field1,
			//$field2
			array( $field1, 'class' => 'col-md-8' ),
			array( $field2, 'class' => 'col-md-4' ),
		));
		$row		 = $cmb2Grid->addRow();
		$row->addColumns( array(
			$field3,
			$field4,
			$field5,
		) );
	}*/
}