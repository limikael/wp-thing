<?php

namespace thing;

require_once __DIR__."/../utils/Singleton.php";

class Cmb2DurationField extends Singleton {
	protected function __construct() {
		add_action("cmb2_render_duration",
			array($this,"cmb2_render_duration"),10,5);

		add_filter('cmb2_sanitize_duration',
			array($this,'cmb2_sanitize_duration'),10,5);

		add_filter('cmb2_types_esc_duration',
			array($this,'cmb2_types_esc_duration'),10,4);

		add_action("admin_enqueue_scripts",array($this,"enqueue_scripts"));
	}

	public function enqueue_scripts() {
		/*wp_enqueue_script("thing",
			THING_URL."/js/cmb2-intervaltimer.js",
			array("jquery"),"1.0.0",true);*/
	}

	public function cmb2_render_duration($field, $value, $object_id, $object_type, $field_type) {
		$value=wp_parse_args($value,array(
			"minutes"=>"",
			"seconds"=>""
		));

		$out="<div class='cmb2-durtion'>";

		$options=array(""=>"");
		for ($i=0; $i<60; $i++)
			$options[$i]=$i."m";

		$out.=$field_type->select(array(
			"options"=>HtmlUtil::render_select_options($options,$value["minutes"]),
			"name"=>$field_type->_name("[minutes]"),
			"id"=>$field_type->_id("minutes"),
			"value"=>$value["minutes"],
			"class"=>"cmb2_select it-minutes"
		));

		$options=array(""=>"");
		for ($i=0; $i<60; $i++)
			$options[$i]=$i."s";

		$out.=":".$field_type->select(array(
			"options"=>HtmlUtil::render_select_options($options,$value["seconds"]),
			"name"=>$field_type->_name("[seconds]"),
			"id"=>$field_type->_id("seconds"),
			"value"=>$value["seconds"],
			"class"=>"cmb2_select it-seconds"
		));

		$out.="</div>";

		echo $out;
	}

	function cmb2_sanitize_duration( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}
		return array_filter($meta_value);
	}

	function cmb2_types_esc_duration( $check, $meta_value, $field_args, $field_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
		}	  
		return array_filter($meta_value);
	}
}