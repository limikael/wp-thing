<?php

namespace thing;

require_once __DIR__."/../utils/Singleton.php";
require_once __DIR__."/../controller/ThingController.php";
require_once __DIR__."/../controller/ThingDataController.php";
require_once __DIR__."/../model/ThingData.php";
require_once __DIR__."/../controller/OtherThingController.php";
require_once __DIR__."/ThingSettings.php";
require_once __DIR__."/Cmb2IntervalTimerField.php";
require_once __DIR__."/Cmb2DurationField.php";

class ThingPlugin extends Singleton {
	protected function __construct() {
		ThingDataController::instance();
		ThingController::instance();
		//OtherThingController::instance();
		Cmb2IntervalTimerField::instance();
		Cmb2DurationField::instance();

		add_filter("cmb2_meta_box_url",array($this,"cmb2_meta_box_url"));
		add_action("admin_enqueue_scripts",array($this,"enqueue_scripts"));
		add_action("admin_notices",array($this,"admin_notices"));

		if (is_admin())
			ThingSettings::instance();
	}

	public function admin_notices() {
		global $pagenow;

		if ($pagenow=="edit.php" && $_REQUEST["post_type"]=="thing") {
			$this->initBrokerData();

			if ($this->brokerError) {
				$t=new Template(__DIR__."/../tpl/thing_notice.tpl.php");
				$t->display(array(
					"type"=>"warning",
					"message"=>$this->brokerError
				));
			}
		}
	}

	public function activate() {
		ThingData::install();
	}

	public function uninstall() {
		ThingData::uninstall();
	}

	public function cmb2_meta_box_url($url) {
		if (strpos($url,"wp-thing"))
			$url=THING_URL."/ext/CMB2/";

		return $url;
	}

	public function enqueue_scripts() {
		wp_enqueue_script('charts-bundle',
			'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js',
			array('jquery'),"2.9.3",true);

		wp_enqueue_script("thing-js",
			THING_URL."/js/thing.js",
			array("jquery","charts-bundle"),"1.0.1",true);

		wp_enqueue_style("thing-style",
			THING_URL."/css/thing.css");

		wp_enqueue_script("cmb2-conditional-logic",
			THING_URL."/js/cmb2-conditional-logic.js",
			array("jquery"),"1.0.0",true);
	}

	public function brokerCall($url, $params=array()) {
		$brokerUrl=get_option("thing_brokerurl");
		$url=$brokerUrl."/".$url."/?".http_build_query($params);

		//error_log($url);

		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

		$key=get_option("thing_apikey");
		if ($key) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				"X-Api-Key: ".$key
			));
		}

		$encoded=curl_exec($curl);
		$code=curl_getinfo($curl,CURLINFO_RESPONSE_CODE);

		if (!$code)
			throw new \Exception("Unable to reach broker, no response from server.");

		if ($code!==200)
			throw new \Exception("Unable to reach broker, response code: ".$code.".");

		$res=json_decode($encoded,TRUE);

		if (!$res/* || !array_key_exists("ok",$res) || !$res["ok"]*/) {
			throw new \Exception("Unable to reach broker: ".$url.": ".$encoded);
		}

		return $res;
	}

	public function initBrokerData() {
		if ($this->haveBrokerData)
			return;

		$this->haveBrokerData=TRUE;

		try {
			$this->brokerData=$this->brokerCall("");
			$this->brokerError=NULL;
		}

		catch (\Exception $e) {
			$this->brokerData=NULL;
			$this->brokerError=$e->getMessage();
		}
	}

	public function isBrokerOnline() {
		$this->initBrokerData();

		if ($this->brokerData)
			return TRUE;

		return FALSE;
	}

	public function isDeviceOnline($deviceTitle) {
		$this->initBrokerData();

		if (!$this->isBrokerOnline())
			return FALSE;

		return in_array($deviceTitle,$this->brokerData["devices"]);
	}
}