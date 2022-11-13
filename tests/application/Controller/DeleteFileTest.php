<?php

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteFileTest extends ApiTestCase
{
    public function testCsvFileNotFound(): void
    {
        static::createClient()->request('DELETE', '/api/files/not-found');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Error',
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'Not Found',
        ]);
    }

    public function testCsvFileFound(): void
    {
        file_put_contents(static::getContainer()->getParameter('app.csv_export_dir').DIRECTORY_SEPARATOR.'delete.csv', 'deleted');
        $response = static::createClient()->request('DELETE', '/api/files/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertEmpty($response->getContent());
        $this->assertFalse(file_exists(static::getContainer()->getParameter('app.csv_export_dir').DIRECTORY_SEPARATOR.'delete.csv'));
    }
}
