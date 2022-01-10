<?php

namespace App\Http\Controllers;

use App\Models\Response\HttpErrorResponse;
use App\Models\Response\HttpSuccessResponse;
use App\Repositories\Device\IDeviceRepository;
use App\Repositories\Order\IOrderRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class OrderController extends Controller
{

    /**
     * @var IOrderRepository
     */
    protected IOrderRepository $repository;
    /**
     * @var IDeviceRepository
     */
    protected IDeviceRepository $deviceRepository;

    /**
     * @param IOrderRepository $repository
     * @param IDeviceRepository $deviceRepository
     */
    public function __construct(IOrderRepository $repository, IDeviceRepository $deviceRepository)
    {
        $this->repository = $repository;
        $this->deviceRepository = $deviceRepository;
    }

    public function createOrder(Request $request)
    {
        if(!isset($request->receipt)){
            $response = (new HttpErrorResponse())->setMessage(["Receipt not found!"]);

            return new Response($response->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $req = Request::create('/api/v1/platform/' . $request->tokenInfo->os, 'POST', ['receipt' => $request->receipt]);
        $res = Route::dispatch($req);
        $result = json_decode($res->getContent());

        if ($result->message->status == false) {

            $response = (new HttpSuccessResponse())->setMessage(['status' => false]);

            return new Response($response->toArray(), Response::HTTP_OK);

        } elseif ($result->message->status == true) {

            $id = $this->deviceRepository->getDeviceByUId($request->tokenInfo->uid)->id;

            $parsedExpireDate = Carbon::parse($result->message->expire_date);

            $expireDate = $parsedExpireDate->format('Y-m-d H:i:s');

            $this->deviceRepository->subscriptionUpdate($id,$request->tokenInfo->appId,['expire_date' => $expireDate,'receipt'=>$request->receipt]);

            $response = (new HttpSuccessResponse())
                ->setMessage([
                    'status' => true,
                    'expire_date' => $expireDate
                ]);

            return new Response($response->toArray(), Response::HTTP_OK);
        } else {
            $response = (new HttpErrorResponse())->setMessage(["An error occurred"]);

            return new Response($response->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkOrder($os, Request $request)
    {
        //TODO $os switch
        if (isset($request->receipt)) {
            if (substr($request->receipt, -1) % 2 == 1) {
                $date = Carbon::now('-6')->add(1, 'month');
                $response = (new HttpSuccessResponse())
                    ->setMessage([
                        'status' => true,
                        'expire_date' => $date
                    ]);

                return new Response($response->toArray(), Response::HTTP_OK);
            } else {
                $response = (new HttpErrorResponse())->setMessage(['status' => false]);

                return new Response($response->toArray(), Response::HTTP_NOT_FOUND);
            }
        }

        $response = (new HttpErrorResponse())->setMessage([" Receipt Not Found!"]);

        return new Response($response->toArray(), Response::HTTP_NOT_FOUND);
    }
}
