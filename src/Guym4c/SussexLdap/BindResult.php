<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 16/03/2019
 * Time: 01:19
 */

namespace Guym4c\SussexLdap;


use Toyota\Component\Ldap\Exception\LdapException;

class BindResult {

    /** @var bool */
    private $bound;

    /** @var LdapException */
    private $error;

    /**
     * BindResult constructor.
     * @param bool $bound
     * @param \Exception $error
     */
    public function __construct(bool $bound, \Exception $error) {
        $this->bound = $bound;
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isBound(): bool {
        return $this->bound;
    }

    /**
     * @return \Exception
     */
    public function getError(): \Exception {
        return $this->error;
    }
}