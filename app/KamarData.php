<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\Storage;

class KamarData
{
    const SYNC_TYPE_CHECK = 'check';
    const SYNC_TYPE_PART = 'part';
    const SYNC_TYPE_FULL = 'full';
    const SYNC_TYPE_ASSESSMENTS ='assessments';
    const SYNC_TYPE_ATTENDANCE ='attendance';
    const SYNC_TYPE_BOOKINGS ='bookings';
    const SYNC_TYPE_CALENDAR ='calendar';
    const SYNC_TYPE_NOTICES ='notices';
    const SYNC_TYPE_PASTORAL ='pastoral';
    const SYNC_TYPE_PHOTOS ='photos';
    const SYNC_TYPE_STAFFPHOTOS ='staffphotos';
    const SYNC_TYPE_STUDENTTIMETABLES ='studenttimetables';
    const SYNC_TYPE_STAFFTIMETABLES ='stafftimetables';

    public $data;
    public $format = 'json';

    public function isMissing()
    {
        return $this->format == 'json'
        ? empty(data_get($this->data, 'SMSDirectoryData'))
        : $this->data->isEmpty();
    }

    public function getSyncType()
    {
        return $this->format == 'json'
        ? data_get($this->data,  'SMSDirectoryData.sync')
        : data_get($this->data,  '@attributes.sync');
    }

    public function isSyncCheck()
    {
        return $this->getSyncType() === self::SYNC_TYPE_CHECK;
    }

    public function isSyncPart()
    {
        return $this->getSyncType() === self::SYNC_TYPE_PART;
    }

    public function isSyncFull()
    {
        return $this->getSyncType() === self::SYNC_TYPE_FULL;
    }

    public function store()
    {
        $filename = $this->getSyncType() . "_" . time() . "_" . mt_rand(1000, 9999) . "." . $this->format;
        Storage::put('data/' . $filename, request()->getContent());
    }

    public function isJson()
    {
        return $this->format === 'json';
    }

    public function isXml()
    {
        return $this->format === 'xml';
    }

    public static function fromRequest()
    {
        $kamarData = new static;
        if (request()->isJson()) {
            $kamarData->data = collect(request()->input());
        } elseif (request()->isXml()) {
            $kamarData->data = request()->getContent() > '' ? collect( request()->xml(true)) : collect([]);
            $kamarData->format = 'xml';
        } else {
            throw new Exception("Invalid content");
        }
        return $kamarData;
    }

    public static function fromFile($filename, $useBasePath = true)
    {
        $kamarData = new static;
        if ($useBasePath) {
            $kamarData->data = collect(json_decode(Storage::disk('local')->get('data/' . $filename), true));
        } else {
            $kamarData->data = collect(json_decode(file_get_contents($filename), true));
        }
        return $kamarData;
    }
}
