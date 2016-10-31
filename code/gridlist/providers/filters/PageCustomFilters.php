<?php
namespace Modular\GridList\Constraints\Filters;

use Modular\Fields\Title;
use Modular\GridList\Interfaces\FilterConstraints;
use Modular\GridList\Interfaces\FiltersProvider;
use Modular\ModelExtension;
use Modular\Models\GridListFilter;

/**
 * Allows filters to be explicitly set on a page-by-page basis by setting config.gridlist_custom_filters on a page model.
 *
 * @package Modular\GridList\Providers\Filters
 */
class PageCustomFilters extends ModelExtension implements FiltersProvider, FilterConstraints {

	/**
	 * Return the current pages config.gridlist_custom_filters if set or empty array.
	 * @return array
	 */
	public function provideGridListFilters() {
		$page = \Director::get_current_page();
		if ($page instanceof \CMSMain) {
			$page = $page->currentPage();
		}
		$filters = new \ArrayList();
		$customFilters = $page->config()->get('gridlist_custom_filters') ?: [];
		foreach ($customFilters as $filter => $title) {
			$filters->push(new GridListFilter([
				Title::SingleFieldName => $title,
			    GridListFilter::TagFieldName => $filter
			]));
		}
		return $filters;
	}

	/**
	 * Make sure only the custom filters are in there.
	 * @param $filters
	 */
	public function constrainGridListFilters(&$filters) {
		if ($customFilters = $this->provideGridListFilters()) {
			if ($customFilters->count()) {
				$filters = $customFilters;
			}
		}
	}
}