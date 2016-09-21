<?php
namespace Modular\GridList\Constraints\Filters;

use Modular\Fields\Field;
use Modular\Model;
use Modular\Relationships\HasGridListFilters;

/**
 * Removes filters which don't exist in any items, should be added to GridList host (e.g. GridListBlock)
 *
 * @package Modular\GridList\Constraints\Filters
 */
class OnlyPresentInItems extends Field {
	const SingleFieldName   = 'OnlyMatchingFilters';
	const SingleFieldSchema = 'Boolean';

	private static $defaults = [
		self::SingleFieldName => false,
	];

	/**
	 * @param \DataList $filters list of GridListFilter models
	 * @param \DataList $items   list of Pages and other models which could appear in a grid.
	 * @return \ArrayList
	 */
	public function constrainGridListFilters(&$filters, $items) {
		$out = new \ArrayList();
		if ($this()->{self::SingleFieldName}) {
			$ids = $filters->column('ID');

			// this is where we keep track of GridListFilters which have been found on items where ID is the key
			$foundFilters = array_combine(
				$ids,
				array_fill(0, count($ids), false)
			);

			foreach ($foundFilters as $filterID => &$found) {
				/** @var \Page|Model $item */
				foreach ($items as $item) {
					if ($item->hasExtension(HasGridListFilters::class_name())) {
						if ($itemFilters = $item->{HasGridListFilters::relationship_name()}()->column('ID')) {
							if (in_array($filterID, $itemFilters)) {
								$found = true;
								break;
							}
						}
					}
				}
			}
			foreach ($filters as $filter) {
				if (isset($foundFilters[ $filter->ID ])) {
					$out->push($filter);
				}
			}
		}
		$filters = $out->count() ? $out : $filters;
	}
}

