<?php

namespace Vendor\Example\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\GraphQL\EntityReader;
use TYPO3\CMS\GraphQL\Exception\ExecutionException;

class ApiEndpoint implements MiddlewareInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityReader
     */
    protected $entityReader;

    public function __construct(Context $context, EntityReader $entityReader)
    {
        $this->context = $context;
        $this->entityReader = $entityReader;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/api' && in_array('application/json', $request->getHeader('content-type'))) {
            $data = json_decode((string)$request->getBody(), true);

            if ($data !== null && isset($data['query']) && isset($data['variables'])) {
                try {
                    $result = $this->entityReader->execute($data['query'], $data['variables'], $this->context);

                    $stream = new Stream('php://memory','r+');
                    $stream->write(json_encode($result));

                    return new Response(
                        $stream,
                        200, 
                        [
                            'content-type' => 'application/json'
                        ]
                    );
                } catch (\Throwable $throwable) {
                    return (new Response())->withStatus(500, $throwable->getMessage());
                }
            }
        }

        return $handler->handle($request);
    }
}