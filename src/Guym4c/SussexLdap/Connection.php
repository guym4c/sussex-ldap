<?php

namespace Guym4c\SussexLdap;

use RuntimeException;
use Ssh\{Authentication, Configuration, Session};
use Toyota\Component\Ldap\{Core\Manager, Exception\BindException, Platform\Native\Driver};

class Connection {

    const SSH_PROXY_DOMAIN = 'unix.sussex.ac.uk';
    const LDAP_DOMAIN = 'ad.susx.ac.uk';
    const LDAP_BASE_DN = 'cn=ad,cn=susx';
    const LDAP_PROTOCOL_VERSION = 3;
    const LDAP_NUM_REFERRALS = 0;

    /** @var Manager */
    private $ldap;

    /** @var Session */
    private $ssh;

    /** @var bool */
    private $proxied;

    /**
     * Connection constructor.
     * @param bool $proxied
     */
    private function __construct(bool $proxied) {
        $this->proxied = $proxied;
    }

    /**
     * Proxied Connection constructor.
     *
     * @param string $proxyUsername
     * @param string|null $proxyPassword
     * @return Connection
     */
    public static function constructWithProxy(string $proxyUsername, ?string $proxyPassword = null): self {
        $connection = new Connection(true);

        if ($proxyPassword == null) {
            $auth = new Authentication\Agent($proxyUsername);
        } else {
            $auth = new Authentication\Password($proxyUsername, $proxyPassword);
        }

        $connection->ssh = new Session(new Configuration(self::SSH_PROXY_DOMAIN), $auth);
        return $connection;
    }

    /**
     * Unproxied Connection constructor.
     *
     * @param array $params
     * @return Connection
     */
    public static function constructWithoutProxy(array $params = []): self {
        $connection = new Connection(false);
        $connection->ldap = new Manager(array_merge($params, [
            'hostname' => self::LDAP_DOMAIN,
            'base_dn' => self::LDAP_BASE_DN,
            'options' => [
                LDAP_OPT_PROTOCOL_VERSION => self::LDAP_PROTOCOL_VERSION,
                LDAP_OPT_REFERRALS => self::LDAP_NUM_REFERRALS,
            ],
        ]), new Driver());
        return $connection;
    }

    /**
     * Authenticate a user.
     *
     * @param string $user
     * @param string $password
     * @return BindResult
     */
    public function authenticate(string $user, string $password): BindResult {

        $user = $user . '@' . self::LDAP_DOMAIN;

        if ($this->proxied) {
            return $this->authenticateOverProxy($user, $password);
        } else {
            return $this->authenticateOverLdap($user, $password);
        }
    }


    private function authenticateOverProxy(string $user, string $password): BindResult {

        try {
            $result = $this->ssh->getExec()->run(sprintf('ldapwhoami -h %s -D %s -xw %s', self::LDAP_DOMAIN, $user, $password));
        } catch (RuntimeException $e) {
            return new BindResult(false, $e);
        }

        $result = trim($result);

        if (preg_match("/^u:\S+/", trim($result))) {
            return new BindResult();
        }

        return new BindResult(true, null);
    }


    private function authenticateOverLdap(string $user, string $password): BindResult {

        try {
            $this->ldap->bind($user, $password);
        } catch (BindException $e) {
            return new BindResult(false, $e);
        }
        return new BindResult(true);
    }
}