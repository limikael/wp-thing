<?php

namespace thing;

require_once __DIR__."/../utils/Singleton.php";
require_once __DIR__."/../controller/ThingController.php";
require_once __DIR__."/../controller/OtherThingController.php";
require_once __DIR__."/ThingSettings.php";
require_once __DIR__."/Cmb2IntervalTimerField.php";
require_once __DIR__."/Cmb2DurationField.php";

class ThingPlugin extends Singleton {
	protected function __construct() {
		ThingController::instance();
		//OtherThingController::instance();
		Cmb2IntervalTimerField::instance();
		Cmb2DurationField::instance();

		add_filter("cmb2_meta_box_url",array($this,"cmb2_meta_box_url"));
		add_action("admin_enqueue_scripts",array($this,"enqueue_scripts"));

		if (is_admin())
			ThingSettings::instance();
	}

	public function cmb2_meta_box_url($url) {
		if (strpos($url,"wp-thing"))
			$url=THING_URL."/ext/CMB2/";

		return $url;
	}

	public function enqueue_scripts() {
		wp_enqueue_script("thing",
			THING_URL."/js/thing.js",
			array("jquery"),"1.0.0",true);

		wp_enqueue_style("thing-style",
			THING_URL."/css/thing.css");

		wp_enqueue_script("cmb2-conditional-logic",
			THING_URL."/js/cmb2-conditional-logic.js",
			array("jquery"),"1.0.0",true);
	}

	public function brokerCall($url, $params=array()) {
		$brokerUrl=ThingSettings::instance()->getBrokerUrl();
		$url=$brokerUrl."/".$url."/?".http_build_query($params);

		//error_log($url);

		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		/*curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			"X-Api-Key: ysOIV9vNp1hS2tHC"
		));*/

		$encoded=curl_exec($curl);
		$res=json_decode($encoded,TRUE);

		if (!$res/* || !array_key_exists("ok",$res) || !$res["ok"]*/) {
			throw new \Exception("Unable to perform API call: ".$url.": ".$encoded);
		}

		return $res;
	}
}