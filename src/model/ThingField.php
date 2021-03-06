<?php

namespace thing;

/**
 * Field data.
 */
class ThingField {

	/**
	 * Constructor.
	 */
	public function __construct($fieldData) {
		$this->data=$fieldData;
	}

	/**
	 * Get label.
	 */
	public function getLabel() {
		return $this->data["name"];
	}

	/**
	 * Key
	 */
	public function getKey() {
		return $this->data["key"];
	}

	/**
	 * Create field definitions.
	 */
	public function getCmbDef() {
		$id=$this->data["key"];

		$fieldData=NULL;
		switch ($this->data["type"]) {
			case "select":
				$fieldData=array(
					"type"=>"select",
					"name"=>$this->data["name"],
					"id"=>$id,
					"options"=>$this->data["options"]
				);
				break;

			case "boolean":
				$fieldData=array(
					"type"=>"select",
					"name"=>$this->data["name"],
					"id"=>$id,
					"options"=>array(
						"false"=>"Off",
						"true"=>"On"
					)
				);
				break;

			case "intervaltimer":
				$fieldData=array(
					"type"=>"intervaltimer",
					"name"=>$this->data["name"],
					"id"=>$id,
					"repeatable"=>TRUE,
					"text"=>array(
						"add_row_text"=>"+"
					)
				);
				break;

			case "duration":
				$fieldData=array(
					"type"=>"duration",
					"name"=>$this->data["name"],
					"id"=>$id,
				);
				break;

			case "text":
				$fieldData=array(
					"type"=>"text",
					"name"=>$this->data["name"],
					"id"=>$id
				);
				break;

			case "number":
			case "number-not-nan":
				$fieldData=array(
					"type"=>"text_small",
					"name"=>$this->data["name"],
					"id"=>$id
				);
				break;

			default:
				throw new \Exception("Unknown field type: ".$this->data["type"]);
				break;
		}

		$fieldData['attributes']=array();

		if ($this->data["conditionKey"]) {
			$fieldData['attributes']=array(
				'data-conditional-id'=>$this->data["conditionKey"],
				'data-conditional-value'=>wp_json_encode($this->data["conditionValue"])
			);
		}

		if ($this->data["readonly"])
			$fieldData["attributes"]["readonly"]=TRUE;

		return $fieldData;
	}

	/**
	 * Get the value for CMB2.
	 */
	public function getCmb2Value() {
		switch ($this->data["type"]) {
			case "duration":
				$secs=floor($this->data["value"]/1000);

				return array(
					"minutes"=>floor($secs/60),
					"seconds"=>$secs%60,
				);
				break;

			case "boolean":
				if ($this->data["value"])
					return "true";

				else
					return "false";
				break;

			default:
				return $this->data["value"];
				break;
		}
	}

	/**
	 * Get value to be sent to device.
	 */
	public function getValue() {
		return $this->data["value"];
	}

	/**
	 * Save value.
	 */
	public function updateValueWithCmb2Data($value) {
		switch ($this->data["type"]) {
			case "duration":
				$value=$value["minutes"]*60*1000+$value["seconds"]*1000;
				break;
		}

		$this->data["value"]=$value;
		$this->updated=TRUE;
	}

	/**
	 * Is this a readonly field.
	 */
	public function isReadOnly() {
		return (
			array_key_exists("readonly",$this->data) && 
			$this->data["readonly"]
		);
	}

	/**
	 * Is this field updated?
	 */
	public function isUpdated() {
		return $this->updated;
	}

	/**
	 * Get tab.
	 */
	public function getTabName() {
		return $this->data["tab"];
	}

	/**
	 * Is this field loggable?
	 */
	public function isLoggable() {
		return (
			array_key_exists("log",$this->data) && 
			$this->data["log"]
		);
	}
}