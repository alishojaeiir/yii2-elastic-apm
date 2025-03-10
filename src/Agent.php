<?php


namespace ivoglent\yii2\apm;

use Elastic\Apm\PhpAgent\Agent as BaseAgent;
use Elastic\Apm\PhpAgent\Exception\RuntimeException;
use Elastic\Apm\PhpAgent\Model\Context\SpanContext;
use Elastic\Apm\PhpAgent\Model\Span;
use Exception;
use Psr\Http\Message\RequestInterface;
use Yii;

class Agent extends BaseAgent
{
    public $transactionStarted = false;

    public function startTransaction(string $name, string $type, ?string $id = null)
    {
        try {
            parent::startTransaction($name, $type, $id); // TODO: Change the autogenerated stub
            $this->transactionStarted = true;
        } catch (Exception $e) {
            $this->transactionStarted = false;
            Yii::error($e->getMessage());
        }
    }

    public function startTrace(string $name, string $type): Span
    {
        try {
            return parent::startTrace($name, $type);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
        }
    }

    public function stopTrace(?string $id = null, ?SpanContext $context = null) {
        try {
            return parent::stopTrace($id, $context);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
        }
    }

    /**
     * @param \Throwable $throwable
     * @return mixed|void
     */
    public function notifyException(\Throwable $throwable) {
        if (!$this->isReady()) {
            return;
        }
        try {
            $this->getTransaction();
            return parent::notifyException($throwable);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
        }
    }

    public function stopTransaction(?string $result = null): void
    {
        if (!$this->isReady()) {
            return;
        }
        try {
            parent::stopTransaction($result); // TODO: Change the autogenerated stub
            $this->transactionStarted = false;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }
    }

    public function send(?RequestInterface $request = null, array $options = []): bool
    {
        try {
            $request = $this->makeRequest();
            return parent::send($request);
        } catch (Exception $e) {
            Yii::error($e);
        }
        return false;
    }

    public function isReady() {
        return $this->transactionStarted;
    }
}