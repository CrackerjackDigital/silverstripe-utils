<?php
namespace Modular;

use Director;
use Modular\Traits\debugging;
use Modular\Traits\enabler;

abstract class Task extends \BuildTask {
	use enabler;
	use debugging;

	const EnablerConfigVar = 'task_enabled';

	// can't use 'enabled' as that is a member var on BuildTask
	private static $task_enabled = true;

	abstract public function execute($request);

	/**
	 * Task can run if enabled and either is_cli or logged in as ADMIN.
	 * @return bool
	 */
	public function canRun() {
		return static::enabled() && (Director::isDev() || Director::is_cli() || \Permission::check('ADMIN'));
	}

	final public function run($request) {
		$this->debugger()->toScreen(Debugger::DebugAll);

		$taskName = get_class($this);

		if ($this->canRun()) {
			if (!Director::is_cli()) {
				ob_start('nl2br');
			}
			$this->debug_info("Starting task $taskName");

			$this->execute($request);

			$this->debug_info("End of task $taskName");

			ob_end_flush();
		} else {
			$this->debug_info("Task $taskName not allowed to run");
		}
	}

}