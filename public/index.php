<?php

declare(strict_types=1);

// 1 -Identifico la ruta y el metodo
// con el operador ?? (Null Coalescing Operator) comprobamos si existe (buscando si la 'REQUEST_URI' esta definida en el array $_SERVER)
// Comprueba si es nula, si no existiera o es null, devuelve lo de la derecha, en este caso seria '/'
//El proposito de usar ?? es evitar que PHP lance un aviso de tipo "Notice: Undefined index"

//la funcion parse_url(string url, int component) esta funcion toma una url completa (como /productos?id=10) y gracias a la constante PHP_URL_PATH
//extrae solo la ruta, ignorando el parametro de busqueda (en este caso ?id=10), utilizo el operador ternario para que en caso de que la url este mal formada y devuelva false
// asigne el valor por defecto '/'
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
// Aqui igual para obtener el metodo GET, POST, PUT, DELETE,ETC.. utilizamos el array $_SERVER lo que queremos obtener es el 'REQUEST_METHOD' en caso de nulo o no existiera devuelve 'GET'
// utilizo el metodo strtoupper para asegurarme que esta en mayuscula.
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

//2 - Resoponder un endpoint de salud
// Utilizaremos esta ruta para comprobar que la API esta viva y sin problemas
if ($method === 'GET' && $path === '/api/health') {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8'); //Aqui n os encargamos de decirle al navegador que vamos a enviar un json, ademas a;ade el utf-8 para que se vean tildes y ñ
    echo json_encode(['ok' => true, 'time' => date('c')], JSON_UNESCAPED_UNICODE); // Aqui simplemente convertimos un array de PHP en un string  copn formato JSON, con la fecha en formato ISO 8601, y con la constante JSON_UNESCAPED_UNICODE nos aseguramos de que PHP no transforme caracteres especiales en codigos raros 
    exit; //y detenemos la ejecucion de PHP
}


//Es temporal hasta que definamos los distintos endpoints
http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => ['message' => 'No encontrado']], JSON_UNESCAPED_UNICODE);
