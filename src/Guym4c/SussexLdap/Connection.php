<?php

namespace Guym4c\SussexLdap;

use Exception;
use RuntimeException;
use Ssh\{Authentication, Configuration, Session};
use Toyota\Component\Ldap\{Core\Manager, Platform\Native\Driver};

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

    /** @var string */
    private $userDomain = self::LDAP_DOMAIN;

    private function __construct(bool $proxied, string $userDomain = self::LDAP_DOMAIN) {
        $this->proxied = $proxied;
        $this->userDomain = $userDomain;
    }

    /**
     * Proxied Connection constructor.
     *
     * @param string $proxyUsername
     * @param string|null $proxyPassword
     * @param string $proxyDomain
     * @param string $userDomain
     * @return Connection
     */
    public static function overProxy(string $proxyUsername, ?string $proxyPassword = null, ?string $proxyDomain = self::SSH_PROXY_DOMAIN, string $userDomain = self::LDAP_DOMAIN): self {

        $connection = new Connection(true, $userDomain);

        if (empty($proxyPassword)) {
            $auth = new Authentication\Agent($proxyUsername);
        } else {
            $auth = new Authentication\Password($proxyUsername, $proxyPassword);
        }

        $connection->ssh = new Session(new Configuration($proxyDomain), $auth);
        return $connection;
    }

    /**
     * Unproxied Connection constructor.
     *
     * @param string $host
     * @param string $baseDn
     * @param array $options
     * @return Connection
     */
    public static function withoutProxy(string $host = self::LDAP_DOMAIN, string $baseDn = self::LDAP_BASE_DN, array $options = []): self {

        $connection = new Connection(false, $host);
        $connection->ldap = new Manager([
            'hostname' => $host,
            'base_dn' => $baseDn,
            'options' => array_merge([
                LDAP_OPT_PROTOCOL_VERSION => self::LDAP_PROTOCOL_VERSION,
                LDAP_OPT_REFERRALS => self::LDAP_NUM_REFERRALS,
            ], $options),
        ], new Driver());
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
            $this->ssh->getExec()->run(sprintf('ldapwhoami -h %s -D %s -xw %s', self::LDAP_DOMAIN, $user, $password));
        } catch (RuntimeException $e) {
            return new BindResult(false, $e);
        }

        return new BindResult();
    }


    private function authenticateOverLdap(string $user, string $password): BindResult {

        try {
            $this->ldap->bind($user, $password);
        } catch (Exception $e) {
            return new BindResult(false, $e);
        }
        return new BindResult();
    }

    /**
     * @return bool
     */
    public function isProxied(): bool {
        return $this->proxied;
    }

    /**
     * @return string
     */
    public function getUserDomain(): string {
        return $this->userDomain;
    }

    /**
     * @param string $userDomain
     */
    public function setUserDomain(string $userDomain): void {
        $this->userDomain = $userDomain;
    }
}