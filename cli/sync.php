<?php
/**
 * Sincronizacao
 * 
 * Para sincronizar os dados do Moodle Extensao com os do Apolo.
 *
 * Uso:
 *   # php sync.php [--apagar] [--pular_ministrantes] [--pular_alunos]
 *   # php sync.php [--help|-h]
 * 
 * Opcoes:
 *   -h --help              Exibe essa ajuda.
 *   --apagar               Apaga os dados da base do Moodle e sincroniza com
 *                          o Apolo do zero.
 *   --pular_ministrantes   Nao sincroniza os ministrantes.
 *   --pular_alunos         Nao sincroniza os alunos.
 * 
 * Os dados ficam armazenados nas tabelas:
 * 1. mdl_extensao_turma:       Dados de turmas;
 * 2. mdl_extensao_ministrante: Dados de ministrantes e suas turmas;
 * 3. mdl_extensao_aluno:       Dados de alunos e suas matriculas.
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../src/Service/Sincronizacao.php');


// Descricao do uso
$uso = "Para sincronizar os dados do Moodle Extensao com os do Apolo.

Uso:
  # php sync.php [--apagar] [--pular_ministrantes] [--pular_alunos]
  # php sync.php [--help|-h]

Opcoes:
  -h --help              Exibe essa ajuda.
  --apagar               Apaga os dados da base do Moodle e sincroniza com o Apolo do zero.
  --pular_ministrantes   Nao sincroniza os ministrantes.
  --pular_alunos         Nao sincroniza os alunos.
";

// opcoes
list($opcoes, $nao_reconhecidas) = cli_get_params([
  'help' => false,
  'apagar' => false,
  'pular_ministrantes' => false,
  'pular_alunos' => false
], [
  'h' => 'help'
]);

// tratamento de parametros informados que sao desconhecidos
if ($nao_reconhecidas) {
  $nao_reconhecidas = implode(PHP_EOL . '  ', $nao_reconhecidas);
  cli_error('Opcao(oes) desconhecida(s):', $nao_reconhecidas . PHP_EOL);
}

// se for um --help ou -h, tem que exibir
if ($opcoes['help']) {
  cli_writeln($uso);
  exit(2);
}

cli_writeln("
/*********************************/
/    SINCRONIZACAO COM O APOLO    /
/*********************************/");

// faz a sincronizacao
$sinc = new Sincronizar();
$apagar = $opcoes['apagar'];
$pular_ministrantes = $opcoes['pular_ministrantes'];
$pular_alunos = $opcoes['pular_alunos'];

$sinc->sincronizar($opcoes);

cli_writeln("
/*********************************/
/     SINCRONIZACAO CONCLUIDA     /
/*********************************/
");