<?php
/**
 * Aqui ficam as queries que interagem com o Apolo. Assim,
 * toda busca de dados na base do Apolo devera ser uma funcao
 * estatica da classe Query.
 */

namespace block_extensao\Service;

use stdClass;

require_once('USPDatabase.php');

class Query 
{
  /**
   * Captura as turmas abertas.
   * Sao consideradas como turmas abertas somente as turmas com
   * data de encerramento posterior a data de hoje.
   * 
   * @return array
   */
  public static function turmasAbertas () {
    $hoje = date("Y-m-d");
    $query = "
      SELECT
        o.codofeatvceu
        ,c.nomcurceu
        ,o.dtainiofeatv
        ,o.dtafimofeatv
      FROM OFERECIMENTOATIVIDADECEU o
          LEFT JOIN CURSOCEU c
            ON c.codcurceu = o.codcurceu
          LEFT JOIN EDICAOCURSOOFECEU e
            ON o.codcurceu = e.codcurceu AND o.codedicurceu = e.codedicurceu
      WHERE e.dtainiofeedi >= '$hoje'
      ORDER BY codofeatvceu 
    ";

    return USPDatabase::fetchAll($query);
  }

  /**
   * Captura os ministrantes das turmas abertas.
   * Sao consideradas como turmas abertas somente as turmas com
   * data de encerramento posterior a data de hoje.
   * 
   * @return array
   */
  public static function ministrantesTurmasAbertas () {
    $hoje = date("Y-m-d");
    $query = "
      SELECT
        m.codofeatvceu
        ,m.codpes
        ,m.codatc
      FROM dbo.MINISTRANTECEU m
      WHERE codpes IS NOT NULL
        AND m.dtainimisatv >= '$hoje'
      ORDER BY codofeatvceu
    ";

    return USPDatabase::fetchAll($query);
  }

  /**
   * A partir do codofeatvceu, captura as informacoes de uma
   * turma, como a data de inicio e tal.
   * 
   * [ a query sera posta aqui posteriormente ]
   * 
   * @param string|integer $codofeatvceu Codigo de oferecimento da atividade.
   * 
   * @return object
   */
  public static function informacoesTurma ($codofeatvceu) {
    $info_curso = new stdClass;
    $info_curso->codofeatvceu = $codofeatvceu;
    $info_curso->startdate = strtotime("now");
    $info_curso->enddate = strtotime("+1 year");
    return $info_curso;
  }
  
  /**
   * Obtem o objetivo do curso informado.
   * 
   * @param string|integer $codofeatvceu Codigo de oferecimento da atividade.
   * 
   * @return array
   */
   // Obtem o objetivo do curso explicitado 
  public static function objetivo_extensao($codofeatvceu) {
    $obj = "
    SELECT c.objcur FROM OFERECIMENTOATIVIDADECEU o LEFT JOIN CURSOCEU c ON c.codcurceu = o.codcurceu 
    WHERE codofeatvceu = $codofeatvceu";
    return USPDatabase::fetch($obj);
  }

  /**
   * Captura os alunos matriculados nas turmas abertas.
   * Sao consideradas como tumras abertas somente as turmas com
   * data de encerramento posterior a data de hoje.
   * 
   * @param string|integer $codofeatvceu Codigo de oferecimento da atividade.
   * 
   * @return array
   */
  public static function alunosMatriculados ($codofeatvceu) {
    $hoje = date("Y-m-d");
    $query = "
      SELECT 
        ma.codofeatvceu,
        mc.codpes,
        p.nompes,
        p.numcpf
      FROM dbo.MATRICULAATIVIDADECEU ma
      INNER JOIN	
        dbo.MATRICULACURSOCEU mc
        ON mc.codmtrcurceu = ma.codmtrcurceu
      INNER JOIN
        dbo.PESSOA p 
        ON mc.codpes = p.codpes
      WHERE ma.codofeatvceu = $codofeatvceu
    ";
    return USPDatabase::fetchAll($query);
  }
}