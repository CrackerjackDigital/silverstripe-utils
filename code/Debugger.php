<?php
namespace Modular;

use Controller;
use Filesystem;
use Modular\Exceptions\Debug;
use SS_Log;
use SS_LogEmailWriter;
use SS_LogFileWriter;

class Debugger extends Object {
	use bitfield;
	use enabler;

	const DefaultSendEmailsFrom = 'servers@moveforward.co.nz';

	// options in numerically increasing order, IMO Zend did this the wrong way, 0 should always be 'no' or least
	const DebugErr    = SS_Log::ERR;        // 3
	const DebugWarn   = SS_Log::WARN;       // 4
	const DebugNotice = SS_Log::NOTICE;     // 5
	const DebugInfo   = SS_Log::INFO;       // 6
	const DebugTrace  = SS_Log::DEBUG;      // 7

	// disable all debugging
	const DebugOff = 16;

	const DebugFile   = 32;
	const DebugScreen = 64;
	const DebugEmail  = 128;

	private static $levels = [
		self::DebugErr    => 'ERROR',
		self::DebugWarn   => 'WARN',
		self::DebugNotice => 'NOTICE',
		self::DebugInfo   => 'INFO',
		self::DebugTrace  => 'TRACE',
	];

	// TODO implement writing a log file per class as well as global log, may need to move this into trait
	// as we need to get the class name for the file maybe, though SS_Log already handles backtrace it doesn't
	// og back far enough
	const DebugPerClass = 256;

	// debug warning level and debug to file and screen
	const DefaultDebugLevel = 102;

	private static $send_emails_from = self::DefaultSendEmailsFrom;

	// name of log file to create if none supplied to toFile
	private static $log_file = 'silverstripe.log';

	// path to create log file in relative to assets folder if no file supplied to toFile
	private static $log_path = 'logs';

	// set when toFile is called.
	private $logFilePathName;

	// when destructor is called on the logger email the log file to this address
	private $emailLogFileTo;

	// where are messages coming from?
	private $source;

	// what level will we trigger at
	private $level;

	// what level is on-screen trigger, generally pemissive
	private $screenLevel = self::DebugTrace;

	public function __construct($level = self::DefaultDebugLevel, $source = '') {
		parent::__construct();
		$this->init($level, $source);
	}

	/**
	 * If emailLogFileTo and logFilePathName is set then email the logFilePathName content if not empty
	 */
	public function __destruct() {
		if ($this->emailLogFileTo && $this->logFilePathName) {
			if ($body = file_get_contents($this->logFilePathName)) {
				$email = new \Email(
					$this->config()->get('send_emails_from'),
					$this->emailLogFileTo,
					'Debug log from: ' . \Director::protocolAndHost(),
					$body
				);
				$email->sendPlain();
			}
		}
	}

	public static function debugger($level = self::DefaultDebugLevel, $source = '') {
		$class = get_called_class();
		return new $class($level, $source);
	}

	public function level($level = null) {
		if (func_num_args()) {
			$this->level = $level;
			if ($level & static::DebugScreen) {
				$this->screenLevel = $level;
			}
			return $this;
		} else {
			return $this->level;
		}
	}

	public function source($source = null) {
		if (func_num_args()) {
			$this->source = $source;
			return $this;
		} else {
			return $this->source;
		}
	}

	/**
	 * Set levels and source and if flags indicate debugging to file screen or email initialise those aspects of debugging using defaults from config.
	 * @param      $level
	 * @param null $source
	 * @return $this
	 */
	protected function init($level, $source = null) {
		SS_Log::clear_writers();

		$this->level($level);
		$this->source($source);

		if ($this->bitfieldTest($level, self::DebugFile)) {
			if ($logFile = static::config()->get('log_file')) {
				$this->toFile($level, $logFile);
			}
		}
		if ($this->bitfieldTest($level, self::DebugScreen)) {
			$this->toScreen($level);
		}
		if ($this->bitfieldTest($level, self::DebugEmail)) {
			if ($email = $this->config()->get('log_email')) {
				static::toEmail($email, $level);
			}
		}
		return $this;
	}

	/**
	 *
	 * @param string $message
	 * @param string $severity e.g. 'ERR', 'TRC'
	 * @param string $source
	 * @return mixed
	 */
	public function formatMessage($message, $severity, $source = '') {
		return implode("\t", [
			date('Y-m-d'),
			date('h:i:s'),
			"$severity:",
			$source,
			$message,
		]);
	}

