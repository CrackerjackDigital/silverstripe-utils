<?php
namespace Modular\Interfaces;

use Modular\Exceptions\Debug;

interface Debugger
{
	public static function debugger($level = \Modular\Debugger::DefaultDebugLevel, $prefix = '');
	
	/**
	 * @param int|null new level or get
	 * @return $this|int
	 */
	public function level($level = null);
	
	/**
	 * Returns provided parameters in a common format, used by all the debug methods log, info, trace etc
	 * @param        $message
	 * @param        $severity
	 * @param string $source
	 * @return mixed
	 */
	public function formatMessage($message, $severity, $source = '');
	
	/**
	 * @param        $message
	 * @param string $source
	 * @return $this
	 */
	public function log($message, $facilities, $source = '');
	
	/**
	 * @param        $message
	 * @param string $source
	 * @return $this
	 */
	public function info($message, $source = '');
	
	/**
	 * @param        $message
	 * @param string $source
	 * @return $this
	 */
	public function trace($message, $source = '');
	
	/**
	 * @param        $message
	 * @param string $source
	 * @return $this
	 */
	public function notice($message, $source = '');
	
	/**
	 * @param        $message
	 * @param string $source
	 * @return $this
	 */
	public function warn($message, $source = '');
	
	/**
	 * @param        $message
	 * @param string $source
	 * @return $this
	 */
	public function error($message, $source = '');
	
	/**
	 * @param        $message
	 * @param string $source
	 * @throws Debug
	 */
	public function fail($message, $source = '');
	
	
	/**
	 * @param int $level
	 * @return $this
	 */
	public function toScreen($level);
	
	/**
	 * @param string $emailAddress
	 * @param int    $level
	 * @return $this
	 */
	public function toEmail($level, $emailAddress);
	
	/**
	 * @param int    $level
	 * @param string $filePathName to log events to if not provided one will be generated
	 * @return $this
	 */
	public function toFile($level, $filePathName = '');
	
}