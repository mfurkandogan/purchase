<?php

namespace App\Http\Middleware;

use App\Helpers\JWTHelper;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $jwtToken = $request->bearerToken();

        try {
            $decodedToken = JWTHelper::decodeJwt($jwtToken);
        } catch (ExpiredException $exception) {
            return new Response(null, 401,
                ["WWW-Authenticate" => 'Bearer error="invalid_token", error_description="The token is expired"']
            );
        } catch (SignatureInvalidException $exception) {

            return new Response(null, 401,
                ["WWW-Authenticate" => 'Bearer error="invalid_token", error_description="The signature is invalid"']
            );
        } catch (\Exception $exception) {

            return new Response(null, 401,
                ["WWW-Authenticate" => 'Bearer']
            );
        }

        $request->tokenInfo = $decodedToken;

        return $next($request);
    }
}