	/**
	 * Return level if level from facilities less than current level otherwise false.
	 *
	 * @param $facilities
	 * @return bool|int
	 */
	protected function lvl($facilities, $compareToLevel = null) {
		// strip out non-level facilities
		$level = $facilities & (self::DebugErr | self::DebugWarn | self::DebugNotice | self::DebugInfo | self::DebugTrace);
		$compareToLevel = is_null($compareToLevel) ? $this->level() : $compareToLevel;
		return $level <= $compareToLevel ? $level : false;
	}

	public function log($message, $facilities, $source = '') {
		$levels = $this->config()->get('levels');
		$level = $this->lvl($facilities);
		$source = $source ?: $this->source();

		if ($level) {
			SS_Log::log("$source: $message", $level);
		}
		$toScreen = $this->lvl($facilities, $this->screenLevel);
		if ($toScreen) {
			$str = isset($levels[$toScreen]) ? $levels[$toScreen] : '???';
			echo $this->formatMessage($message, $str, $source) . (\Director::is_cli() ? '' : '<br/>') . PHP_EOL;
		}
		return $this;
	}

	public function info($message, $source = '') {
		$this->log($message, self::DebugInfo, $source);
		return $this;
	}

	public function trace($message, $source = '') {
		if ($this->lvl(self::DebugTrace)) {
			echo $message . (\Director::is_cli() ? '' : "<br/>") . PHP_EOL;
			ob_flush();
		}
		$this->log($message, self::DebugTrace, $source);
		return $this;
	}

	public function notice($message, $source = '') {
		$this->log($message, self::DebugNotice, $source);
		return $this;
	}

	public function warn($message, $source = '') {
		$this->log($message, self::DebugWarn, $source);
		return $this;
	}

	public function error($message, $source = '') {
		$this->log($message, self::DebugErr, $source);
		return $this;
	}

	public function toScreen($level) {
		$this->screenLevel = $level;
	}

	/**
	 * Set the email address to send emails to
	 *
	 * @param $address
	 * @param $level
	 * @return $this
	 */
	public function toEmail($address, $level) {
		if ($address) {
			SS_Log::add_writer(
				new SS_LogEmailWriter($address),
				$level
			);
		};
		return $this;
	}

	/**
	 * Log to provided file or to a generated file. Filename is relative to site root if it starts with a '/' otherwise is interpreted as relative
	 * to assets folder. Checks to make sure final log file path is inside the web root.
	 *
	 * @param  int    $level        only log above this level
	 * @param  string $filePathName log to this file or if not supplied generate one
	 * @return $this
	 */
	public function toFile($level, $filePathName = '') {
		$baseFolder = \Director::baseFolder();

		if ($filePathName) {
			$filePathName = realpath(
				substr($filePathName, 0, 1) == '/'
					? ($baseFolder . "/$filePathName")
					: (ASSETS_PATH . "/$filePathName")
			);

			// test we are in web root after resolving absolute file path otherwise log to a default log file
			if (substr($filePathName, 0, strlen($baseFolder)) == $baseFolder) {
				// ok
				$this->logFilePathName = $filePathName;
			} else {
				$this->logFilePathName = $this->makeLogFileName();
			}

		} else {
			$this->logFilePathName = $this->makeLogFileName();
		}

		SS_Log::add_writer(
			new SS_LogFileWriter($this->logFilePathName),
			$this->lvl($level)
		);

		// log an warning if we got an invalid path above
		if ($filePathName && (substr($filePathName, 0, strlen($baseFolder)) != $baseFolder)) {
			$this->warn("Invalid file path outside of web root '$filePathName' using '$this->logFilePathName' instead");
		}
		return $this;
	}

	/**
	 * At end of Debugger lifecycle file set by toFile will be sent to this email address.
	 *
	 * @param $emailAddress
	 * @return $this
	 */
	public function sendFile($emailAddress) {
		$this->emailLogFileTo = $emailAddress;
		return $this;
	}

	/**
	 * Returns a log file path and name relative to the assets folder using config.log_path. If path doesn't exist
	 * and is in the assets folder then will try and create it (recursively). If it is outside
	 * the assets folder then will not try and create the path.
	 *
	 * @return string
	 */
	protected function makeLogFileName() {
		if (!$filePathName = static::config()->get('log_file')) {
			$path = static::config()->get('log_path');
			$date = date('Ymd_his');

			$prefix = $this->source
				? ("{$this->source}-$date-")
				: ("$date-");

			$fileName = basename(tempnam($path, $prefix));
		} else {
			$path = dirname($filePathName);
			$fileName = basename($filePathName, '.log');
		}
		$path = realpath(Controller::join_links(
			ASSETS_PATH,
			$path
		));
		if (substr($path, 0, strlen(ASSETS_PATH)) == ASSETS_PATH) {
			// we only try and make a logging directory if we are inside the assets folder
			Filesystem::makeFolder($path);
		}
		return "$path/$fileName.log";
	}
}
