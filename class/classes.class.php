<?php

class Security {

    private static $_instance;

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        session_start();
    }

    public function isLogged() {
        if (isset($_SESSION['usuarioLogado'])) {
            return true;
        }
        return false;
    }

    public function setLogged($usuarioTemp) {
        $_SESSION['usuarioLogado'] = $usuarioTemp;
    }

    public function setUnlogged() {
        $_SESSION['usuarioLogado'] = "";
        unset($_SESSION['usuarioLogado']);
        session_destroy();
    }

    public function getUsuario() {
        return $_SESSION['usuarioLogado'];
    }

}

class Usuario {

    public $contaID;
    public $primeiroNome;
    public $ultimoNome;
    public $email;

    public function __construct($conta, $pNome, $uNome, $email) {        
        $this->contaID=$conta;   
        $this->primeiroNome=$pNome;   
        $this->ultimoNome=$uNome;   
        $this->email=$email;
        return $this;
    }

}

?>
