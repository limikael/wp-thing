<?php

namespace thing;

class Thing {

	/**
	 * Constructor.
	 */
	public function __construct($post) {
		$this->post=$post;
	}

	/**
	 * Get the current thing being viewed or edited.
	 */
	static function getCurrent() {
		global $post;

		if (isset($_GET['post']))
			$usePost=get_post($_GET['post']);

		else if (isset($_POST['post_ID']))
			$usePost=get_post($_POST['post_ID']);

		else if ($post)
			$usePost=$post;

		if ($usePost && $usePost->post_type=="thing")
			return new Thing($usePost);

		else
			return null;
	}

	/**
	 * Query remote device for settings fields.
	 */
	public function getSettingsFields() {
		$res=ThingPlugin::brokerCall("test");
		error_log(print_r($res,TRUE));
	}
}