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
//Vamos a hacer un array de rutas para enrutar los diferentes endpoints
//Antiguo if de health (lo dejo para aprendizaje)

/*if ($method === 'GET' && $path === '/api/health') {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8'); //Aqui n os encargamos de decirle al navegador que vamos a enviar un json, ademas a;ade el utf-8 para que se vean tildes y ñ
    echo json_encode(['ok' => true, 'time' => date('c')], JSON_UNESCAPED_UNICODE); // Aqui simplemente convertimos un array de PHP en un string  copn formato JSON, con la fecha en formato ISO 8601, y con la constante JSON_UNESCAPED_UNICODE nos aseguramos de que PHP no transforme caracteres especiales en codigos raros 
    exit; //y detenemos la ejecucion de PHP
}*/


// Lo hacemos como un array de rutas que despues de un bucle encuentra el match con la peticion del navegador por facilidad a la hora de introducir nuevas rutas, es mas escalable y legible
// Utilizo el mismo concepto que "/tasks/:id" de Express o rutas de Laravel/Symfony, asi es como funcionan por dentro.
$routes = [
    //Health
    ['GET', '#^/api/health$#', function () {
        json_response(['ok' => true, 'time' => date('c')], 200);
    }],

    //Projects (mock)
    ['GET', '#^/api/projects$#', function () {
        json_response(
            [
                'data' =>
                ['id' => 1, 'name' => 'Web coporativa'],
                ['id' => 2, 'name' => 'Portal interno']
            ],
            200
        );
    }],

    // Tasks list (mock)
    ['GET', '#^/api/tasks$#', function () {
        json_response(
            [
                'data' =>
                ['id' => 10, 'project_id' => 1, 'title' => 'Crear página contacto', 'status' => 'todo', 'priority' => 2],
                ['id' => 11, 'project_id' => 2, 'title' => 'Login con sesiones', 'status' => 'doing', 'priority' => 1],
            ],
            200
        );
    }],

    //Task detail con parametro {id}
    ['GET', '#^/api/tasks/(\d+)$#', function (string $id) {
        json_response(['data' => ['id' => (int)$id]], 200);
    }],


];


//Dispatcher (el encargado de enrutar los distintos endpoints)



foreach (
    $routes
    as [$m, $pattern, $handler]
) {
    if ($m !== $method) continue;

    if (preg_match($pattern, $path, $matches) === 1) {
        array_shift($matches); //quitamos el match completo
        $handler(...$matches);
        exit;
    }
}


// Si no hay match:
json_response(['error' => ['message' => 'No encontrado']], 404);


//Es temporal hasta que definamos los distintos end
/*http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => ['message' => 'No encontrado']], JSON_UNESCAPED_UNICODE);
*/



#Helpers
//Hago lo mismo que he hecho antes, pero lo dividimos en funciones por reutilizacion, en la funcion pasamos el payload en funcion de el endpoint (en el de salud devolvemos ok si esta en true y el tiempo), al; ser una prueba pondremos datos ficticios antes de unirlo con la base de datos
// y el status que queremos mostrar, despues añadimos la variable global JSON_UNESCAPED_UNICODE para que no convierta los caracteres especiales en nada raro
// y añadimos el header para el navegador.
function json_response(array $payload, int $status)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
}



