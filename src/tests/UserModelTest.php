<?php
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    protected static $dbConnection;
    protected static $userId;

    public static function setUpBeforeClass()
    {
        self::$dbConnection = new \MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
    }

    public function testSuccessfulUserCreation()
    {
        $user = new \CRUD\Model\User(self::$dbConnection);
        $obj = new \stdClass;
        $obj->name = "Test";
        $obj->email = "test@test.com";
        $result = $user->create(['request_body' => $obj]);
        $this->assertEquals(true, $result['success']);
        self::$userId = $result['userId'];
    }

    public function testSuccessfulUserUpdate()
    {
        $user = new \CRUD\Model\User(self::$dbConnection);
        $obj = new \stdClass;
        $obj->name = "TestUpdate";
        $obj->email = "testUpdate@test.com";
        $result = $user->update(['request_body' => $obj, 'id' => self::$userId]);
        $this->assertEquals(true, $result['success']);
    }

    public function testGettingUniqueUser()
    {
        $user = new \CRUD\Model\User(self::$dbConnection);
        $result = $user->get(['id' => self::$userId]);
        $this->assertEquals('TestUpdate', $result[0]->name);
    }

    public function testGettingAllUsers()
    {
        $user = new \CRUD\Model\User(self::$dbConnection);
        $result = $user->get([]);
        $this->assertGreaterThan(0, count($result));
    }

    public function testExceptionUserUpdate()
    {
        $user = new \CRUD\Model\User(self::$dbConnection);
        $user->update(['id' => '']);
        $this->expectException(InvalidArgumentException::class);
    }

    public function testExceptionUserDelete()
    {
        $user = new \CRUD\Model\User(self::$dbConnection);
        $user->delete(['id' => '']);
        $this->expectException(InvalidArgumentException::class);
    }

    public function testSuccessfulUserDelete()
    {
        $user = new \CRUD\Model\User(self::$dbConnection);
        $result = $user->delete(['id' => self::$userId]);
        $this->assertEquals(true, $result['success']);
    }

    public static function tearDownAfterClass()
    {
        $user = new \CRUD\Model\User(self::$dbConnection);
        $user->delete(['id' => self::$userId]);
    }
}
