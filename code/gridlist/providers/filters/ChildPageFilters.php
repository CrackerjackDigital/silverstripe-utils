<?php
namespace Modular\GridList\Providers\Filters;

use Modular\GridList\Interfaces\FiltersProvider;
use Modular\ModelExtension;

class ChildPageFilters extends ModelExtension implements FiltersProvider {
	use children;
}