<?php
require 'vendor/autoload.php';

use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;

$server = new HttpServer(function (ServerRequestInterface $request) {
    $path = $request->getUri()->getPath();
    $method = $request->getMethod();

    $staticFile = __DIR__ . '/public' . $path;

    if (file_exists($staticFile) && is_file($staticFile)) {
        // Detectar el tipo MIME
        $extension = pathinfo($staticFile, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
        ];
    
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        $content = file_get_contents($staticFile);
    
        return new Response(
            200,
            ['Content-Type' => $mimeType],
            $content
        );
    }

    switch ($path) {
        case '/':
            return new Promise(function ($resolve) {
                $file = __DIR__ . '/public/index.html';

                if (!file_exists($file)) {
                    $resolve(new Response(500, ['Content-Type' => 'text/plain'], 'Archivo HTML no encontrado'));
                    return;
                }

                $html = file_get_contents($file);
                $resolve(new Response(200, ['Content-Type' => 'text/html'], $html));
            });

            case '/contact':
                return new Promise(function ($resolve) use ($request) {
                    $method = $request->getMethod();
                    $_SERVER['REQUEST_METHOD'] = $method;
            
                    $_POST = [];
            
                    if ($method === 'POST') {
                        // Obtener y parsear el cuerpo del formulario
                        $body = (string)$request->getBody();
                        parse_str($body, $_POST); // Esto llenará $_POST correctamente
                    }
            
                    ob_start();
                    include __DIR__ . '/views/contact.php';
                    $html = ob_get_clean();
            
                    $resolve(new Response(
                        200,
                        ['Content-Type' => 'text/html'],
                        $html
                    ));
                });


                case '/data':
                    $file = __DIR__ . '/data/contactos.json';
                    $method = $request->getMethod();
                    $queryParams = $request->getUri()->getQuery(); // Obtener cadena después de ?
                    parse_str($queryParams, $query); // Convertir a array asociativo
                
                    if (!file_exists($file)) {
                        file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
                    }
                
                    $data = json_decode(file_get_contents($file), true);
                
                    switch ($method) {
                        case 'GET':
                            return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, JSON_PRETTY_PRINT));
                
                        case 'POST':
                            $params = json_decode($request->getBody()->getContents(), true);
                            if (!$params) {
                                return new Response(400, ['Content-Type' => 'text/plain'], 'Datos inválidos');
                            }
                
                            $nuevo = [
                                "id" => time(),
                                "nombre" => $params['nombre'] ?? '',
                                "email" => $params['email'] ?? '',
                                "mensaje" => $params['mensaje'] ?? '',
                                "fecha" => date('Y-m-d H:i:s')
                            ];
                
                            $data[] = $nuevo;
                            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
                
                            return new Response(201, ['Content-Type' => 'application/json'], json_encode($nuevo));
                
                        case 'PUT':
                            if (!isset($query['id'])) {
                                return new Response(400, ['Content-Type' => 'text/plain'], 'ID requerido');
                            }
                
                            $id = $query['id'];
                            $params = json_decode($request->getBody()->getContents(), true);
                            $encontrado = false;
                
                            foreach ($data as &$item) {
                                if ($item['id'] == $id) {
                                    $item['nombre'] = $params['nombre'] ?? $item['nombre'];
                                    $item['email'] = $params['email'] ?? $item['email'];
                                    $item['mensaje'] = $params['mensaje'] ?? $item['mensaje'];
                                    $item['fecha'] = date('Y-m-d H:i:s');
                                    $encontrado = true;
                                    break;
                                }
                            }
                
                            if (!$encontrado) {
                                return new Response(404, ['Content-Type' => 'text/plain'], 'Elemento no encontrado');
                            }
                
                            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
                            return new Response(200, ['Content-Type' => 'application/json'], json_encode(['actualizado' => true]));
                
                        case 'DELETE':
                            if (!isset($query['id'])) {
                                return new Response(400, ['Content-Type' => 'text/plain'], 'ID requerido');
                            }
                
                            $id = $query['id'];
                            $originalCount = count($data);
                            $data = array_filter($data, fn($item) => $item['id'] != $id);
                
                            if (count($data) === $originalCount) {
                                return new Response(404, ['Content-Type' => 'text/plain'], 'Elemento no encontrado');
                            }
                
                            file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT));
                            return new Response(200, ['Content-Type' => 'application/json'], json_encode(['eliminado' => true]));
                
                        default:
                            return new Response(405, ['Content-Type' => 'text/plain'], 'Método no permitido');
                    }

                    case '/data.html':
                        $filePath = __DIR__ . '/data.html';
                        return new Response(
                            200,
                            ['Content-Type' => 'text/html'],
                            file_get_contents($filePath)
                        );
                        
                
                    
        default:
            return new Response(
                404,
                ['Content-Type' => 'text/plain'],
                "Página no encontrada"
            );
    }
});

$socket = new SocketServer('127.0.0.1:8080');
$server->listen($socket);

echo "Servidor corriendo en http://127.0.0.1:8080\n";

React\EventLoop\Loop::get()->run();
