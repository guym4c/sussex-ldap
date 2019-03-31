<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Guym4c\SussexLdap\Connection;
use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase {

    const TEST_USERNAME = '';
    const TEST_PASSWORD = '';

    /**
     * @test
     */
    public function proxiedTest(): void {
        $connection = Connection::constructWithProxy(self::TEST_USERNAME, self::TEST_PASSWORD);
        $connection->authenticate(self::TEST_USERNAME, self::TEST_PASSWORD);
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function directTest(): void {
        $connection = Connection::constructWithoutProxy();
        $connection->authenticate(self::TEST_USERNAME, self::TEST_PASSWORD);
        $this->assertTrue(true);
    }
}