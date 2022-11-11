<?php

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class DownloadFileTest extends ApiTestCase
{
    public function testCsvFileNotFound(): void
    {
        static::createClient()->request('GET', '/api/files/not-found', [
            'headers' => [
                'Accept' => 'text/csv',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        $this->assertJsonContains([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testCsvFileFound(): void
    {
        $response = static::createClient()->request('GET', '/api/files/test', [
            'headers' => [
                'Accept' => 'text/csv',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertResponseHeaderSame('Content-Disposition', 'attachment; filename=test.csv');
        $this->assertSame(
            file_get_contents(static::getContainer()->getParameter('app.csv_export_dir').DIRECTORY_SEPARATOR.'test.csv'),
            $response->getKernelResponse()->getFile()->getContent()
        );
    }
}
