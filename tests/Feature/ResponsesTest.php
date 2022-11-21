<?php

namespace Tests\Feature;

use App\Responses\Check\Success as CheckSuccess;
use App\Responses\Standard\FailedAuthentication;
use App\Responses\Standard\MissingData;
use App\Responses\Standard\Success;
use Tests\TestCase;

class ResponsesTest extends TestCase
{
    public function test_unauthenticated_standard_requests_return_403()
    {
        $response = $this->postJson(route('kamar'));

        $response->assertJson((new FailedAuthentication())->toArray());
    }

    public function test_unauthenticated_check_requests_return_403_with_service_details()
    {
        $response = $this->postJson(route('kamar'), $this->checkRequest());

        $response->assertJson((new FailedAuthentication())->toArray());
    }

    public function test_standard_requests_with_invalid_credentials_return_403()
    {
        $response = $this->withHeaders([
            'HTTP_AUTHORIZATION' => $this->invalidCredentials(),
        ])->postJson(route('kamar'));

        $response->assertJson((new FailedAuthentication())->toArray());
    }

    public function test_check_requests_with_invalid_credentials_return_403_with_service_details()
    {
        $response = $this->withHeaders([
            'HTTP_AUTHORIZATION' => $this->invalidCredentials(),
        ])->postJson(route('kamar'), $this->checkRequest());

        $response->assertJson((new FailedAuthentication())->toArray());
    }

    public function test_authenticated_standard_requests_with_blank_data_return_400()
    {
        $response = $this->withHeaders([
            'HTTP_AUTHORIZATION' => $this->validCredentials(),
        ])->postJson(route('kamar'));

        $response->assertJson((new MissingData())->toArray());
    }

    public function test_authenticated_standard_requests_with_empty_data_return_400()
    {
        $response = $this->withHeaders([
            'HTTP_AUTHORIZATION' => $this->validCredentials(),
        ])->postJson(route('kamar'), []);

        $response->assertJson((new MissingData())->toArray());
    }

    public function test_authenticated_check_requests_return_0_and_service_details()
    {
        $response = $this->withHeaders([
            'HTTP_AUTHORIZATION' => $this->validCredentials(),
        ])->postJson(route('kamar'), $this->checkRequest());

        $response->assertJson((new CheckSuccess())->toArray());
    }

    public function test_authenticated_standard_requests_return_0()
    {
        $response = $this->withHeaders([
            'HTTP_AUTHORIZATION' => $this->validCredentials(),
        ])->postJson(route('kamar'), $this->partRequest());

        $response->assertJson((new Success())->toArray());
    }

    private function validCredentials()
    {
        return "Basic " . base64_encode(config('kamar.username') . ':' . config('kamar.password'));
    }

    private function invalidCredentials()
    {
        return "Basic " . base64_encode('username' . ':' . 'password');
    }

    private function partRequest()
    {
        return [
            'SMSDirectoryData' => [
                'sync' => 'part'
            ]
            ];
    }

    private function checkRequest()
    {
        return [
            'SMSDirectoryData' => [
                'sync' => 'check'
            ]
            ];
    }
}
