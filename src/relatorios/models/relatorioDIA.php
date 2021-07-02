<?php

namespace siap\relatorios\models;

use siap\models\DBSiap;

class relatorioDIA {
    private $grupo_codigo;
    private $grupo;
    private $media_material;
    private $quantidade;
    private $setor_ordem;
    private $setor_nome;
    private $setor_sigla;
    private $bloco;
    private $numero_mes;
    private $ano;

    private function bundle($row) {
        $u = new relatorioDIA();
        $u->setGrupo_codigo($row['grupo_codigo']);
        $u->setGrupo($row['grupo']);
        $u->setMedia_material($row['media_material']);
        $u->setQuantidade($row['quantidade']);
        $u->setSetor_ordem($row['setor_ordem']);
        $u->setSetor_nome($row['setor_nome']);
        $u->setSetor_sigla($row['setor_sigla']);
        $u->setBloco($row['bloco']);
        $u->setNumero_mes($row['numero_mes']);
        $u->setAno($row['ano']);

        return $u;
    }
    
    public function getGrupos(){
        $sql = "select 
            g.grupo_codigo,
            g.nome as grupo, 
            s.setor_id as setor_ordem,
            s.nome as setor_nome, 
            s.sigla as setor_sigla,
            b2.nome as bloco
            from siap.produto p
            inner join siap.balanco b on p.produto_codigo = b.produto_codigo
            inner join public.setor s on b.setor_id = s.setor_id
            inner join public.bloco b2 on b2.bloco_id = s.bloco_id 
            inner join public.grupo g on p.grupo_codigo = g.grupo_codigo
            where b.tipo = 'E' and b.requisicao_codigo is not null
            group by g.grupo_codigo, g.nome, s.setor_id, s.nome, s.sigla, b2.nome

            order by g.nome asc, s.nome asc";
        
            $stmt = DBSiap::getSiap()->prepare($sql);
            $stmt->execute(array());
            $rows = $stmt->fetchAll();
            //return $stmt->errorInfo();
            $result = array();
            foreach ($rows as $row) {
                array_push($result, self::bundle($row));
            }
            return $result;
    }
    
    static function getAll($grupo, $setor, $mes, $ano) {
        $sql = "select
        sum(b.quantidade) as quantidade,
	round(avg(b.vlr_uni), 2) as media
	
        from siap.produto p
        inner join siap.balanco b on p.produto_codigo = b.produto_codigo
        inner join public.setor s on b.setor_id = s.setor_id
        inner join public.bloco b2 on b2.bloco_id = s.bloco_id 
        inner join public.grupo g on p.grupo_codigo = g.grupo_codigo
        where g.grupo_codigo = ? and 
		s.setor_id = ? and 
		extract(month from data) = ? and
		extract(year from data) = ? and 
		b.tipo = 'E' and 
		b.requisicao_codigo is not null";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($grupo, $setor, $mes, $ano));
        $row = $stmt->fetch();
        if ($row == null){
          return false;
        }
        return array($row['quantidade'], $row['media']);
    }

//    public function getAll() {
//        $sql = "select 
//	g.nome as grupo, 
//	round(avg(b.vlr_uni), 2) as media_material,
//	sum(b.quantidade) as quantidade,
//	s.setor_id as setor_ordem,
//	s.nome as setor_nome, 
//	s.sigla as setor_sigla,
//	b2.nome as bloco,
//	extract(month from data) as numero_mes, 
//	extract(year from data) as ano
//	 
//        from siap.produto p
//        inner join siap.balanco b on p.produto_codigo = b.produto_codigo
//        inner join public.setor s on b.setor_id = s.setor_id
//        inner join public.bloco b2 on b2.bloco_id = s.bloco_id 
//        inner join public.grupo g on p.grupo_codigo = g.grupo_codigo
//        where b.tipo = 'E' and b.requisicao_codigo is not null
//        group by g.nome, s.setor_id, s.nome, s.sigla, b2.nome, extract(month from data), extract(year from data)
//
//        order by g.nome asc, s.nome asc, ano asc, numero_mes asc";
//
//        $stmt = DBSiap::getSiap()->prepare($sql);
//        $stmt->execute(array());
//        $rows = $stmt->fetchAll();
//        //return $stmt->errorInfo();
//        $result = array();
//        foreach ($rows as $row) {
//            array_push($result, self::bundle($row));
//        }
//        return $result;
//    }

    public function getGrupo() {
        return $this->grupo;
    }

    public function getMedia_material() {
        return $this->media_material;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    public function getSetor_nome() {
        return $this->setor_nome;
    }

    public function getSetor_sigla() {
        return $this->setor_sigla;
    }

    public function getNumero_mes() {
        return $this->numero_mes;
    }

    public function getAno() {
        return $this->ano;
    }

    public function setGrupo($grupo) {
        $this->grupo = $grupo;
    }

    public function setMedia_material($media_material) {
        $this->media_material = $media_material;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }

    public function setSetor_nome($setor_nome) {
        $this->setor_nome = $setor_nome;
    }

    public function setSetor_sigla($setor_sigla) {
        $this->setor_sigla = $setor_sigla;
    }

    public function setNumero_mes($numero_mes) {
        $this->numero_mes = $numero_mes;
    }

    public function setAno($ano) {
        $this->ano = $ano;
    }

    public function getSetor_ordem() {
        return $this->setor_ordem;
    }

    public function setSetor_ordem($setor_ordem) {
        $this->setor_ordem = $setor_ordem;
    }

    public function getBloco() {
        return $this->bloco;
    }

    public function setBloco($bloco) {
        $this->bloco = $bloco;
    }
    public function getGrupo_codigo() {
        return $this->grupo_codigo;
    }

    public function setGrupo_codigo($grupo_codigo) {
        $this->grupo_codigo = $grupo_codigo;
    }


}
