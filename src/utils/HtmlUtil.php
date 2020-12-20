<?php

namespace thing;

class HtmlUtil {
	static function render_select_options($options, $current=NULL) {
		$res="";

		foreach ( $options as $key => $label ) {
			$res.=sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $key ),
				( ( strval( $current ) === strval( $key ) ) ? 'selected' : '' ),
				esc_html( $label )
			);
		}

		return $res;
	}

	static function display_select_options($options, $current=NULL) {
		echo HtmlUtil::render_select_options($options,$current);
	}
}
