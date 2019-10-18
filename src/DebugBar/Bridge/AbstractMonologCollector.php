<?php

namespace DebugBar\Bridge;

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\MessagesAggregateInterface;
use DebugBar\DataCollector\Renderable;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

abstract class AbstractMonologCollector extends AbstractProcessingHandler implements DataCollectorInterface, Renderable, MessagesAggregateInterface
{
    protected $name;

    protected $records = array();

    /**
     * @param Logger $logger
     * @param int $level
     * @param boolean $bubble
     * @param string $name
     */
    public function __construct(Logger $logger = null, $level = Logger::DEBUG, $bubble = true, $name = 'monolog')
    {
        parent::__construct($level, $bubble);
        $this->name = $name;
        if ($logger !== null) {
            $this->addLogger($logger);
        }
    }

    /**
     * Adds logger which messages you want to log
     *
     * @param Logger $logger
     */
    public function addLogger(Logger $logger)
    {
        $logger->pushHandler($this);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->records;
    }

    /**
     * @return array
     */
    public function collect()
    {
        return array(
            'count' => count($this->records),
            'records' => $this->records
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        $name = $this->getName();
        return array(
            $name => array(
                "icon" => "suitcase",
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "$name.records",
                "default" => "[]"
            ),
            "$name:badge" => array(
                "map" => "$name.count",
                "default" => "null"
            )
        );
    }
}