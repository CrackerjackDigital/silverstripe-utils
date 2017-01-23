<?php
namespace Modular\GridList\Sequencers;

use Modular\GridList\Fields\Mode;
use Modular\GridList\GridList;
use Modular\GridList\Interfaces\GridListTempleDataProvider;
use Modular\GridList\Interfaces\ItemsSequencer;
use Modular\ModelExtension;

class GroupByField extends ModelExtension implements ItemsSequencer, GridListTempleDataProvider {
	const GroupByFieldName  = '';
	const TitleDBFieldClass = 'Text';
	const TemplateDataKey   = 'GroupedBy';

	/**
	 * Return an array of additional data to return to the template and make available via the $GridList template variable.
	 *
	 * @param array $existingData not used
	 * @return array to add to template data
	 */
	public function provideGridListTemplateData($existingData = []) {
		return [
			static::TemplateDataKey => static::GroupByFieldName,
		];
	}

	/**
	 * Sort items by EventDate desc, if we are in list mode then group by EventDate also update ItemCounts by group.
	 *
	 * @param \ArrayList|\DataList $items
	 * @param                      $filters
	 * @param array                $parameters
	 */
	public function sequenceGridListItems(&$items, $filters, &$parameters = []) {
		if ($items->count()) {
			// this was added by Mode field
			$mode = $parameters[Mode::TemplateDataKey];

			if ($mode == GridList::ModeList) {
				$groupByFieldName = static::GroupByFieldName;

				$items = \GroupedList::create(
					$items->Sort($groupByFieldName, 'desc')
				)->GroupedBy($groupByFieldName);

				foreach ($items as $group) {

					// de-dup items within the group
					if ($group->Children) {
						$group->Children->removeDuplicates();
					}

					// this is used for uniqueness testing by front-end when loading via ajax
					$group->Hash = md5($group->$groupByFieldName);

					// add the group title field
					$group->GroupTitle = $this->createGroupTitle($group->$groupByFieldName);
				}
			}
		}
	}

	/**
	 * @param $value
	 * @return \DBField
	 */
	protected function createGroupTitle($value) {
		return \DBField::create_field(static::TitleDBFieldClass, $value);
	}

}