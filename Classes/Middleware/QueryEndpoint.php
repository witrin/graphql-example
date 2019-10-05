<?php

namespace Vendor\Example\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspectFactory;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\GraphQL\EntityReader;
use TYPO3\CMS\GraphQL\Exception\ExecutionException;
use Vendor\Example\Context\ProfileAspect;

/**
 * @todo Refactoring might be needed here because of the manual initialisation of the TypoScriptFrontendController
 */
class QueryEndpoint implements MiddlewareInterface
{
    /**
     * @var SiteMatcher
     */
    protected $matcher;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityReader
     */
    protected $entityReader;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(SiteMatcher $matcher, Context $context, EntityReader $entityReader)
    {
        $this->matcher = $matcher;
        $this->context = $context;
        $this->entityReader = $entityReader;
        $this->serializer = new Serializer([], [new JsonEncoder()]);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/api' && in_array('application/json', $request->getHeader('content-type'))) {
            $data = json_decode((string)$request->getBody(), true);

            if ($data !== null && isset($data['query'])) {
                $this->setLanguage($request);
                $this->setTypoScriptFrontendController($request);

                try {
                    $this->context->setAspect('profile', new ProfileAspect(
                        $request->hasHeader('profile') ? $request->getHeader('profile') : 'default'
                    ));
                    
                    $result = $this->entityReader->execute($data['query'], $data['variables'] ?? [], $this->context);

                    $stream = new Stream('php://memory','r+');
                    $stream->write($this->serializer->serialize($result, 'json'));

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

    protected function setTypoScriptFrontendController(ServerRequestInterface $request)
    {
        $site = $request->getAttribute('site', null);
        $routeResult = $this->matcher->matchRequest($request->withUri($request->getUri()->withPath('/')));

        $controller = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $this->context,
            $site,
            $request->getAttribute('language', $site->getDefaultLanguage()),
            $site->getRouter()->matchRequest($request, $routeResult),
            $request->getAttribute('frontend.user', null)
        );

        $controller->determineId();
        $controller->getFromCache();
        $controller->getConfigArray();
        $controller->settingLanguage();

        $GLOBALS['TSFE'] = $controller;
    }

    protected function setLanguage(ServerRequestInterface $request)
    {
        if (!$request->hasHeader('accept-language') || !$request->getAttribute('site')) {
            return;
        }

        $locale = strtolower(array_pop($request->getHeader('accept-language')));
        $site = $request->getAttribute('site', null);
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