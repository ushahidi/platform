<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Log writer abstract class. All [Log] writers must extend this class.
 *
 * @package    Kohana
 * @category   Logging
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Log_Writer {

	/**
	 * Numeric log level to string lookup table.
	 * @var array 
	 */
	protected $_log_levels = array(
		LOG_EMERG   => 'EMERGENCY',
		LOG_ALERT   => 'ALERT',
		LOG_CRIT    => 'CRITICAL',
		LOG_ERR     => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE  => 'NOTICE',
		LOG_INFO    => 'INFO',
		LOG_DEBUG   => 'DEBUG',
		8           => 'STRACE',
	);

	/**
	 * Write an array of messages.
	 *
	 *     $writer->write($messages);
	 *
	 * @param   array   $messages
	 * @return  void
	 */
	abstract public function write(array $messages);

	/**
	 * Allows the writer to have a unique key when stored.
	 *
	 *     echo $writer;
	 *
	 * @return  string
	 */
	final public function __toString()
	{
		return spl_object_hash($this);
	}

} // End Kohana_Log_Writer
