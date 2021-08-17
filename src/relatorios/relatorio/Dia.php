<?php

namespace siap\relatorios\relatorio;

use siap\relatorios\models\relatorioDIA;

include_once '../../../public/uteis/funcoes.php';

class Dia {

    function geraXls() {
        $relatorio_dia = new relatorioDIA();

        $grupos = $relatorio_dia->getGrupos();
        $table = array(['Tipo', 'Media de Valor', 'Quantidade', 'Ordem Setor', 'Setor', 'Abreviação', 'Bloco', 'Mês', 'Mês Abreviado', 'Ordem do Mês', 'Ano']);

        foreach ($grupos as $grupo) {
            $ano = 2019;
            while ($ano <= date('Y')) {
                $mes = 1;
                while ($mes <= 12) {
                    $result = relatorioDIA::getAll($grupo->getGrupo_codigo(), $grupo->getSetor_ordem(), $mes, $ano);
                    $media = $result[1] == null ? 0 : $result[1];
                    $quantidade = $result[0] == null ? 0 : $result[0];
                    $type = preg_replace("/material (de )?/i", "", $grupo->getGrupo());

                    array_push($table, [upperInitial($type), $media, $quantidade, upperInitial($grupo->getSetor_ordem()), upperInitial($grupo->getSetor_nome()), $grupo->getSetor_sigla(), $grupo->getBloco(), mesCompleto($mes), mesAbreviado($mes), upperInitial($mes), upperInitial($ano)]);

                    $mes += 1;
                }
                $ano += 1;
            }
        }
        return $table;
    }

}
