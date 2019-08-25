<?php


namespace ivoglent\yii2\apm\components\db\mysql;


use yii\base\Event;

class Command extends \yii\db\Command
{
    const QUERY_MAX_LEN = 50;

    const EVENT_BEFORE_QUERY    = 'before_query';
    const EVENT_AFTER_QUERY    = 'after_query';

    private $query;
    private $name;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function internalExecute($rawSql)
    {
        $this->name = $this->getQueryName($rawSql);
        $this->query = $rawSql;

        Event::trigger($this, self::EVENT_BEFORE_QUERY);
        parent::internalExecute($rawSql);
        Event::trigger($this, self::EVENT_AFTER_QUERY);
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }



    /**
     * @param $sql
     * @return string
     */
    private function getQueryName($sql) {
        $sql = str_replace('`', "", $sql);
        if (strlen($sql) <= self::QUERY_MAX_LEN) {
            return $sql;
        }
        $command = strtoupper(trim(substr($sql,0, strpos($sql, ' '))));
        $name = $command;
        switch ($command) {
            case 'SELECT':
                if(preg_match('/FROM\s(.*?)\s/i', $sql, $matches)) {
                    $name = "$name FROM " . $matches[1];
                }
                break;
            case  'UPDATE':
                if(preg_match('/UPDATE \s(.*?)\s/i', $sql, $matches)) {
                    $name = "$name " . $matches[1];
                }
                break;
            case  'INSERT':
                if(preg_match('/INSERT INTO\s(.*?)\s/i', $sql, $matches)) {
                    $name = "$name INTO " . $matches[1];
                }
                break;
            case  'DELETE':
                if(preg_match('/FROM \s(.*?)\s/i', $sql, $matches)) {
                    $name = "$name FROM " . $matches[1];
                }
                break;
            case  'SHOW':
                $name = "$name TABLES";
                break;
            default:
                $name = substr($sql, 0, self::QUERY_MAX_LEN) . '...';
        }

        return $name;

    }
}