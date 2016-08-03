<?php
namespace Modular;

use \HtmlEditorField;

class HasSynopsisField extends HasFieldsExtension {
	const FieldName = 'Synopsis';

	private static $db = [
		self::FieldName => 'HTMLText',
	];
	public function cmsFields() {
		return [
			HtmlEditorField::create('Synopsis', 'Synopsis')->setRows(5),
		];
	}
}
