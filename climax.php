<?php
/**
 * Simples classe para facilitar o desenvolvimento
 * de scripts em PHP CLI...
 */
class phpTerminal
{
    private $error_delay         = 750000; // (0.5s)
    private $input_yn_error_msg  = "Tecla inválida, tente novamente...";
    private $input_key_error_msg = "Opção inválida, tente novamente...";

    function __construct() {
        #
    }

    /**
     * Limpa o terminal...
     */
    public function clear() {
        system('clear');
    }

    /**
     * Impede o uso de Ctrl+C e Ctrl+Z...
     */
    public function trap_sigs() {
        declare(ticks = 1);
        pcntl_signal(SIGINT, SIG_IGN);  // Ctrl+C
        pcntl_signal(SIGTSTP, SIG_IGN); // Ctrl+Z
    }

    /**
     * Imprime o texto com/sem quebra de linha...
     */
    public function out($s, $bl = true) {
        echo ($bl) ? $s.PHP_EOL : $s;
    }

    /**
     * Escreve nos streams de saída: out | error | buffer...
     */
    public function to($stdout = "out") {
        #
    }

    /**
     * Simples entrada de dados S/N
     */
    public function input_yn($msg, $opt = []) {
        $opt_defaults = [
            'valid'          => ['s','n'],
            'default_answer' => 's', // 'false' para nenhuma...
            'show_error'     => true
        ];
        $opt = $this->opt_args_parser($opt_defaults, $opt);
        $i = $this->prompt_loop($msg, $opt['valid'], $opt['default_answer'], $opt['show_error'], $this->input_yn_error_msg);
        return strtolower($i);
    }

    /**
     * Simples entrada de múltiplas opções (ex.: menus)...
     */
    public function input_opt($msg, $valid, $opt = []) {
        $opt_defaults = [
            'default_answer'    => false,
            'show_error'        => true
        ];
        $opt = $this->opt_args_parser($opt_defaults, $opt);
        $i = $this->prompt_loop($msg, $valid, $opt['default_answer'], $opt['show_error'], $this->input_key_error_msg);
        return strtolower($i);
    }

    /**
     * Simples entrada do tipo 'tecle qualquer coisa para continuar'...
     */
    public function input_any($msg) {
        $this->out($msg, false);
        $this->stty_silent(true);
        $i = fgetc(STDIN);
        $this->stty_silent(false);
        echo PHP_EOL;
    }

    /**
     * Simples entrada de uma linha de texto...
     */
    public function input_s($msg, $lenght = 1024) {
        $this->out($msg, false);
        $i = fgets(STDIN, $lenght);
        echo PHP_EOL;
        return $i;
    }

    /**
     * 06. Entrada em múltiplas linhas...
     */
    public function multiline($p = '~') {
        #
    }

    /**
     * 10. Transforma array associativa em um menu simples...
     */
    public function menu($options) {
        #
    }

    /**
     * 11. Entrada de senhas (oculta o texto digitado)...
     */
    public function password($s) {
        #
    }

    /**
     * 12. Caixas de seleção...
     */
    public function checkboxes($s, $options) {
        #
    }

    /**
     * 13. Botões de rádio...
     */
    public function radio($s, $options) {
        #
    }

    /**
     * 14. Tranforma arrays em tabela...
     */
    public function table($a) {
        #
    }

    /**
     * 15. Tranforma arrays em colunas...
     */
    public function columns($a, $n = 2) {
        #
    }

    /**
     * 16. Colunas com larguras definidas...
     */
    public function padding($a, $f = '.', $l = 0) {
        #
    }

    /**
     * 17. Imprime linhas horizontais...
     */
    public function hline($p = '-', $l = 80) {
        #
    }

    /**
     * 18. Barra de progresso...
     */
    public function progress($total = 100, $s = '=') {
        #
    }

    /**
     * 19. Avança a barra de progresso...
     */
    public function advance($n = 1, $s = '') {
        #
    }

    /**
     * 20. var_dump...
     */
    public function dump($a) {
        #
    }

    /**
     * 21. ### Mensagem em destaque! ###
     */
    public function headline($m, $symbol = '#', $n = 3) {
        #
    }

    /**
     * 22. Quebra de linha...
     */
    public function br() {
        echo PHP_EOL;
    }

    /**
     * 26. Sair...
     */
    public function fexit($wait = 0, $clear = false, $message = '') {
        #
    }

    /**
     * 27. Textos coloridos...
     */
    public function color($text, $name, $bg = false, $bold = false) {
        #
    }

    // Métodos de controle......................................................

    /**
     * Limpa a linha atual permitindo a sobrescrita...
     */
    private function clear_line() {
        system('echo -n "\033[1K\r"');
    }

    /**
     * Parser de argumentos
     */
    private function opt_args_parser($opt_defaults, $user) {
        if (count($user > 0)) {
            foreach ($user as $k => $v) {
                $opt_defaults[$k] = $v;
            }
        }
        return $opt_defaults;
    }

    /**
     * Loop comum a vários prompts...
     */
    private function prompt_loop($msg, $valid, $default_answer, $show_error, $default_error_msg) {
        $this->stty_silent(true);
        while (true) {
            $this->out($msg, false);
            $i = fgetc(STDIN);
            if (in_array(strtolower($i), $valid) || ($default_answer !== false && $i == PHP_EOL)) {
                if ($i == PHP_EOL) {
                    $i = $default_answer;
                }
                break;
            } else {
                $this->parse_error_msg($show_error, $default_error_msg);
            }
        }
        $this->stty_silent(false);
        echo PHP_EOL;
        return strtolower($i);
    }

    private function parse_error_msg($show_error, $default_error_msg) {
        if ($show_error === true) {
            $this->show_error_msg($default_error_msg);
        } elseif (!is_bool($show_error) && trim($show_error) != "") {
            $this->show_error_msg($show_error);
        } else {
            $this->show_error_msg('');
        }
    }

    /**
     * Exibe mensagens de erro...
     */
    private function show_error_msg($msg) {
        $this->clear_line();
        echo ($msg);
        if (trim($msg) != "") {
            usleep($this->error_delay);
        }
        $this->clear_line();
    }

    private function stty_silent($status) {
        if ($status) {
            shell_exec('stty -echo -icanon');
        } else {
            shell_exec('stty echo icanon');
        }
    }

}



?>
