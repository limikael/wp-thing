<?php

namespace thing;

require_once __DIR__."/../model/Thing.php";
require_once __DIR__."/../model/ThingData.php";
require_once __DIR__."/../utils/HtmlUtil.php";

class ThingDataController extends Singleton {
	protected function __construct() {
		add_action("wp_ajax_thingdata",array($this,"wp_ajax_thingdata"));
		add_action("wp_ajax_nopriv_thingdata",array($this,"wp_ajax_thingdata"));
		add_action("wp_ajax_thing_chart_data",array($this,"wp_ajax_thing_chart_data"));
		add_action("wp_ajax_nopriv_thing_chart_data",array($this,"wp_ajax_thing_chart_data"));
		add_action("add_meta_boxes",array($this,"add_meta_boxes"));
	}

	static function alignTimestampToMonth($timestamp) {
		return strtotime(gmdate("Y-m-01 00:00:00",$timestamp)." UTC");
	}

	function wp_ajax_thing_chart_data() {
		$timestamp=$_REQUEST["timestamp"];
		$var=$_REQUEST["var"];
		$postId=$_REQUEST["postId"];

		if ($timestamp>time())
			$timestamp=time();

		$scope=$_REQUEST["scope"];
		if (!$scope)
			$scope="hour";

		switch ($scope) {
			case "hour":
				$fromTimestamp=intval(60*60*floor($timestamp/(60*60)));
				$toTimestamp=$fromTimestamp+60*60;
				$prevTimestamp=$fromTimestamp-60*60;
				$span="live";
				$rangeLabel=
					gmdate("j M, Y, H:i",$fromTimestamp)." -> ".
					gmdate("H:i",$toTimestamp);
				$labelFormat="H:i";
				break;

			case "day":
				$fromTimestamp=intval(60*60*24*floor($timestamp/(60*60*24)));
				$toTimestamp=$fromTimestamp+60*60*24;
				$prevTimestamp=$fromTimestamp-60*60*24;
				$span="minutely";
				$rangeLabel=
					gmdate("j M, Y",$fromTimestamp);
				$labelFormat="H:i";
				break;

			case "month":
				$aligned=ThingDataController::alignTimestampToMonth($timestamp);
				$fromTimestamp=$aligned;
				$toTimestamp=
					ThingDataController::alignTimestampToMonth(
						$aligned+32*60*60*24
					);
				$prevTimestamp=
					ThingDataController::alignTimestampToMonth(
						$aligned-60*60*24
					);
				$span="hourly";
				$rangeLabel=
					gmdate("M, Y",$fromTimestamp);
				$labelFormat="j";
				break;

			default:
				wp_die();
				break;
		}

		$output=array();
		$output["labels"]=array();
		$output["values"]=array();
		$output["nextTimestamp"]=$toTimestamp;
		$output["prevTimestamp"]=$prevTimestamp;
		$output["rangeLabel"]=$rangeLabel;

		$datas=ThingData::getSpanData($postId,$var,$span,$fromTimestamp,$toTimestamp);
		foreach ($datas as $data) {
			$output["labels"][]=gmdate($labelFormat,$data->getTimestamp());
			$output["values"][]=$data->value;
		}

		echo json_encode($output);
		wp_die();
	}

	public function wp_ajax_thingdata() {
		$thing=Thing::getByTitle($_REQUEST["id"]);

		if (!$thing)
			return;

		$logVars=array();
		foreach ($_REQUEST as $key=>$val)
			if ($key!="id" && $key!="action" && $key!="key")
				$logVars[]=$key;

		foreach ($logVars as $logVar)
			$this->logValue($thing,$logVar,$_REQUEST[$logVar]);

		wp_die();
	}

	public function logValue($thing, $var, $val) {
		$t=time();

		$data=new ThingData();
		$data->post_id=$thing->getId();
		$data->var=$var;
		$data->value=$val;
		$data->stamp=gmdate("Y-m-d H:i:s",$t);
		$data->span="live";
		$data->save();

		ThingData::summarize($thing->getId(),$var,"live","minutely",$t);
		ThingData::summarize($thing->getId(),$var,"minutely","hourly",$t);
		ThingData::summarize($thing->getId(),$var,"hourly","daily",$t);
	}

	public function add_meta_boxes() {
		if (Thing::getCurrent() && Thing::getCurrent()->isOnline()) {
			add_meta_box(
				'thing-chart',"Chart",
				array($this,"thing_chart_box"),
				'thing','normal'
			);
		}
	}

	public function thing_chart_box() {
		$thing=Thing::getCurrent();

		$vars=array();
		$vars["timestamp"]=time();
		$vars["scopeoptions"]=array(
			"hour"=>"Hour",
			"day"=>"Day",
			"month"=>"Month",
		);

		$vars["varoptions"]=array();
		foreach ($thing->getLogFields() as $logField)
			$vars["varoptions"][$logField->getKey()]=$logField->getLabel();

		$vars["postId"]=$thing->getId();

		$t=new Template(__DIR__."/../tpl/thing_chart_box.tpl.php");
		$t->display($vars);
	}
}