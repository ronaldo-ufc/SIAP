<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace siap\produto\models;

use siap\models\DBSiap;

/**
 * Description of Ativos
 *
 * @author Ronaldo
 */
//A classe foi modificada colocando mais um atributo, o ID do responsável pelo bem.
//Como pelo menos por hora o responsável será apenas a Maria Elias, não mudei nenhuma outra função para levar em conta quem é o responsável.
//A tabela responsável foi modelada apenas com uma chave serial e o nome do Responsável.

class Ativos {

    private $patrimonio;
    private $nome;
    private $data_atesto;
    private $nota_fiscal;
    private $fornecedor;
    private $descricao;
    private $observacao;
    private $foto;
    private $fabricante_id;
    private $modelo_id;
    private $aquisicao_id;
    private $status_id;
    private $setor_id;
    private $conservacao_id;
    private $template_id;
    private $usuario_id;
    private $categoria_id;
    //private $responsavel_id;
    private $fabricante;
    private $modelo;
    private $aquisicao;
    private $status;
    private $setor;
    private $conservacao;
    private $template;
    private $usuario;
    private $categoria;
    private $empenho;
    //private $responsavel;

    private function bundle($row) {
        $u = new Ativos();
        $u->setPatrimonio($row['patrimonio']);
        $u->setNome($row['nome']);
        $u->setData_atesto($row['data_atesto']);
        $u->setNota_fiscal($row['nota_fiscal']);
        $u->setFornecedor($row['fornecedor']);
        $u->setDescricao($row['descricao']);
        $u->setObservacao($row['observacao']);
        $u->setFoto($row['foto']);
        $u->setFabricante_id($row['fabricante_id']);
        $u->setModelo_id($row['modelo_id']);
        $u->setAquisicao_id($row['aquisicao_id']);
        $u->setStatus_id($row['status_id']);
        $u->setSetor_id($row['setor_id']);
        $u->setConservacao_id($row['conservacao_id']);
        $u->setTemplate_id($row['template_id']);
        $u->setUsuario_id($row['usuario_id']);
        $u->setCategoria_id($row['categoria_id']);
        $u->setEmpenho($row['empenho']);
        //$u->setResponsavel_id(1);

        #Montando os objetos
        $u->setFabricante(\siap\cadastro\models\Fabricante::getById($row['fabricante_id']));
        $u->setModelo(\siap\cadastro\models\Modelo::getById($row['modelo_id']));
        $u->setSetor(\siap\setor\models\Setor::getById($row['setor_id']));
        $u->setStatus(\siap\cadastro\models\Status::getById($row['status_id']));
        $u->setAquisicao(\siap\cadastro\models\Aquisicao::getById($row['aquisicao_id']));
        $u->setConservacao(\siap\cadastro\models\EConservacao::getById($row['conservacao_id']));
        $u->setCategoria(\siap\cadastro\models\Categoria::getById($row['categoria_id']));
        //$u->setResponsavel(\siap\produto\models\Responsavel::getById(1));

        return $u;
    }

