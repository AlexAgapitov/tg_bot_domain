<?php

namespace Core;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class App
{

    public static array $middleware = [];

    public function run(): void
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $app = new \Silex\Application();

            $app->before(function (Request $request) {
                if (str_starts_with($request->headers->get('Content-Type'), 'application/json')) {
                    $data = json_decode($request->getContent(), true);
                    $request->request->replace(is_array($data) ? $data : array());
                }
                Router::setParams();
            });

            $app->get('/api/v1/dictionary/get/times', function () use ($app) {
                return self::buildSuccess(['times' => Router::getTimes()]);
            });

            $app->get('/api/v1/dictionary/get/days', function () use ($app) {
                return self::buildSuccess(['days' => Router::getDays()]);
            });

            $app->post('/api/v1/domain/post/set', function () use ($app) {
                return
                    false !== ($id = Router::addDomain())
                    ? self::buildSuccess(['id' => $id])
                    : self::buildError(['message' => Router::getErrorMessage()])
                ;
            });

            $app['debug'] = true;

            $app->run();

        } else {
            $args = $_SERVER['argv'];

            switch ($args[1]) {
                case 'add':
                    $array = !empty($args[2]) ? json_decode($args[2], true) : null;
                    if (empty($array)) {
                        throw new Exception('Error in string');
                    }
                    Router::addDomain();
                    break;
                case 'getTimes':
                    Router::getTimes();
                    break;
                case 'getDays':
                    Router::getDays();
                    break;
                default:
                    throw new Exception('Command not found');
            }
        }
    }

    private function buildSuccess(array $content): JsonResponse
    {
        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData([
            'ok' => 1,
            'content' => $content,
        ]);
        return $response;
    }

    private function buildError(array $content): JsonResponse
    {
        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData([
            'ok' => 0,
            'content' => $content,
        ]);
        $response->setStatusCode(400);
        return $response;
    }
}