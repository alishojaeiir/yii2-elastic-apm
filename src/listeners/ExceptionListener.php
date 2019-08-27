<?php


namespace ivoglent\yii2\apm\listeners;


use ivoglent\yii2\apm\components\ConsoleErrorHandler;
use ivoglent\yii2\apm\components\WebErrorHandler;
use ivoglent\yii2\apm\Listener;
use yii\base\Event;
use yii\console\ErrorHandler;

class ExceptionListener extends Listener
{
    public $skipExceptions = [];
    public function init()
    {
        parent::init();
        Event::on(ConsoleErrorHandler::class, ConsoleErrorHandler::EVENT_ON_ERROR, [$this, 'onError']);
        Event::on(WebErrorHandler::class, WebErrorHandler::EVENT_ON_ERROR, [$this, 'onError']);
    }

    /**
     * @param Event $event
     * @throws \Elastic\Apm\PhpAgent\Exception\RuntimeException
     */
    public function onError(Event $event) {
        /** @var WebErrorHandler|ConsoleErrorHandler $sender */
        $sender = $event->sender;
        foreach ($this->skipExceptions as $exception) {
            if ($sender->errorException instanceof $exception) {
                return;
            }
        }
        if ($this->agent->transactionStarted) {
            $this->agent->notifyException($sender->errorException);
            $this->agent->stopTransaction('5xx');
        }

    }
}