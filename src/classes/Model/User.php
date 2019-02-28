<?php

namespace CRUD\Model;

/**
 * Undocumented class
 */
class User
{
    const COLLECTION = 'crud-php-mongodb.users';
    private $mongoDb;

    /**
     * Undocumented function
     *
     * @param \MongoDB\Driver\Manager $mongoDb
     */
    public function __construct(\MongoDB\Driver\Manager $mongoDb)
    {
        $this->mongoDb = $mongoDb;
    }
    
    /**
     * Undocumented function
     *
     * @return \MongoDB\Driver\Manager
     */
    private function getMongoDb(): \MongoDB\Driver\Manager
    {
        return $this->mongoDb;
    }

    /**
     * Undocumented function
     *
     * @param string $propoerty
     * @param [type] $value
     * @return void
     */
    public function __call(string $propoerty, $value)
    {
        if (! isset($this->$property)) {
            throw new \InvalidArgumentException();
        }

        $this->$property = $value;
    }
    
    /**
     * create
     *
     * @param  mixed $params
     *
     * @return array
     */
    public function create(array $params): array
    {
        $bulk = new \MongoDB\Driver\BulkWrite();
        $params['request_body']->userId = $this->getNextId();
        $bulk->insert($params['request_body']);
        $this->getMongoDb()->executeBulkWrite(self::COLLECTION, $bulk);

        return [
            "success" => true,
            'userId' => $params['request_body']->userId
        ];
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    private function getNextId(): int
    {
        $filter = [];
        $options = [
            'sort' => ['_id' => -1],
            'limit' => 1
        ];
        $query = new \MongoDB\Driver\Query($filter, $options);
        $cursor = $this->getMongoDb()->executeQuery(self::COLLECTION, $query);
        $lastDocument = $cursor->toArray();
        return ! empty($lastDocument[0]->userId) ? ++$lastDocument[0]->userId : 1;
    }

    public function update(array $params)
    {
        $bulk = new \MongoDB\Driver\BulkWrite();
        if (empty($params['id'])) {
            throw new \InvalidArgumentException('Id. parameter is mandatory');
        }
        $userId =  (int) $params['id'];
        $bulk->update(['userId' => $userId], ['$set' => $params['request_body']]);
        $this->getMongoDb()->executeBulkWrite(self::COLLECTION, $bulk);

        return [
            "success" => true,
            'userId' => $userId
        ];
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return array
     */
    public function get(array $params): array
    {
        $filter = ! empty($params['id']) ? ['userId' => (int) $params['id']] : [];
        $options = [
            'projection' => ['_id' => 0],
            'sort' => ['_id' => -1]
        ];
        $query = new \MongoDB\Driver\Query($filter, $options);
        $cursor = $this->getMongoDb()->executeQuery(self::COLLECTION, $query);
        return $cursor->toArray();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function delete(array $params): array
    {
        $bulk = new \MongoDB\Driver\BulkWrite();
        if (empty($params['id'])) {
            throw new \InvalidArgumentException('Id. parameter is mandatory');
        }
        $userId =  (int) $params['id'];
        $bulk->delete(['userId' => $userId]);
        $this->getMongoDb()->executeBulkWrite(self::COLLECTION, $bulk);

        return [
            "success" => true,
            'userId' => $userId
        ];
    }
}