    static function create($patrimonio, $nome, $data_atesto, $nota_fiscal, $fornecedor, $descricao, $observacao, $foto, $fabricante_id, $modelo_id, $aquisicao_id, $status_id, $setor_id, $conservacao_id, $template_id, $usuario, $categoria_id,$empenho) {
        #Verifica se tem um ativo já foi deletado
        if (self::temAtivoDeletado($patrimonio)) {
            return array('Erro', 'info', 'Existe um ativo excluído com esse número de patrimônio. Recupere o ativo no menu de recupeção de ativo!');
        }
        #Verifica se tem responsavel pelo setor
        if (!self::verificaResponsabelPeloSetor($setor_id, $data_atesto)) {
            return array('Erro', 'info', 'No período da data de atesto não existe um Agente Setorial responsável pelo setor. Cadastre primeiro o Agente Setorial para o setor');
        }
        $sql = "INSERT INTO siap.ativo (patrimonio, "
                . "nome ,  "
                . "data_atesto,  "
                . "nota_fiscal ,  "
                . "fornecedor , "
                . "descricao ,  "
                . "observacao ,  "
                . "foto ,  "
                . "fabricante_id , "
                . "modelo_id ,  "
                . "aquisicao_id ,  "
                . "status_id ,  "
                . "setor_id ,  "
                . "conservacao_id, "
                . "template_id, "
                . "usuario_id, "
                . "categoria_id,"
                . "empenho)"
                . " VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($patrimonio,
            strtoupper(tirarAcentos($nome)),
            $data_atesto,
            strtoupper(tirarAcentos($nota_fiscal)),
            strtoupper(tirarAcentos($fornecedor)),
            strtoupper(tirarAcentos($descricao)),
            strtoupper(tirarAcentos($observacao)),
            $foto,
            $fabricante_id,
            $modelo_id,
            $aquisicao_id,
            $status_id,
            $setor_id,
            $conservacao_id,
            $template_id,
            $usuario,
            $categoria_id,
            strtoupper(tirarAcentos($empenho))
        ));
        return $stmt->errorInfo();
    }

    static function update($patrimonio, $nome, $data_atesto, $nota_fiscal, $fornecedor, $descricao, $observacao, $foto, $fabricante_id, $modelo_id, $aquisicao_id, $status_id, $conservacao_id, $usuario, $categoria_id,$empenho) {
        #Verifica se o tombamento está cadastrado.
        if (self::getById($patrimonio) == NULL) {
            return array('Erro', 'info', 'Não existe um ativo cadastrado com esse número de patrimônio.');
        }
        
        $sql = "UPDATE siap.ativo SET "
                . "nome = ?,  "
                . "data_atesto = ?,  "
                . "nota_fiscal  = ?,  "
                . "fornecedor = ? , "
                . "descricao  = ?,  "
                . "observacao = ? ,  "
                . "foto = ? ,  "
                . "fabricante_id  = ?, "
                . "modelo_id  = ?,  "
                . "aquisicao_id  = ?,  "
                . "status_id  = ?,  "
                . "conservacao_id = ?, "
                . "usuario_id  = ?, "
                . "categoria_id  = ?, "
                . "empenho  = ? "
                . " WHERE patrimonio = ? ";
        
//        $sql = Ativos::injectionCaracteres($sql);
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(
            strtoupper(tirarAcentos($nome)),
            $data_atesto,
            strtoupper(tirarAcentos($nota_fiscal)),
            strtoupper(tirarAcentos($fornecedor)),
            strtoupper(tirarAcentos($descricao)),
            strtoupper(tirarAcentos($observacao)),
            $foto,
            $fabricante_id,
            $modelo_id,
            (int) $aquisicao_id,
            $status_id,
            $conservacao_id,
            $usuario,
            $categoria_id,
            strtoupper(tirarAcentos($empenho)),
            $patrimonio
        ));
        return $stmt->errorInfo();
    }

    static function delete($patrimonio, $usuario) {
        $sql = "DELETE FROM siap.ativo WHERE patrimonio = ?";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($patrimonio));
        $msg = $stmt->errorInfo();
        if ($msg[2]) {
            return $msg;
        }
        self::logAtivo($patrimonio, $usuario);
    }

    private function logAtivo($patrimonio, $usuario) {
        $sql = "UPDATE siap.ativo_del SET usuario_del = ? WHERE patrimonio = ?";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($usuario, $patrimonio));
        return $stmt->errorInfo();
    }

    static function verificaResponsabelPeloSetor($setor_id, $data) {
        $responsavel = \siap\setor\models\SetorResponsavel::getResponsavelBySetorAndData($setor_id, $data);
        if ($responsavel) {
            return true;
        }
        return false;
    }

    private function temAtivoDeletado($patrimonio) {
        $sql = "SELECT * FROM siap.ativo_del WHERE patrimonio = ?";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($patrimonio));
        $row = $stmt->fetch();
        if ($row == null) {
            return false;
        }
        return true;
    }

    static function getQtdById($template_id) {
        $sql = "SELECT count(*) as quantidade FROM siap.ativo where template_id = ?";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($template_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return 0;
        }
        return $row['quantidade'];
    }

    static function getById($patrimonio_id) {
      $sql = "SELECT * FROM siap.ativo where patrimonio = ?";

      $stmt = DBSiap::getSiap()->prepare($sql);
      $stmt->execute(array($patrimonio_id));
      $row = $stmt->fetch();
      if ($row == null) {
          return FALSE;
      }
      return self::bundle($row);
    }
    static function Filtrar($nome, $patrimonio, $categoria,$modelo,$atesto,$status,$conservacao,$setor,$fornecedor,$nota_fiscal,$empenho,$descricao,$cpf_usuario){
        $sql = "SELECT * from siap.ativo where true ";
        if($nome){
            $nome = strtoupper(tirarAcentos($nome));
            $sql = $sql."AND ativo.nome LIKE '%$nome%' ";
        }
        if($patrimonio){
            $sql = $sql."AND ativo.patrimonio LIKE '%$patrimonio%' ";
        }
        
        if($modelo != "n"){
            $sql = $sql."AND ativo.modelo_id = '$modelo' ";
        }
        if($categoria != "n"){
            $sql = $sql."AND ativo.categoria_id = '$categoria' ";
        }
        if($atesto){
            $sql = $sql."AND ativo.data_atesto = '$atesto' ";
        }
        if($status != "n"){
            $sql = $sql."AND ativo.status_id = '$status' ";
        }
        if($conservacao != "n"){
            $sql = $sql."AND ativo.conservacao_id = '$conservacao' ";
        }
        if($fornecedor){
            $fornecedor = strtoupper(tirarAcentos($fornecedor));
            $sql = $sql."AND ativo.fornecedor LIKE '%$fornecedor%' ";
        }
        if($nota_fiscal){
            $sql = $sql."AND ativo.nota_fiscal = '$nota_fiscal' ";
        }
        if($empenho){
            $empenho = strtoupper(tirarAcentos($empenho));
            $sql = $sql."AND ativo.empenho = '$empenho' ";
        }
        if($descricao){
            $descricao = strtoupper(tirarAcentos($descricao));
            $sql = $sql."AND ativo.descricao LIKE '%$descricao%' ";
        }
        if($setor != "n"){
            $sql = $sql."AND ativo.setor_id = '$setor' ";
        }
        
        if ($cpf_usuario) {
          $sql = $sql." AND setor_id = (select setor_id 
				  from setor_responsavel 
				  where responsavel_id = '$cpf_usuario' and current_date between data_inicio and data_fim)";
        }
        
        $sql = $sql." ORDER BY data_atesto desc limit 150";
       
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        if (sizeof($result) == 0) {
            return false;
        } else {
            return $result;
        }
    }
    
