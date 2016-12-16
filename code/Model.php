<?php
namespace Modular;
/**
 * Model
 *
 * @package Modular
 * @property int ID
 */
class Model extends \DataObject {
	use lang;
	use related;
	use reflection;
	use debugging;

	/**
	 * Invoking a type returns itself.
	 *
	 * @return $this
	 */
	public function __invoke() {
		return $this;
	}

	/**
	 * Patch until php 5.6 static::class is widely available on servers
	 *
	 * @return string
	 */
	public static function class_name() {
		return get_called_class();
	}

	public function getModelClass() {
		return get_class($this);
	}

	public function getModelID() {
		return $this->ID;
	}

	public function getModelInstance() {
		return $this;
	}

}
