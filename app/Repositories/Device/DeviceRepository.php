<?php

namespace App\Repositories\Device;

use App\Models\Device;
use App\Models\Subscription;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DeviceRepository extends BaseRepository implements IDeviceRepository
{
    public function getDeviceByUId($uid)
    {
        return Device::where('uid',$uid)->first();
    }

    /**
     * @param object|array $data
     * @return false|mixed
     */
    public function hasManyCreate($data)
    {

        DB::beginTransaction();

        if(!is_array($data)){
            $data = $data->toArray();
        }



        try {
            $device = $this->getDeviceByUId($data['uid']);
            if($device){
                $createdItem = $device;
            } else {
                $createdItem = $this->model->create($data);
            }

            if($createdItem){
                $subscriptionItem = new Subscription();
                $subscriptionItem->appId=$data['appId'];
                $subscriptionItem->language=$data['language'];
                $createdItem->subscriptions()->save($subscriptionItem);
            }

            DB::commit();
            return $createdItem->id;

        } catch (\Exception $exception) {
            DB::rollback();
            return $exception->getMessage();

        }
    }

    public function isActive($data){
        return Device::join('subscriptions','devices.id','=','subscriptions.device_id')
            ->where('devices.uid',$data->uid)
            ->where('subscriptions.appId',$data->appId)
            ->first();
    }

    public function subscriptionUpdate($id,$appId,$data){

        $row = $this->model->find($id);
        if (!$row) {
            return false;
        }
        $subscription = $row->subscriptions()->where('appId',$appId)->first();
        if (!$subscription) {
            return false;
        }
        DB::beginTransaction();
        try {
            $subscription->fill($data)->save();
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollback();
            return false;
        }
    }
}
