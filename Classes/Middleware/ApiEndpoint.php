<?php

namespace Vendor\Example\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspectFactory;
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
                $this->setLanguage($request);

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

    protected function setLanguage(ServerRequestInterface $request)
    {
        if (!$request->hasHeader('accept-language') || !$request->getAttribute('site')) {
            return;
        }

        $locale = strtolower(array_pop($request->getHeader('accept-language')));
        $site = $request->getAttribute('site');
        $languages = [];

        foreach ($site->getLanguages() as $language) {
            $tag = strtolower($language->getHreflang());
            $baseTag = strtolower($language->getTwoLetterIsoCode());
            $languages[$tag] = $languages[$tag] ?? $language;
            $languages[$baseTag] = $languages[$baseTag] ?? $language;
        }

        if (strlen($locale) === 4 && !isset($languages[$locale])) {
            $locale = substr($locale, 0, 2);
        }

        if (isset($languages[$locale])) {
            $this->context->setAspect(
                'language',
                LanguageAspectFactory::createFromSiteLanguage($languages[$locale])
            );
        }
    }
}