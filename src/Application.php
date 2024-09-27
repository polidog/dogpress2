<?php

namespace Polidog\DogPress;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Parsedown;
use Psr\Http\Message\ServerRequestInterface;

class Application
{
    public function __invoke(ServerRequestInterface $request, Config $config): ResponseInterface
    {
        try {
            $path =  $config->getTemplatePath() . $request->getUri()->getPath();
            $file = strrpos($path, '/') ? $path . 'index.md' : $path . '.md';
            if (!file_exists($file)) {
                error_log('file not found: ' . $path . ' => ' . $file);
                // TODO: 404ページを表示する
                return new Response(404, [], 'Not Found');
            }

            $content = (new Parsedown())->text(file_get_contents($file));
            $renderer = new Renderer(__DIR__ . '/../template/main.php');

            return new Response(200, [], $renderer->render($content));
        } catch (\Throwable $e) {
           return new Response(500, [], 'Internal Server Error');
        }
    }

    public static function run(Config $config): void
    {
        $factory = new Psr17Factory();
        $serverRequest = (new ServerRequestCreator($factory, $factory, $factory, $factory))->fromGlobals();

        $response = (new self())($serverRequest, $config);

        $http_line = sprintf('HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        header($http_line, true, $response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }

        $stream = $response->getBody();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        while (!$stream->eof()) {
            echo $stream->read(1024 * 8);
        }
    }
}