<?php

/**
 * Turmas
 * 
 * A ideia desse arquivo eh mexer com turmas dentro da base do Moodle. Tambem nao sei
 * bem onde deixa-lo, entao por hora vai ficar aqui. Ele vai vasculhar na base de dados
 * do Moodle e trazer informacoes relevantes, a depender da funcao.
 */

// require_once('../../../config.php');

class Turmas {
  
  /**
   * Captura as turmas as quais um usuario eh ministrante.
   * 
   * @param string|integer $nusp_docente Numero USP do usuario.
   * 
   * @return array Cursos do usuario.
   */
  public static function docente_turmas ($nusp_docente) {
    global $DB;
    
    // captura as turmas relacionadas ao usuario
    $query = "SELECT codofeatvceu FROM {extensao_ministrante} WHERE codpes = $nusp_docente AND papel_usuario IN (1,2,5)";
    $usuario_turma = $DB->get_records_sql($query, ['codpes' => $nusp_docente]);

    $cursos_usuario = array();
    // captura os nomes dos cursos relacionados
    foreach ($usuario_turma as $turma) {
      $busca = $DB->get_record('extensao_turma', ['codofeatvceu' => $turma->codofeatvceu]);

      if ($busca->id_moodle) continue;

      $cursos_usuario[] = array(
        'codofeatvceu' => $turma->codofeatvceu,
        'nome_curso_apolo' => $busca->nome_curso_apolo
      );
    }
    
    return $cursos_usuario;
  }

  /**
   * Captura as informacoes de uma turma na tabela extensao_turma a
   * partir de seu codigo de oferecimento.
   * 
   * @param string $codofeatvceu Codigo de oferecimento da atividade.
   * 
   * @return object Resultado da busca na base.
   */
  public static function info_turma_id_extensao ($codofeatvceu) {
    global $DB;
    $infos = $DB->get_record('extensao_turma', ['codofeatvceu' => $codofeatvceu]);
    return $infos;
  }

  /**
   * Verifica se um usuario esta registrado como docente de uma turma.
   * 
   * @param integer|string $nusp_usuario Numero USP do usuario.
   * @param string         $codofeatvceu Codigo de oferecimento da turma.
   * 
   * @return bool Verdadeiro se o usuario estiver registrado como ministrante,
   *              falso caso contrario.
   */
  public static function usuario_docente_turma ($nusp_usuario, $codofeatvceu) {
    global $DB;
    // captura o id apolo da turma na base do extensao
    $info_turma = $DB->get_record('extensao_turma', ['codofeatvceu' => $codofeatvceu]);
    // agora ve se esta associada ao usuario
    $query = "SELECT * FROM {extensao_ministrante} WHERE codofeatvceu = $info_turma->codofeatvceu AND codpes = $nusp_usuario";
    $turma_associada = $DB->get_record_sql($query);
    return !empty($turma_associada);
  }

  /**
   * Verifica se uma turma no extensao_turma ja teve um ambiente criado
   * 
   * @param string $codofeatvceu Codigo de oferecimento da atividade.
   * 
   * @return integer|null Id da turma caso ja tenha sido criado, NULL caso
   *                      contrario.
   */
  public static function ambiente_criado_turma ($codofeatvceu) {
    global $DB;
    $query = $DB->get_record('extensao_turma', ['codofeatvceu' => $codofeatvceu]);
    return $query->id_moodle;
  }

  /**
   * Atualiza o codigo da turma no Moodle na tabela extensao_turma,
   * chamado quando a area da turma eh criada no Moodle.
   * 
   * OBS: Parece que do jeito que foi feito, sem um id primary_key,
   * nao funciona a API do Moodle.
   * 
   * @param string  $codofeatvceu Codigo de oferecimento da atividade.
   * @param integer $id_moodle    Id do curso associado.
   * 
   * @return object Resultado da Query.
   */
  public static function atualizar_id_moodle_turma ($codofeatvceu, $id_moodle) {
    global $DB;
    $query = "UPDATE {extensao_turma} SET id_moodle = $id_moodle WHERE codofeatvceu = $codofeatvceu";
    $query = $DB->execute($query);
    return $query;
  }

  /**
   * Captura o codofeatvceu de um curso a partir do id do ambiente
   * criado no Moodle.
   * 
   * @param string|integer $id_moodle Identificador do ambiente no Moodle.
   * 
   * @return object|null Resultado da busca.
   */
  public static function codofeatvceu($id_moodle) {
    global $DB;
    return $DB->get_record('extensao_turma', ['id_moodle' => $id_moodle]);
  }

  /**
   * Captura os alunos inscritos em uma turma na tabela extensao_aluno a
   * partir do codigo de oferecimento da atividade.
   * 
   * @param string|integer $codofeatvceu Codigo de oferecimento da atividade
   * 
   * @return array
   */
  public static function inscritos_turma ($codofeatvceu) {
    global $DB;

    $inscritos_obj = $DB->get_records('extensao_aluno', ['codofeatvceu' => $codofeatvceu]);
    $lista_inscritos = [];
    foreach ($inscritos_obj as $inscrito) {
      $lista_inscritos[] = array(
        'nome' => $inscrito->nome,
        'email' => $inscrito->email
      );
    }
    return $lista_inscritos;
  }
}