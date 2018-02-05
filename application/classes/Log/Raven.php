<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Raven log writer. Writes out messages and stores them in Sentry.
 *
 * @package    Kohana
 * @category   Logging
 * @author     Kohana Team
 * @copyright  (c) 2008-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Log_Raven extends Log_Writer {

    protected $raven;

    protected static $errorLevelMap = [
        Kohana_Log::EMERGENCY => Raven_Client::FATAL,
        Kohana_Log::ALERT => Raven_Client::FATAL,
        Kohana_Log::CRITICAL => Raven_Client::FATAL,
        Kohana_Log::ERROR => Raven_Client::ERROR,
        Kohana_Log::WARNING => Raven_Client::WARNING,
        Kohana_Log::NOTICE => Raven_Client::INFO,
        Kohana_Log::INFO => Raven_Client::INFO,
        Kohana_Log::DEBUG => Raven_Client::DEBUG,
        8 => Raven_Client::DEBUG,
    ];

    /**
     * Creates a new raven logger.
     *
     *     $writer = new Raven_Log();
     *
     * @param   string  log directory
     * @return  void
     */
    public function __construct($client)
    {
        $this->raven = $client;
    }

    /**
     * Writes each of the messages into the raven.
     *
     *     $writer->write($messages);
     *
     * @param   array   messages
     * @return  void
     */
    public function write(array $messages)
    {
        foreach ($messages as $message)
        {
            if (isset($message['additional']['exception']))
            {
                if ($message['additional']['exception'] instanceof HTTP_Exception) {
                    continue;
                }

                // Write each message into the log file
                // Format: time --- level: body
                $this->raven->captureException($message['additional']['exception']);
            } else {
                // Write each message into the log file
                // Format: time --- level: body
                $this->raven->captureMessage(
                    $this->format_message($message),
                    $message,
                    [
                        'level' => $this->mapRavenLevel($message['level'])
                    ],
                    $message['trace']
                );
            }
        }
    }

    private function mapRavenLevel($level) {
        return isset(self::$errorLevelMap[$level]) ? self::$errorLevelMap[$level] : Raven_Client::INFO;
    }
}