//    static function injectionCaracteres($str){
//        return preg_replace("/[]/ui", NULL, $str);
//    }

    static function getAll() {
        $sql = "select * from siap.ativo order by cadastro desc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
    static function getAllEmpenho() {
        $sql = "select DISTINCT empenho from siap.ativo where empenho != ''";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            foreach ($row as $ro) {
                array_push($result, $ro);
            }
        }
        //TIRANDO OS VALORES DUPLICADOS
        $resultado = array_unique($result, SORT_REGULAR);
        return $resultado;
    }
    
    static function getAllBySetor($setor_id) {
        $sql = "select * from siap.ativo where setor_id = ? order by cadastro desc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor_id));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    

    public function getPatrimonio() {
        return $this->patrimonio;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getData_atesto() {
        return $this->data_atesto;
    }

    public function getNota_fiscal() {
        return $this->nota_fiscal;
    }

    public function getFornecedor() {
        return $this->fornecedor;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getObservacao() {
        return $this->observacao;
    }

    public function getFoto() {
        return $this->foto;
    }

    public function getFabricante_id() {
        return $this->fabricante_id;
    }

    public function getModelo_id() {
        return $this->modelo_id;
    }

    public function getAquisicao_id() {
        return $this->aquisicao_id;
    }

    public function getStatus_id() {
        return $this->status_id;
    }

    public function getSetor_id() {
        return $this->setor_id;
    }

    public function getConservacao_id() {
        return $this->conservacao_id;
    }

    public function getTemplate_id() {
        return $this->template_id;
    }

    public function setPatrimonio($patrimonio) {
        $this->patrimonio = $patrimonio;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setData_atesto($data_atesto) {
        $this->data_atesto = $data_atesto;
    }

    public function setNota_fiscal($nota_fiscal) {
        $this->nota_fiscal = $nota_fiscal;
    }

    public function setFornecedor($fornecedor) {
        $this->fornecedor = $fornecedor;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function setObservacao($observacao) {
        $this->observacao = $observacao;
    }

    public function setFoto($foto) {
        $this->foto = $foto;
    }

    public function setFabricante_id($fabricante_id) {
        $this->fabricante_id = $fabricante_id;
    }

    public function setModelo_id($modelo_id) {
        $this->modelo_id = $modelo_id;
    }

    public function setAquisicao_id($aquisicao_id) {
        $this->aquisicao_id = $aquisicao_id;
    }

    public function setStatus_id($status_id) {
        $this->status_id = $status_id;
    }

    public function setSetor_id($setor_id) {
        $this->setor_id = $setor_id;
    }

    public function setConservacao_id($conservacao_id) {
        $this->conservacao_id = $conservacao_id;
    }

    public function setTemplate_id($template_id) {
        $this->template_id = $template_id;
    }

    public function getUsuario_id() {
        return $this->usuario_id;
    }

    public function setUsuario_id($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    public function getFabricante() {
        return $this->fabricante;
    }

    public function getModelo() {
        return $this->modelo;
    }

    public function setFabricante($fabricante) {
        $this->fabricante = $fabricante;
    }

    public function setModelo($modelo) {
        $this->modelo = $modelo;
    }

    public function getAquisicao() {
        return $this->aquisicao;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getSetor() {
        return $this->setor;
    }

    public function getConservacao() {
        return $this->conservacao;
    }

    public function setAquisicao($aquisicao) {
        $this->aquisicao = $aquisicao;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setSetor($setor) {
        $this->setor = $setor;
    }

    public function setConservacao($conservacao) {
        $this->conservacao = $conservacao;
    }

    public function getCategoria_id() {
        return $this->categoria_id;
    }

    public function setCategoria_id($categoria_id) {
        $this->categoria_id = $categoria_id;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

    public function getResponsavel() {
        return $this->responsavel;
    }

    public function setResponsavel($responsavel) {
        $this->responsavel = $responsavel;
    }

    public function getResponsavel_id() {
        return $this->responsavel_id;
    }

    public function setResponsavel_id($responsavel_id) {
        $this->responsavel_id = $responsavel_id;
    }
    function getEmpenho() {
        return $this->empenho;
    }

    function setEmpenho($empenho) {
        $this->empenho = $empenho;
    }

}
