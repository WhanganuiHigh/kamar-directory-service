<?php

namespace App\Responses;

use Stringable;
use Spatie\ArrayToXml\ArrayToXml;

abstract class AbstractXMLResponse implements Stringable
{
    protected string $service;
    protected float $version;
    protected int $error = 501;
    protected string $result = "Not Implemented";
    protected array $additionalFields = [];

    public function __construct()
    {
        $this->service = config('kamar.serviceName');
        $this->version = config('kamar.serviceVersion');
    }

    public function __toString()
    {
        return ArrayToXml::convert(
            array_merge(
                [
                    'service' => $this->service,
                    'version' => $this->version,
                    'error' => $this->error,
                    'result' => $this->result,
                ],
                $this->additionalFields
            ),
            'SMSDirectoryData'
        );
    }
}
