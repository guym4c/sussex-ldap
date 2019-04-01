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
        $connection = Connection::overProxy(self::TEST_USERNAME, self::TEST_PASSWORD);
        $result = $connection->authenticate(self::TEST_USERNAME, self::TEST_PASSWORD);
        $this->assertTrue($result->isBound());
    }

    /**
     * @test
     */
    public function directTest(): void {
        $connection = Connection::withoutProxy();
        $result = $connection->authenticate(self::TEST_USERNAME, self::TEST_PASSWORD);
        $this->assertTrue($result->isBound());
    }
}