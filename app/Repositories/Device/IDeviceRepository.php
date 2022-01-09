<?php

namespace App\Repositories\Device;

use App\Repositories\IRepository;

interface IDeviceRepository extends IRepository
{
    public function getDeviceByUId(string $uid);

    public function hasManyCreate(object $data);

    public function isActive(object $data);

    public function subscriptionUpdate(int $id,string $appId,array $data);
}
