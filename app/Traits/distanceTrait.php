<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Carbon\Carbon;


trait distanceTrait
{

 public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
             return 0;
         }
        $theta = $lon1 - $lon2;
        $dist = sin($this->degreeToRadins($lat1)) * sin($this->degreeToRadins($lat2)) +  cos($this->degreeToRadins($lat1)) * cos($this->degreeToRadins($lat2)) * cos($this->degreeToRadins($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function degreeToRadins($data)
    {
        $Radians = $data*(M_PI/180);
        return $Radians;
    }

}
