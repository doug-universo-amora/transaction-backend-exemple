<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;

class TransactionTest extends TestCase
{
    public function testGet(): void
    {
        $response = Http::get(self::BASE_API_URL.'transaction/');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testNoneContentPost(): void
    {
        $response = Http::post(self::BASE_API_URL.'transaction/');
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testNoneContentPut(): void
    {
        $response = Http::put(self::BASE_API_URL.'transaction/1');
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testDelete(): void
    {
        $response = Http::delete(self::BASE_API_URL.'transaction/0');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
