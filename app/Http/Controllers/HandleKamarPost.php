<?php

namespace App\Http\Controllers;

use App\Responses\Check\{Success as CheckSuccess, FailedAuthentication as CheckFailedAuthentication};
use App\Responses\Standard\{Success, FailedAuthentication, MissingData};
use App\{AuthenticationCheck, KamarData};

class HandleKamarPost extends Controller
{

    public function __construct(
        protected AuthenticationCheck $authCheck,
        protected KamarData $data,
    ) {
    }

    public function __invoke()
    {
        // Check supplied username/password  matches our expectation
        if ($this->authCheck->fails()) {
            if ($this->data->isSyncCheck()) {
                return response()->json(new CheckFailedAuthentication());  // failed during check, we have to return error, result, version and service
            } else {
                return response()->json(new FailedAuthentication());  // failed during any other request, we only have to return error and result
            }
        }

        // Check we have some data
        if ($this->data->isMissing()) {
            return response()->json(new MissingData());
        }

        // Check if a 'check' sync, return check response.
        if ($this->data->isSyncCheck()) {
            return response()->json(new CheckSuccess());
        }

        // All other messages - store the data and return 'OK' response.
        return $this->handleOKResponse();
    }

    private function handleOKResponse()
    {
        // Do something
        $this->data->store();
        return response()->json(new Success());
    }
}
