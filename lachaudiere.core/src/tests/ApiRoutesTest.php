<?php
declare(strict_types=1);

namespace lachaudiere\tests;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Uri;


class ApiRoutesTest extends TestCase
{
    private App $app;

    public static function setUpBeforeClass(): void
    {
    }

    protected function setUp(): void
    {
        $this->app = require __DIR__ . '/../conf/bootstrap.php';
    }


    private function createRequest(string $method, string $uri): Request
    {
        $uri = new Uri('', '', 80, $uri);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $headers = new Headers(['HTTP_ACCEPT' => 'application/json']);
        return new Request($method, $uri, $headers, [], [], $stream);
    }


    public function testGetCategoriesRoute(): void
    {
        $request = $this->createRequest('GET', '/api/categories');
        $response = $this->app->handle($request);
        $responseData = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('collection', $responseData['type']);
    }

    public function testGetEvenementsRoute(): void
    {
        $request = $this->createRequest('GET', '/api/evenements');
        $response = $this->app->handle($request);
        $responseData = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('collection', $responseData['type']);
    }

    public function testGetEvenementByIdRouteWithExistingEvent(): void
    {
        $existingPublishedEventId = 1;

        $request = $this->createRequest('GET', '/api/evenements/' . $existingPublishedEventId);
        $response = $this->app->handle($request);
        $responseData = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('resource', $responseData['type']);
    }

    public function testGetEvenementByIdRouteWithUnpublishedEvent(): void
    {
        $existingUnpublishedEventId = 2;

        $request = $this->createRequest('GET', '/api/evenements/' . $existingUnpublishedEventId);
        $response = $this->app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetEvenementByIdRouteWithNonExistingEvent(): void
    {
        $nonExistingEventId = 99999;

        $request = $this->createRequest('GET', '/api/evenements/' . $nonExistingEventId);
        $response = $this->app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testGetEvenementsByCategorieRoute(): void
    {
        $existingCategoryId = 1;

        $request = $this->createRequest('GET', '/api/categories/' . $existingCategoryId . '/evenements');
        $response = $this->app->handle($request);
        $responseData = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('collection', $responseData['type']);
    }
}