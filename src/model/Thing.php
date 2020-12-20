<?php

namespace thing;

require_once __DIR__."/ThingField.php";

class Thing {

	private static $currentThing;

	/**
	 * Constructor.
	 */
	public function __construct($post) {
		$this->post=$post;
	}

	/**
	 * Get thing by id.
	 */
	static function getByTitle($title) {
		$post=get_page_by_title($title,OBJECT,"thing");
		if ($post->post_type=="thing")
			return new Thing($post);

		return null;
	}

	/**
	 * Get the current thing being viewed or edited.
	 */
	static function getCurrent() {
		if (!Thing::$currentThing) {
			global $post;

			if (isset($_GET['post']))
				$usePost=get_post($_GET['post']);

			else if (isset($_POST['post_ID']))
				$usePost=get_post($_POST['post_ID']);

			else if ($post)
				$usePost=$post;

			if ($usePost && $usePost->post_type=="thing")
				Thing::$currentThing=new Thing($usePost);

			else
				Thing::$currentThing=null;
		}

		return Thing::$currentThing;
	}

	/**
	 * Make call to broker.
	 */
	public function brokerCall($call, $params=array()) {
		$url=$this->post->post_title."/".$call;
		return ThingPlugin::instance()->brokerCall($url,$params);
	}

	/**
	 * Get id.
	 */
	public function getId() {
		return $this->post->ID;
	}

	/**
	 * Init fields.
	 */
	private function initFields() {
		if (is_array($this->fields))
			return;

		$status=$this->brokerCall("status");

		$this->fields=array();
		$this->fieldsByKey=array();
		$this->fieldsByTabName=array();

		if ($status["fields"])
			foreach ($status["fields"] as $fieldData) {
				$field=new ThingField($fieldData);

				$this->fields[]=$field;
				$this->fieldsByKey[$field->getKey()]=$field;

				$tabName=$field->getTabName();
				if ($tabName)
					$this->fieldsByTabName[$tabName][]=$field;
			}
	}

	/**
	 * Query remote device for settings fields.
	 */
	public function getFields() {
		$this->initFields();

		return $this->fields;
	}

	/**
	 * Get loggable fields.
	 */
	public function getLogFields() {
		$fields=[];

		foreach ($this->fields as $field)
			if ($field->isLoggable())
				$fields[]=$field;

		return $fields;
	}

	/**
	 * Get field by key.
	 */
	public function getFieldByKey($key) {
		$this->initFields();

		return $this->fieldsByKey[$key];
	}

	/**
	 * Save updated fields.
	 */
	public function save() {
		$saveData=array();

		foreach ($this->fields as $field) {
			if ($field->isUpdated() && !$field->isReadOnly())
				$saveData[$field->getKey()]=json_encode($field->getValue());
		}

		$this->brokerCall("update",$saveData);
	}

	/**
	 * Get tab names.
	 */
	public function getTabNames() {
		$this->initFields();

		return array_keys($this->fieldsByTabName);
	}

	/**
	 * Get fields for tab.
	 */
	public function getFieldsByTabName($tabName) {
		return $this->fieldsByTabName[$tabName];
	}
}