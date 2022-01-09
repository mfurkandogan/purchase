<?php

namespace App\Http\Controllers;

use App\Helpers\JWTHelper;
use App\Models\Response\HttpErrorResponse;
use App\Models\Response\HttpSuccessResponse;
use App\Repositories\Device\IDeviceRepository;
use App\Validators\DeviceValitador;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeviceController extends Controller
{
    /**
     * @var IDeviceRepository
     */
    protected  IDeviceRepository $repository;

    /**
     * AuthController constructor.
     * @param IDeviceRepository $repository
     */
    public function __construct(IDeviceRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function register(Request $request) : Response {
        $validation = DeviceValitador::register($request);
        if (!$validation["isValid"]) {
            $response = (new HttpErrorResponse())->setMessage([$validation["errorMessage"]]);

            return new Response($response->toArray(),Response::HTTP_BAD_REQUEST);
        }

        if($this->repository->hasManyCreate($request)){
            $jwt = JWTHelper::createJwt($request->uid,$request->appId,$request->language,$request->os);

            $response = (new HttpSuccessResponse())
                ->setMessage([ 'client_token' => $jwt]);

            return new Response($response->toArray(),Response::HTTP_CREATED);
        }

        $response = (new HttpErrorResponse())
            ->setMessage(["Register Failed!"]);

        return new Response($response->toArray(),Response::HTTP_UNAUTHORIZED);

    }

    public function checkSubscription(Request $request) : Response
    {
        $device = $this->repository->isActive($request->tokenInfo);


        if($device && $device->is_ended == 0){
            $response = (new HttpSuccessResponse())
                ->setMessage([
                    "status" => true
                ]);

            return new Response($response->toArray(),Response::HTTP_OK);

        } else {
            $response = (new HttpErrorResponse())
                ->setMessage([
                    "status" => false
                ]);

            return new Response($response->toArray(),Response::HTTP_EXPECTATION_FAILED);
        }
    }
}
