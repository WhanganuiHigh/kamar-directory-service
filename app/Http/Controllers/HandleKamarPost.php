<?php

namespace App\Http\Controllers;

use App\{AuthenticationCheck, KamarData};
use App\Responses\Check\Success as CheckSuccess;
use App\Responses\Check\XMLSuccess as XMLCheckSuccess;
use App\Responses\Standard\{Success, FailedAuthentication, MissingData};
use App\Responses\Standard\{XMLSuccess, XMLFailedAuthentication, XMLMissingData};

use Illuminate\Support\Facades\Log;


class HandleKamarPost extends Controller
{

    public function __construct(
        protected AuthenticationCheck $authCheck,
        protected KamarData $data,
    ) {
        $this->data = KamarData::fromRequest();
    }

    public function __invoke()
    {

        Log::info($this->data->getSyncType());
        // Check supplied username/password matches our expectation
        if ($this->authCheck->fails()) {
            if ($this->data->isJson()) {
                return response()->json(new FailedAuthentication());
            }
            if ($this->data->isXml()) {
                return response()->xml(new XmlFailedAuthentication());
            }
        }

        // Check we have some data
        if ($this->data->isMissing()) {
            if ($this->data->isJson()) {
                return response()->json(new MissingData());
            }
            if ($this->data->isXml()) {
                return response()->xml(new XmlMissingData());
            }
        }


        // Check if a 'check' sync, return check response.
        if ($this->data->isSyncCheck()) {
            if ($this->data->isJson()) {
                return response()->json(new CheckSuccess());
            }
            if ($this->data->isXml()) {
                return response()->xml(new XmlCheckSuccess());
            }
        }

        // All other messages - store the data and return 'OK' response.
        return $this->handleOKResponse();
    }

    private function handleOKResponse()
    {
        // Do something
        $this->data->store();
        if ($this->data->isJson()) {
            return response()->json(new Success());
        }
        if ($this->data->isXml()) {
            return response()->xml(new XmlSuccess());
        }
    }
}
