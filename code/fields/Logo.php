<?php
namespace Modular\Fields;

use FormField;

class Logo extends Image {
	const RelationshipName        = 'Logo';
	const DefaultUploadFolderName = 'logos';

	/**
	 * Return the single related image, shouldn't really get here as the extended model's field accessor should be called first.
	 *
	 * @return Image|null
	 */
	public function Logo() {
		return $this()->{self::RelationshipName}();
	}

	public function Logos() {
		return new \ArrayList(array_filter([$this->Logo()]));
	}

}