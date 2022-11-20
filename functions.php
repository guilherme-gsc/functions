<?php
/**
 * FUNÇÔES ÙTEIS
 */


/**
 * Derixa palavras com todas as letras minúsculas.
 *
 * @param string $word
 * @return string
 */
function toLower(string $word): string
{
    return mb_convert_case($word, MB_CASE_LOWER);
}

/**
 * Formata palavras com a primeira letra maiúsculas.
 * Usada em Nomes próprios, compostos ou não.
 *
 * @param string $word
 * @return string
 */
function word_format(string $word): string
{
    $words = explode(" ", trim(toLower($word)));

    //Se nome composto.
    if (count($words) > 1) {
        $ignore = ["do", "da", "dos", "das", "de", "e"];
        foreach ($words as $word) {
            $formated[] = (in_array($word, $ignore)) ? $word : ucfirst($word);
        }

        $wordFormated = implode(" ", $formated);
        return trim($wordFormated);
    }

    //Se nome simples
    $wordFormated = ucfirst(toLower($word));
    return trim($wordFormated);
}

//FUNÇÕES DE NÚMEROS
/**
 * Formata número com ponto de milhar.
 * @param int $number
 * @return int
 */
function format_number(int $number): int
{
    $numberFormated = number_format($number, 0, ",", ".");
    return trim($numberFormated);
}

/**
 * Formata moeda.
 *
 * @param float $number
 * @return float
 */
function real_money(float $number): float
{
    $realMoney = number_format($number, 2, ",", ".");
    return trim($realMoney);
}

/**
 * Elimina possíveis máscaras inseridas em campos de formulários.
 * campos como cpf, cnpj que utilizam caracteres de pontuação.
 *
 * @param string $param
 * @return string
 */
function only_number(string $param): string
{
    $number = preg_replace('/\D/', '', $param);
    return trim($number);
}

/**
 * Verifica se cpf ou cnpj são válidos.
 *
 * @param string $param
 * @return boolean
 */
function verify_document(string $param): bool
{
    //Remove pontuações.
    $document = only_number($param);

    //Valida se está vazio ou númeração repetida.
    if (is_empty($document) || is_repeated($document)) {
        return false;
    }

    //Pega tamanho da string (CPF ou CNPJ).
    $size = strlen($document);

    //Elimina os dois últimos dígitos da string para fazer o cálculo.
    $verify = substr($document, 0, ($size - 2));

    switch ($size) {
        case "11":
            //Calcula dígitos do cpf
            $verify .= cpf_calculator($verify);
            $verify .= cpf_calculator($verify);
            break;
        case "14":
            //Calcula dígitos do cnpj
            $verify .= cnpj_calculator($verify);
            $verify .= cnpj_calculator($verify);
            break;
        default:
            return false;
    }

    return ($document == $verify);
}

/**
 * Calcula dígitos verificadores do cnpj.
 *
 * @param string $param
 * @return string
 */
function cnpj_calculator(string $param): string
{
    $size = strlen($param);
    $digit = 9;
    $sum = 0;

    for ($i = ($size - 1); $i >= 0; $i--) {
        $sum += $param[$i] * $digit;

        $digit--;
        $digit = ($digit < 2) ? 9 : $digit;
    }

    return $sum % 11;
}

/**
 * Calcula dígitos verificadores de cpf.
 *
 * @param string $param
 * @return string
 */
function cpf_calculator(string $param): string
{
    $size = strlen($param);
    $digit = ($size + 1);
    $sum = 0;

    for ($i = 0; $i < $size; $i++) {
        $sum += $param[$i] * $digit;
        $digit--;
    }

    $module = $sum % 11;
    return $module > 1 ? (11 - $module) : 0;
}

/**
 * Verifica se variável está vazia.
 *
 * @param string $param
 * @return boolean
 */
function is_empty(string $param): bool
{
    if (empty($param) || $param == null) {
        return true;
    }
}

/**
 * Verifica se variável possui caracteres repetidos.
 * Utilizada na validação de digitação de documentos (cpf, cnpj)
 * @param string $param
 * @return boolean
 */
function is_repeated(string $param): bool
{
    $repeats = [
        "00000000000", "11111111111", "22222222222", "33333333333",
        "44444444444", "55555555555", "66666666666", "77777777777",
        "88888888888", "99999999999", "123456789"
    ];

    if (in_array($param, $repeats)) {
        return true;
    }
}
