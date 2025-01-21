<?php

namespace Core;

use Exception;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

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
            });

            $app->get('/api/v1/dictionary/get/times', function () use ($app) {
                return self::buildSuccess(['times' => Router::getTimes()]);
            });

            $app->get('/api/v1/dictionary/get/days', function () use ($app) {
                return self::buildSuccess(['days' => Router::getDays()]);
            });

            $app->post('/api/v1/domain/post/set', function (Request $request) use ($app) {
                return
                    false !== ($data = Router::addDomain($request->request->all()))
                    ? self::buildSuccess(['data' => $data])
                    : self::buildError(['message' => Router::getErrorMessage()])
                ;
            })->before(function (Request $request) use ($app) {
                $constraint = new Assert\Collection([
                    'user_id' => new Assert\Positive(),
                    'name' => new Assert\Length(['min' => 1]),
                    'time' => new Assert\Positive(),
                    'days' => new Assert\Positive(),
                ]);
                self::validation($request->request->all(), $constraint, $app);
            });

            $app->post('/api/v1/domain/post/get', function (Request $request) use ($app) {
                return self::buildSuccess(['domains' => Router::getDomains($request->request->all())]);
            })->before(function (Request $request) use ($app) {
                $constraint = new Assert\Collection([
                    'user_id' => new Assert\Positive()
                ]);
                self::validation($request->request->all(), $constraint, $app);
            });

            $app->error(function (\Exception $e) use ($app) {
                if ($e instanceof BadRequestHttpException) {
                    return self::buildError(['message' => $e->getMessage()]);
                }
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
                    Router::addDomain($array);
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

    private static function validation(array $inputs, Assert\Collection $constraint, Application $app)
    {
        $validator = Validation::createValidator();


        $violations = $validator->validate($inputs, $constraint);

        if (count($violations) > 0) {
//            $errors = [];
//            foreach ($violations AS $violation) {
//                $errors[] = $violation->getPropertyPath();
//            }
            var_dump($violations);exit;
            throw new BadRequestHttpException('Validation error');
        }
    }

    private static function buildSuccess(array $content): JsonResponse
    {
        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData([
            'ok' => 1,
            'content' => $content,
        ]);
        return $response;
    }

    private static function buildError(array $content): JsonResponse
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