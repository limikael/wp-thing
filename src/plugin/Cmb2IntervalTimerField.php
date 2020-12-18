<?php

namespace thing;

require_once __DIR__."/../utils/Singleton.php";

class Cmb2IntervalTimerField extends Singleton {
	protected function __construct() {
		add_action("cmb2_render_intervaltimer",
			array($this,"cmb2_render_intervaltimer"),10,5);

		add_filter('cmb2_sanitize_intervaltimer',
			array($this,'cmb2_sanitize_intervaltimer'),10,5);

		add_filter('cmb2_types_esc_intervaltimer',
			array($this,'cmb2_types_esc_intervaltimer'),10,4);

		add_action("admin_enqueue_scripts",array($this,"enqueue_scripts"));
	}

	public function enqueue_scripts() {
		wp_enqueue_script("thing",
			THING_URL."/js/cmb2-intervaltimer.js",
			array("jquery"),"1.0.0",true);
	}

	public function cmb2_render_intervaltimer($field, $value, $object_id, $object_type, $field_type) {
		$value=wp_parse_args($value,array(
			"interval"=>"",
			"hour"=>"",
			"minute"=>"",
			"second"=>""
		));

		$options=array(
			""=>"",
			"day"=>"Every Day",
			"hour"=>"Every Hour",
			"minute"=>"Every Minute",
		);

		$out="<div class='cmb2-intervaltimer'>";
		$out.=$field_type->select(array(
			"options"=>HtmlUtil::render_select_options($options,$value["interval"]),
			"name"=>$field_type->_name("[interval]"),
			"id"=>$field_type->_id("_interval"),
			"value"=>$value["interval"],
			"class"=>"cmb2_select it-interval"
		));

		$options=array(""=>"");
		for ($i=0; $i<24; $i++)
			$options[$i]=$i."h";

		$out.=" ".$field_type->select(array(
			"options"=>HtmlUtil::render_select_options($options,$value["hour"]),
			"name"=>$field_type->_name("[hour]"),
			"id"=>$field_type->_id("_hour"),
			"value"=>$value["hour"],
			"class"=>"cmb2_select it-hour"
		));

		$options=array(""=>"");
		for ($i=0; $i<60; $i++)
			$options[$i]=$i."m";

		$out.=":".$field_type->select(array(
			"options"=>HtmlUtil::render_select_options($options,$value["minute"]),
			"name"=>$field_type->_name("[minute]"),
			"id"=>$field_type->_id("minute"),
			"value"=>$value["minute"],
			"class"=>"cmb2_select it-minute"
		));

		$options=array(""=>"");
		for ($i=0; $i<60; $i++)
			$options[$i]=$i."s";

		$out.=":".$field_type->select(array(
			"options"=>HtmlUtil::render_select_options($options,$value["second"]),
			"name"=>$field_type->_name("[second]"),
			"id"=>$field_type->_id("_second"),
			"value"=>$value["second"],
			"class"=>"cmb2_select it-second"
		));

		$out.="</div>";

		echo $out;
	}

	function cmb2_sanitize_intervaltimer( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			if ($val["interval"])
				$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}
		return array_filter($meta_value);
	}

	function cmb2_types_esc_intervaltimer( $check, $meta_value, $field_args, $field_object ) {
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