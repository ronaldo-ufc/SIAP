<?php

namespace siap\produto\models;

use siap\models\DBSiap;

class TemplateProduto {

    private $template_id;
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
    private $categoria_id;
    private $categoria;
    private $fabricante;
    private $modelo;
    private $setor;

    private function bundle($row) {
        $u = new TemplateProduto();
        $u->setTemplate_id($row['template_id']);
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
        $u->setCategoria_id($row['categoria_id']);
        $u->setCategoria(\siap\cadastro\models\Categoria::getById($row['categoria_id']));
        $u->setFabricante(\siap\cadastro\models\Fabricante::getById($row['fabricante_id']));
        $u->setModelo(\siap\cadastro\models\Modelo::getById($row['modelo_id']));
        $u->setSetor(\siap\setor\models\Setor::getById($row['setor_id']));
        return $u;
    }

    static function getAll() {
        $sql = "SELECT * FROM siap.template_produto ORDER BY template_id desc";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getById($template_id) {
        $sql = "SELECT * FROM siap.template_produto where template_id = ?";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($template_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return false;
        }
        return self::bundle($row);
    }

    static function create($nome, $data_atesto, $nota_fiscal, $fornecedor, $descricao, $observacao, $foto, $fabricante_id, $modelo_id, $aquisicao_id, $status_id, $setor_id, $conservacao_id, $categoria_id) {
        $sql = "INSERT INTO siap.template_produto (nome ,  "
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
                . "categoria_id) "
                . " VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome)),
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
            $categoria_id
        ));
        return $stmt->errorInfo();
    }
    
    
    static function update($template_id, $nome, $data_atesto, $nota_fiscal, $fornecedor, $descricao, $observacao, $foto, $fabricante_id, $modelo_id, $aquisicao_id, $status_id, $conservacao_id, $categoria_id, $setor_id) {

        $sql = "UPDATE siap.template_produto SET "
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
                . "setor_id = ?, "
                . "categoria_id  = ? "
                . " WHERE template_id = ? ";
        
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
            $setor_id,
            $categoria_id,
            $template_id
        ));
        return $stmt->errorInfo();
    }
    
    

    static function delete($template_id) {
        $sql = 'DELETE FROM siap.template_produto WHERE template_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($template_id));
        return $stmt->errorInfo();
    }

    function getTemplate_id() {
        return $this->template_id;
    }

    function getNome() {
        return $this->nome;
    }

    function getData_atesto() {
        return $this->data_atesto;
    }

    function getNota_fiscal() {
        return $this->nota_fiscal;
    }

    function getFornecedor() {
        return $this->fornecedor;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getObservacao() {
        return $this->observacao;
    }

    function getFabricante_id() {
        return $this->fabricante_id;
    }

    function getModelo_id() {
        return $this->modelo_id;
    }

    function getAquisicao_id() {
        return $this->aquisicao_id;
    }

    function getStatus_id() {
        return $this->status_id;
    }

    function getSetor_id() {
        return $this->setor_id;
    }

    function getConservacao_id() {
        return $this->conservacao_id;
    }

    function setTemplate_id($template_id) {
        $this->template_id = $template_id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setData_atesto($data_atesto) {
        $this->data_atesto = $data_atesto;
    }

    function setNota_fiscal($nota_fiscal) {
        $this->nota_fiscal = $nota_fiscal;
    }

    function setFornecedor($fornecedor) {
        $this->fornecedor = $fornecedor;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setObservacao($observacao) {
        $this->observacao = $observacao;
    }

    function setFabricante_id($fabricante_id) {
        $this->fabricante_id = $fabricante_id;
    }

    function setModelo_id($modelo_id) {
        $this->modelo_id = $modelo_id;
    }

    function setAquisicao_id($aquisicao_id) {
        $this->aquisicao_id = $aquisicao_id;
    }

    function setStatus_id($status_id) {
        $this->status_id = $status_id;
    }

    function setSetor_id($setor_id) {
        $this->setor_id = $setor_id;
    }

    function setConservacao_id($conservacao_id) {
        $this->conservacao_id = $conservacao_id;
    }

    function getFoto() {
        return $this->foto;
    }

    function setFoto($foto) {
        $this->foto = $foto;
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
    function getSetor() {
        return $this->setor;
    }

    function setSetor($setor) {
        $this->setor = $setor;
    }



}
