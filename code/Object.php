<?php
namespace Modular;

require_once 'traits/bitfield.php';
require_once 'traits/cache.php';
require_once 'traits/config.php';
require_once 'traits/debugging.php';
require_once 'traits/enabler.php';
require_once 'traits/json.php';
require_once 'traits/lang.php';

class ModularObject extends \Object {
	use cache;
	use lang;
	use bitfield;
	use config;
	use debugging;

}