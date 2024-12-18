<?php


namespace ivoglent\yii2\apm\listeners;


use Elastic\Apm\PhpAgent\Exception\RuntimeException;
use Elastic\Apm\PhpAgent\Model\Context\DbContext;
use Elastic\Apm\PhpAgent\Model\Span;
use GuzzleHttp\Exception\GuzzleException;
use ivoglent\yii2\apm\Listener;
use Yii;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\web\Application;
use yii\web\Controller;
use yii\web\Response;

class RequestListener extends Listener
{
    public $skipActions = [];

    public function init()
    {
        parent::init();
        $this->skipActions = array_merge($this->skipActions, [
            Yii::$app->errorHandler->errorAction
        ]);
        Event::on(Response::class, Response::EVENT_AFTER_SEND, [$this, 'afterRequest']);
        Event::on(Controller::class, Controller::EVENT_BEFORE_ACTION, [$this, 'beforeAction']);
        if (!Yii::$app->request->isOptions) {
            $txtName = Yii::$app->request->url;
            $this->agent->startTransaction($txtName, 'http');
        }

    }

    /**
     * @param Event $event
     * @throws RuntimeException
     * @throws GuzzleException
     */
    public function afterRequest(Event $event) {
        Yii::info('Request stop', 'apm');
        try {
            /** @var Application $sender */
            $sender = $event->sender;
            if (!Yii::$app->request->isOptions && $this->agent->transactionStarted) {
                $result = $this->convertStatusCode(Yii::$app->response->getStatusCode());
                //$this->agent->getTransaction()->setResult($result);
                //$this->agent->getTransaction()->stop($result);
                $this->agent->stopTransaction($result);
                return true;
            }
        } catch (Exception $e) {
            Yii::error($e);
        }
        return false;
    }

    /**
     * @param ActionEvent $event
     * @throws RuntimeException
     */
    public function beforeAction(ActionEvent $event) {
        Yii::info('Action start', 'apm');
        $txtName = sprintf('%s.%s', Yii::$app->controller->id, $event->action->id);
        if (false ===  $this->agent->isReady()) {
            $this->agent->startTransaction($txtName, 'http');
        }
        if (!empty(Yii::$app->controller->module)) {
            $txtName = Yii::$app->controller->module->id . '.' . $txtName;
        }
        if (!Yii::$app->request->isOptions && !$this->isSkipActions(str_replace('.', '/', $txtName))) {
            $this->agent->getTransaction()->setName($txtName);
        }

    }

    /**
     * @param $actionId
     * @return bool
     */
    private function isSkipActions($actionId) {
        return in_array($actionId, $this->skipActions);
    }

    /**
     * @param $code
     * @return string
     */
    private function convertStatusCode($code) {
        if ($code >= 100 && $code < 200) {
            return '1xx';
        }
        if ($code >= 200 && $code < 300) {
            return '2xx';
        }
        if ($code >= 300 && $code < 400) {
            return '3xx';
        }
        if ($code >= 400 && $code < 500) {
            return '4xx';
        }
        if ($code >= 500 && $code < 600) {
            return '5xx';
        }
    }
}