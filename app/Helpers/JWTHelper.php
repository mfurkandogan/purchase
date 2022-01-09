<?php

namespace App\Helpers;

use Firebase\JWT\JWT;

class JWTHelper
{
    const JWT_SECRET = "NdedxQFxCMrBgVPXqLKRK5gcSDw9FWDY";

    /**
     * @param string $uid
     * @param string $appId
     * @param string $language
     * @param string $os
     * @return string
     */
    public static function createJwt(string $uid, string $appId, string $language,string $os) : string {

        $payload = array(
            "uid" => $uid,
            "appId" => $appId,
            "language" =>$language,
            "os" =>$os,

        );

        return JWT::encode($payload, self::JWT_SECRET, 'HS256');
    }


    /**
     * @param $token
     * @return object
     */
    public static function decodeJwt($token) : object {

       return JWT::decode($token,self::JWT_SECRET, ['HS256']);

    }


}
