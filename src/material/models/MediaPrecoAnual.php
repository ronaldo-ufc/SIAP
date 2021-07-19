<?php
namespace siap\material\models;
use siap\models\DBSiap;

class MediaPrecoAnual{
    private $produto_codigo;
    private $produto_nome;
    private $ano;
    private $media;
    
    public function __construct($produto_codigo) {
        $this->produto_codigo = $produto_codigo;
    }


    private function bundle($r){
        $u = new MediaPrecoAnual($r['produto_codigo']);
        $u->setProduto_nome($r['produto_nome']);
        $u->setAno($r['ano']);
        $u->setMedia($r['media']);
        
        return $u;
    }
    
    
    public function getAll() {
        $sql = "select p.produto_codigo as produto_codigo, 
                p.nome as produto_nome, 
                extract(year from b.data) as ano, 
                round(avg(b.vlr_uni), 2) as media from siap.balanco b
                inner join siap.produto p on p.produto_codigo = b.produto_codigo
                where p.produto_codigo = ?
                group by p.produto_codigo, p.nome, extract(year from data)
                order by extract(year from data)";
        
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($this->produto_codigo));
        $rows = $stmt->fetchAll();
        //return $stmt->errorInfo();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
    function updateMediaPreco($preco, $ano) {
        $sql = "update siap.balanco set vlr_uni = ? "
                . "where produto_codigo = ? and "
                . "extract(year from data) = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(moedaBanco($preco), $this->produto_codigo, $ano));
        return $stmt->errorInfo();
    }
    
    public function getProduto_codigo() {
        return $this->produto_codigo;
    }

    public function getProduto_nome() {
        return $this->produto_nome;
    }

    public function getAno() {
        return $this->ano;
    }

    public function getMedia() {
        return $this->media;
    }

    public function setProduto_codigo($produto_codigo) {
        $this->produto_codigo = $produto_codigo;
    }

    public function setProduto_nome($produto_nome) {
        $this->produto_nome = $produto_nome;
    }

    public function setAno($ano) {
        $this->ano = $ano;
    }

    public function setMedia($media) {
        $this->media = $media;
    }


}
