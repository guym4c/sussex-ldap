<?php

namespace Guym4c\SussexLdap;

use Toyota\Component\Ldap\{Core\Manager,
    Exception\BindException,
    Exception\ConnectionException,
    Platform\Native\Driver};

class Connection {

    const LDAP_DOMAIN = 'ad.susx.ac.uk';
    const LDAP_BASE_DN = 'cn=ad,cn=susx';
    const LDAP_PROTOCOL_VERSION = 3;
    const LDAP_NUM_REFERRALS = 0;

    /** @var Manager */
    private $ldap;

    /**
     * Connection constructor.
     * @throws ConnectionException
     */
    public function __construct() {
        $this->ldap = new Manager([
            'hostname' => self::LDAP_DOMAIN,
            'base_dn' => self::LDAP_BASE_DN,
            'options' => [
                LDAP_OPT_PROTOCOL_VERSION => self::LDAP_PROTOCOL_VERSION,
                LDAP_OPT_REFERRALS => self::LDAP_NUM_REFERRALS,
            ],
        ], new Driver());

        $this->ldap->connect();
    }

    public function authorise(string $user, string $password): BindResult {
        try {
            $this->ldap->bind(
                $user . '@' . self::LDAP_DOMAIN,
                $password
            );
        } catch (BindException $e) {
            return new BindResult(false, $e);
        }
        return new BindResult(true, null);
    }
}