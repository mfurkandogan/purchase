<?php


namespace App\Validators;


use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceValitador
{
    /**
     * @param Request $request
     * @return array
     */
    public static function register(Request $request) : array {

        $validator = Validator::make($request->all(),
            [
                'uid' => 'required|string|min:1',
                'appId' => 'required|string|min:1',
                'language' => 'required|string|min:1',
                'os' => 'required|string|min:1'
            ]);

        if ($validator->fails()) {
            return ["isValid" =>false, "errorMessage" => $validator->errors()];
        }

        $device = Device::join('subscriptions','devices.id','=','subscriptions.device_id')
            ->where('devices.uid',$request->uid)
            ->where('subscriptions.appId',$request->appId)
            ->first();

        if($device){
            return ["isValid" =>false, "errorMessage" => "This app was registered"];
        }

        return ["isValid" =>true, "errorMessage" => ""];
    }
}
