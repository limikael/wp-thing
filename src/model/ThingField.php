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

			case "schedule":
				$fieldData=array(
					"type"=>"schedule",
					"name"=>$this->data["name"],
					"id"=>$id,
					"repeatable"=>TRUE,
					"text"=>array(
						"add_row_text"=>"+"
					)
				);
				break;

			case "text":
			default:
				$fieldData=array(
					"type"=>"text",
					"name"=>$this->data["name"],
					"id"=>$id
				);
				break;
		}

		if ($this->data["conditionKey"]) {
			$fieldData['attributes']=array(
				'data-conditional-id'=>$this->data["conditionKey"],
				'data-conditional-value'=>$this->data["conditionValue"],
			);
		}

//		$cmb2->add_field($fieldData);
		return $fieldData;
	}

	/**
	 * Get the value for CMB2.
	 */
	public function getValue() {
		return $this->data["value"];
	}

	/**
	 * Save value.
	 */
	public function updateValue($value) {
		$this->data["value"]=$value;
		$this->updated=TRUE;
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
}