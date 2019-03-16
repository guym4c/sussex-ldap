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
    public function testServer(): void {
        $ldap = ldap_connect('ldaps://' . Connection::LDAP_DOMAIN);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, Connection::LDAP_PROTOCOL_VERSION);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, Connection::LDAP_NUM_REFERRALS);
        $this->assertTrue(ldap_bind($ldap, self::TEST_USERNAME . '@' . Connection::LDAP_DOMAIN, self::TEST_PASSWORD));
        ldap_unbind($ldap);
    }

    /**
     * @test
     */
    public function testConnection(): void {
        $ldap = new \Guym4c\SussexLdap\Connection();
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function testAuthorise(): void {
        $ldap = new \Guym4c\SussexLdap\Connection();
        $ldap->authorise(self::TEST_USERNAME, self::TEST_PASSWORD);
        $this->assertTrue(true);
    }
}