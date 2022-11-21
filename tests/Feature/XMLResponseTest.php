<?php

namespace Tests\Feature;

use App\Responses\Check\XMLSuccess as XMLCheckSuccess;
use App\Responses\Standard\XMLFailedAuthentication;
use App\Responses\Standard\XMLMissingData;
use App\Responses\Standard\XMLSuccess;
use Spatie\ArrayToXml\ArrayToXml;
use Tests\TestCase;

class XMLResponseTest extends TestCase
{
    public function test_unauthenticated_standard_xml_requests_return_403()
    {
        $response = $this
            ->call(
                'POST',
                route('kamar'),
                [],
                [],
                [],
                $this->transformHeadersToServerVars(['content-type' => 'application/xml']),
                $this->xmlFullRequestXml()
            );

        $response->assertSee(
            (string) (new XMLFailedAuthentication),
            false
        );
    }

    public function test_unauthenticated_check_xml_requests_return_403_with_service_details()
    {
        $response = $this
            ->call(
                'POST',
                route('kamar'),
                [],
                [],
                [],
                $this->transformHeadersToServerVars(['content-type' => 'application/xml']),
                $this->xmlCheckRequestXml()
            );

        $response->assertSee(
            (string) (new XMLFailedAuthentication),
            false
        );
    }

    public function test_standard_xml_requests_with_invalid_credentials_return_403()
    {
        $response = $this
            ->call(
                'POST',
                route('kamar'),
                [],
                [],
                [],
                $this->transformHeadersToServerVars([
                    'content-type' => 'application/xml',
                    'HTTP_AUTHORIZATION' => $this->invalidCredentials(),
                ]),
                $this->xmlFullRequestXml()
            );

        $response->assertSee(
            (string) (new XMLFailedAuthentication),
            false
        );
    }

    public function test_check_xml_requests_with_invalid_credentials_return_403_with_service_details()
    {
        $response = $this
            ->call(
                'POST',
                route('kamar'),
                [],
                [],
                [],
                $this->transformHeadersToServerVars([
                    'content-type' => 'application/xml',
                    'HTTP_AUTHORIZATION' => $this->invalidCredentials(),
                ]),
                $this->xmlCheckRequestXml()
            );

        $response->assertSee(
            (string)(new XMLFailedAuthentication()),
            false
        );
    }

    public function test_authenticated_standard_xml_requests_with_blank_data_return_400()
    {
        $response = $this
            ->call(
                'POST',
                route('kamar'),
                [],
                [],
                [],
                $this->transformHeadersToServerVars([
                    'content-type' => 'application/xml',
                    'HTTP_AUTHORIZATION' => $this->validCredentials(),
                ])
            );

        $response->assertSee(
            (string) (new XMLMissingData()),
            false
        );
    }

    public function test_authenticated_standard_xml_requests_with_empty_data_return_400()
    {
        $response = $this
            ->call(
                'POST',
                route('kamar'),
                [],
                [],
                [],
                $this->transformHeadersToServerVars([
                    'content-type' => 'application/xml',
                    'HTTP_AUTHORIZATION' => $this->validCredentials(),
                ]),
                ''
            );

        $response->assertSee(
            (string) (new XMLMissingData()),
            false
        );
    }

    public function test_authenticated_check_xml_requests_return_0_and_service_details()
    {
        $response = $this
            ->call(
                'POST',
                route('kamar'),
                [],
                [],
                [],
                $this->transformHeadersToServerVars([
                    'content-type' => 'application/xml',
                    'HTTP_AUTHORIZATION' => $this->validCredentials(),
                ]),
                $this->xmlCheckRequestXml()
            );

        $response->assertSee(
            (string) (new XMLCheckSuccess()),
            false
        );
    }

    public function test_authenticated_standard_xml_requests_return_0()
    {
        $response = $this
        ->call(
            'POST',
            route('kamar'),
            [],
            [],
            [],
            $this->transformHeadersToServerVars([
                'content-type' => 'application/xml',
                'HTTP_AUTHORIZATION' => $this->validCredentials(),
            ]),
            $this->xmlFullRequestXml()
        );

    $response->assertSee(
        (string) (new XMLSuccess()),
        false
    );

    }

    private function validCredentials()
    {
        return "Basic " . base64_encode(config('kamar.username') . ':' . config('kamar.password'));
    }

    private function invalidCredentials()
    {
        return "Basic " . base64_encode('username' . ':' . 'password');
    }

    private function xmlCheckRequestXml()
    {
        return '<smsdirectorydata datetime="20200930131754" sync="check" sms="KAMAR" version="1453">
                    <infourl>https://help.mydomain.nz/</infourl>
                    <privacystatement>data privacy statement</privacystatement>
                    <schools>
                        <school index="1">
                            <moecode>0123</moecode>
                            <name>My Sample School</name>
                            <type>32</type>
                            <authoritative>true</authoritative>
                        </school>
                    </schools>
                </smsdirectorydata>';
    }

    private function xmlFullRequestXml()
    {
        return '<smsdirectorydata datetime="20200930131754" sync="full" sms="KAMAR" version="1453">
                    <infourl>https://help.mydomain.nz/</infourl>
                    <privacystatement>data privacy statement</privacystatement>
                    <schools>
                        <school index="1">
                            <moecode>0123</moecode>
                            <name>My Sample School</name>
                            <type>32</type>
                            <authoritative>true</authoritative>
                        </school>
                    </schools>
                </smsdirectorydata>';
    }
}
