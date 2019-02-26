<?php

namespace CRUD\Controller;

/**
 * Undocumented class
 */
class User
{
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
     * @return void
     */
    private function getMongoDb()
    {
        return $this->mongoDb;
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return void
     */
    public function create(array $params): array
    {
        $user = new \CRUD\Model\User($this->getMongoDb());
        return $user->create($params);
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return void
     */
    public function update(array $params)
    {
        $user = new \CRUD\Model\User($this->getMongoDb());
        return $user->update($params);
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return array
     */
    public function get(array $params): array
    {
        $user = new \CRUD\Model\User($this->getMongoDb());
        return $user->get($params);
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return array
     */
    public function delete(array $params): array
    {
        $user = new \CRUD\Model\User($this->getMongoDb());
        return $user->delete($params);
    }
}
