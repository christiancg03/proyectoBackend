<?php

function GenerarRutaJs(string $route): string {
    $ultimo = strrpos($route, "/");
    $ruta = substr($route, 0, $ultimo);
    $partes = explode("/", $route);
    $fichero = end($partes);
    $partes2 = explode(".", $fichero);
    $nombreFichero = $partes2[0] . ".js";
    $path = $ruta . "/js/" . $nombreFichero;
    return $path;
}

function router() {
    $url = $_SERVER["REQUEST_URI"];

    // si pongo solo la barra asumo que es ruta por defecto
    if (substr($url, -1) == "/") return "principal.php";

    // CORREGIDO: evita que strpos devuelva 0 como false
    if (strpos($url, "index.php") === false) return "views/404.php";

    // si no hay tabla, vista por defecto
    if (!isset($_REQUEST["tabla"]) || empty($_REQUEST["tabla"])) {
        return "principal.php";
    }

    $tablas = [
        "usuarios" => [
            "crear"   => "create.php",
            "guardar" => "store.php",
            "ver"     => "show.php",
            "listar"  => "list.php",
            "buscar"  => "search.php",
            "borrar"  => "delete.php",
            "editar"  => "edit.php",
            "perfil" => "perfil.php"
        ],
    ];

    $tabla = $_REQUEST["tabla"];
    if (!isset($tablas[$tabla])) return "views/404.php";

    $accion = $_REQUEST["accion"] ?? "listar";
    if (!isset($tablas[$tabla][$accion])) return "views/404.php";

    return "views/{$tabla}/{$tablas[$tabla][$accion]}";
}
