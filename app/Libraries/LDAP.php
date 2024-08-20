<?php

namespace App\Libraries;

use Exception;

class LDAP
{
    private string $server = "grupoberlin.com";

    private string $domain = "grupoberlin.com";

    protected string $user;

    /**
     * @var false|resource
     */
    private $ldap_connection;

    private function initConnection(): void
    {
        $this->ldap_connection = ldap_connect($this->server);
        ldap_set_option($this->ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldap_connection, LDAP_OPT_REFERRALS, 0);
    }

    /**
     * Permite la autenticación de un usuario mediante el servidir LDAP
     * @param string $username Nombre de usuario
     * @param string $password Contraseña del usuario
     * @throws Exception
     */
    public function ldapAuth(string $username, string $password):bool
    {
        $this->initConnection();

        //Verify connection to LDAP server
        if(!$this->ldap_connection){
            throw new Exception("Error al conectar con el servidor LDAP");
        }

        # Return boolean of the authenticate state
        return ldap_bind($this->ldap_connection, $username . "@" . $this->domain, $password);
    }
}