<?php
function is_valid_dni(string $dni): bool
{
    $letter = substr($dni, -1);
    $numbers = substr($dni, 0, -1);
    $patron = "/[0-9]{7,8}[A-Z]/";
    if (preg_match($patron, $dni) && substr("TRWAGMYFPDXBNJZSQVHLCKE", $numbers % 23, 1) == $letter && strlen($letter) == 1 ) {
        return true;
    }
    return false;
}

function HayNulos(array $camposNoNulos, array $arrayDatos): array
{
    $nulos = [];
    foreach ($camposNoNulos as $index => $campo) {
        if (!isset($arrayDatos[$campo]) || empty($arrayDatos[$campo]) || $arrayDatos[$campo] == null) {
            $nulos[] = $campo;
        }
    }
    return $nulos;
}

function existeValor(array $array, string $campo, mixed $valor): bool
{
        return in_array ($array[$campo],$valor);

}

function DibujarErrores($errores, $campo)
{
    $cadena = "";
    if (isset($errores[$campo])) {
        $last = end($errores);
        foreach ($errores[$campo] as $indice => $msgError) {
            $salto = ($errores[$campo] == $last) ? "" : "<br>";
            $cadena .= "{$msgError}.{$salto}";
        }
    }
    return $cadena;
}

function is_valid_email($str)
{
    return (false !== filter_var($str, FILTER_VALIDATE_EMAIL));
}