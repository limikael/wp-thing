<?php

namespace thing;

require_once __DIR__."/../utils/Singleton.php";
require_once __DIR__."/../controller/ThingController.php";
require_once __DIR__."/ThingSettings.php";

class ThingPlugin extends Singleton {
	protected function __construct() {
		ThingController::instance();

		add_filter("cmb2_meta_box_url",array($this,"cmb2_meta_box_url"));

		if ( is_admin() )
			ThingSettings::instance();
	}

	public function cmb2_meta_box_url() {
		$url=THING_URL."/ext/CMB2/";

		return $url;
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
			throw new \Exception("Unable to perform API call: ".$encoded);
		}

		return $res;
	}
}