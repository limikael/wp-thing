<?php

namespace thing;

require_once __DIR__."/../../ext/wprecord/WpRecord.php";

class ThingData extends \WpRecord {
	const spans=array(
		"live"=>5,
		"minutely"=>60,
		"hourly"=>60*60,
		"daily"=>60*60*24
	);

	public static function initialize() {
		self::field( 'id', 'integer not null auto_increment' );
		self::field( 'post_id', 'integer not null' );
		self::field( 'var', 'char(16) not null' );
		self::field( 'stamp', 'datetime not null' );
		self::field( 'value', 'float not null' );
		self::field( 'min', 'float not null' );
		self::field( 'max', 'float not null' );
		self::field( 'span', 'char(16) not null' );
		self::field( 'summarized', 'tinyint not null' );

		self::index( 'stamprange', '(post_id,var,span,stamp)');
		self::index( 'stamprange_summarized', '(post_id,var,span,summarized,stamp)');
	}

	public static function summarize($postId, $var, $fromSpan, $toSpan, $time) {
		if (!ThingData::spans[$fromSpan] || !ThingData::spans[$toSpan])
			throw new \Exception("Unknown span $fromSpan -> $toSpan");

		$currentSpanStart=ThingData::spanifyTimestamp($toSpan,$time);
		$datas=ThingData::findAllByQuery(
			"SELECT * ".
			"FROM   :table ".
			"WHERE  post_id=%s ".
			"AND    var=%s ".
			"AND    summarized=0 ".
			"AND    stamp<%s ".
			"AND    span=%s",
			$postId,
			$var,
			gmdate("Y-m-d H:i:s",$currentSpanStart),
			$fromSpan);

		$summaryDataByStamp=array();
		foreach ($datas as $data) {
			if ($fromSpan=="live") {
				$data->min=$data->value;
				$data->max=$data->value;
			}

			$t=strtotime($data->stamp." UTC");
			$t=ThingData::spanifyTimestamp($toSpan,$t);
			$spanStamp=gmdate("Y-m-d H:i:s",$t);

			if (!array_key_exists($spanStamp,$summaryDataByStamp)) {
				$spanData=new ThingData();
				$spanData->post_id=$postId;
				$spanData->var=$var;
				$spanData->value=0;
				$spanData->count=0;
				$spanData->span=$toSpan;
				$spanData->stamp=$spanStamp;
				$spanData->min=$data->min;
				$spanData->max=$data->max;

				$summaryDataByStamp[$spanStamp]=$spanData;
			}

			$summaryDataByStamp[$spanStamp]->value+=$data->value;
			$summaryDataByStamp[$spanStamp]->count++;

			if ($data->min<$summaryDataByStamp[$spanStamp]->min)
				$summaryDataByStamp[$spanStamp]->min=$data->min;

			if ($data->max>$summaryDataByStamp[$spanStamp]->max)
				$summaryDataByStamp[$spanStamp]->max=$data->max;
		}

		foreach ($summaryDataByStamp as $summaryData) {
			$summaryData->value/=$summaryData->count;
			$summaryData->save();
		}

		foreach ($datas as $data) {
			$data->summarized=1;
			$data->save();
		}
	}

	public static function spanifyTimestamp($span, $time) {
		return floor($time/ThingData::spans[$span])*ThingData::spans[$span];
	}

	public function getSpanifiedTimestamp() {
		return ThingData::spanifyTimestamp($this->span,strtotime($this->stamp." UTC"));
	}

	public function getTimestamp() {
		return strtotime($this->stamp." UTC");
	}

	public static function getSpanData($postId, $var, $span, $fromTimestamp, $toTimestamp) {
		$fromTimestamp=intval($fromTimestamp);
		$toTimestamp=intval($toTimestamp);

		$datas=ThingData::findAllByQuery(
			"SELECT * ".
			"FROM   :table ".
			"WHERE  $postId=%s ".
			"AND    var=%s ".
			"AND    span=%s ".
			"AND    stamp>=%s ".
			"AND    stamp<%s",
			$postId,
			$var,
			$span,
			gmdate("Y-m-d H:i:s",$fromTimestamp),
			gmdate("Y-m-d H:i:s",$toTimestamp)
		);

		$datasByStamp=array();
		foreach ($datas as $data)
			$datasByStamp[$data->getSpanifiedTimestamp()]=$data;

		for ($t=$fromTimestamp; $t<$toTimestamp; $t+=ThingData::spans[$span]) {
			if (!array_key_exists($t,$datasByStamp)) {
				$data=new ThingData();
				$data->stamp=gmdate("Y-m-d H:i:s",$t);
				$datasByStamp[$t]=$data;
			}
		}

		ksort($datasByStamp);

		return array_values($datasByStamp);
	}
}
