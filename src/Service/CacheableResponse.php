<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class CacheableResponse extends Response
{
    public function __construct(?string $content, int $statusCode = 200, array $header = [], int $maxAge = 3600)
    {
        parent::__construct($content, $statusCode, $header);
        $this->setPublic();
        $this->headers->addCacheControlDirective('must-revalidate', true);
        $this->setMaxAge($maxAge);
    }
}