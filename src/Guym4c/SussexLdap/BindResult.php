<?php

namespace Guym4c\SussexLdap;

use Exception;
use Toyota\Component\Ldap\Exception\LdapException;

class BindResult {

    /** @var bool */
    private $bound;

    /** @var Exception */
    private $error;

    /**
     * BindResult constructor.
     * @param bool $bound
     * @param Exception|null $error
     */
    public function __construct(bool $bound = true, ?Exception $error = null) {
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
     * @return Exception
     */
    public function getError(): Exception {
        return $this->error;
    }
}