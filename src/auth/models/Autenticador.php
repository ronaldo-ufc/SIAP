<?php

namespace siap\auth\models;
use siap\usuario\models\Usuario;

abstract class Autenticador {
 
    private static $instancia = null;
 
    private function __construct() {}
 
    /**
     * 
     * @return Autenticador
     */
    public static function instanciar() {
 
        if (self::$instancia == NULL) {
            self::$instancia = new AutenticadorEmMemoria();
        }
 
        return self::$instancia;
 
    }
 
    public abstract function logar($login, $senha);
    public abstract function logado();
    public abstract function encerraSessao();
    public abstract function getUsuarioNome();
    public abstract function expulsar($app, $rota);
    public abstract function pegar_privilegio();
    public abstract function getUsuario();
    public abstract function getUsuarioRol();
    public abstract function confereSenhas($senha);
}


class AutenticadorEmMemoria extends Autenticador {
 
    public function logado() {
        $sess = new Sessao();
        return $sess->existe('nome');
    }
 
    public function logar($login, $senha) {
        $sess = new Sessao();
        $sess->instanciar();
        
        $usuario = Usuario::getAtivoByLogin($login);
        
        if (!$usuario) return false;
        
        $crypt_pass = md5($senha);
        if ($crypt_pass == $usuario->getSenha()) {
            $sess->set('nome', $usuario->getNome());
            $sess->set('privilegio', $usuario->getNivelAcesso());
            $sess->set('user', $login);
            $sess->set('pwd',$crypt_pass);
            return true;
        }
        else {
            return false;
        }
 
    }
    public function getUsuarioRol() {
        $sess = new Sessao();
 
        if ($this->logado()) {
            $usuario = $sess->get('privilegio');
            return $usuario;
        }
        else {
            return false;
        }
    }
    public function confereSenhas($senha) {
        $sess = new Sessao();
 
        if ($this->logado()) {
            return $sess->get('pwd') == md5($senha);
            
        }
        else {
            return false;
        }
    }
    public function getUsuarioNome() {
        $sess = new Sessao();
 
        if ($this->logado()) {
            $usuario = $sess->get('nome');
            return $usuario;
        }
        else {
            return false;
        }
    }
    
    function getUsuario() {
        $sess = new Sessao();
 
        if ($this->logado()) {
            $usuario = $sess->get('user');
            return $usuario;
        }
        else {
            return false;
        }
    }
    
    public function pegar_privilegio() {
      $sess = new Sessao();
 
        if ($this->logado()) {
            $usuario = $sess->get('privilegio');
            return $usuario;
        }
        else {
            return false;
        }
    }
 
    public function expulsar($app, $rota) 
    {
      
      $app->redirect($rota);
      
    }
    
    public function encerraSessao()
    {
      // Inicializa a sessão.
      // Se estiver sendo usado session_name("something"), não esqueça de usá-lo agora!
      //session_start();

      // Apaga todas as variáveis da sessão
      $_SESSION = array();

      // Se é preciso matar a sessão, então os cookies de sessão também devem ser apagados.
      // Nota: Isto destruirá a sessão, e não apenas os dados!
      if (ini_get("session.use_cookies")) {
          $params = session_get_cookie_params();
          setcookie(session_name(), '', time() - 403000,
              $params["path"], $params["domain"],
              $params["secure"], $params["httponly"]
          );
      }

      // Por último, destrói a sessão
      session_destroy();
    }
 
}
 
?>
    