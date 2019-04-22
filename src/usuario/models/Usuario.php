<?php

namespace siap\usuario\models;
use siap\setor\models\Setor;
use siap\models\DBSiap;

class Usuario {

    private $login;
    private $nome;
    private $senha;
    private $nivel_acesso;
    private $telefone;
    private $setor;
    private static $setorNome = null;
    private $ativo;
    
    public function getSetorNome() {
      if (!$this->setorNome){
        $this->setorNome = Setor::getById($this->getSetor());
      }
      return $this->setorNome->getNome();
    }

    function getAtivo() {
        return $this->ativo;
    }

    function setAtivo($ativo) {
        $this->ativo = $ativo;
    }

    function getTelefone() {
        return $this->telefone;
    }

    function getSetor() {
        return $this->setor;
    }

    function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    function setSetor($setor) {
        $this->setor = $setor;
    }

    public function is_authenticated() {
        return true;
    }

    /**
     * @return mixed
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login) {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getNome() {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome) {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getSenha() {
        return $this->senha;
    }

    /**
     * @param mixed $senha
     */
    public function setSenha($senha) {
        $this->senha = $senha;
    }

    /**
     * @param $senha string
     * @return bool
     */
    public function checkSenha($senha) {
        return $this->senha == md5($senha);
    }

    public function getNivelAcesso() {
        return $this->nivel_acesso;
    }

    public function setNivelAcesso($nacesso) {
        $this->nivel_acesso = $nacesso;
    }

    static function createOrUpdate($login, $nome, $senha, $telefone, $setor, $privilegio, $ativo) {
        $user = self::getByLogin($login);
        #Se tiver usuário cadastrado então é uma atualização se não é um novo usuário
        if ($user) {
            #Verifica se o usuário ta atualizando a senha.
            $senha = ($senha == null or $senha == '') ? $user->getSenha() : $senha;
            $sql = 'UPDATE siap.usuario SET nome = ?, senha = ?, telefone = ?, setor_id = ?, nivel_acesso_id = ?, ativo = ? WHERE login = ?';
        } else {
            $sql = 'INSERT INTO siap.usuario (nome, senha, telefone, setor_id, nivel_acesso_id, ativo, login) VALUES (?, ?, ?, ?, ?, ?, ?)';
        }
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($nome, $senha, $telefone, $setor, $privilegio, $ativo, $login));
    }

    /**
     * @param $row array
     * @return Informação da operação
     */
    static function updatePessoa($login, $nome, $senha, $telefone, $setor) {
        $sql = 'UPDATE siap.usuario SET nome = ?, senha = ?, telefone = ?, setor_id = ? WHERE login = ?';

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($nome, $senha, $telefone, $setor, $login));
        return $stmt->errorInfo();
    }

    /**
     * @param $row array
     * @return Usuario
     */
    private static function bundle($row) {
        $u = new Usuario();
        $u->setLogin($row['login']);
        $u->setNome($row['nome']);
        $u->setSenha($row['senha']);
        $u->setTelefone($row['telefone']);
        $u->setSetor($row['setor_id']);
        $u->setNivelAcesso($row['nivel_acesso_id']);
        $u->setAtivo($row['ativo']);
        
        return $u;
    }

    /**
     * @param $login string
     * @return Usuario|null
     */
    public static function getAtivoByLogin($login) {
        $sql = "select * from siap.usuario where login = ? and ativo = 'S'";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($login));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    public static function getByLogin($login) {
        $sql = "select * from siap.usuario where login = ?";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($login));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    /**
     * @return Usuario|null
     */
    public static function getAllAtivos() {
        $sql = "select * from siap.usuario where ativo = 'S'";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    public static function getAll() {
        $sql = "select * from siap.usuario";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

}
