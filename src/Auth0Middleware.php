<?php
declare(strict_types=1);

namespace Akmeh;

use Auth0\SDK\Exception\InvalidTokenException;
use Illuminate\Http\Request;
use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\JWTVerifier;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


/**
 * Class Auth0Middleware
 * @package Akmeh
 */
class Auth0Middleware
{
    /**
     * Validate the access token sent in the headers
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (!$request->headers->has('Authorization')) {
            return $this->unauthorized('No token provided.');
        }

        try {
            $user = $this->getUser($request);
            if (array_key_exists('email', $user)) {
                $params = $request->request->all();
                $params['X-Email'] = $user['email'];
                $request->request->add($params);
            }
        } catch (\Exception $e) {
            return $this->unauthorized($e->getMessage());
        }
        return $next($request);

    }


    /**
     * @param Request $request
     * @return array
     * @throws CoreException
     * @throws \Exception
     */
    private function getUser(Request $request): array
    {

        $authorizationHeader = str_replace('bearer ', '', $request->headers->get('Authorization'));
        $token = str_replace('Bearer ', '', $authorizationHeader);
        return $this->authorize($token);

    }


    /**
     * @param string $token
     * @return mixed
     * @throws \Exception
     */
    private function authorize(string $token): array
    {

        try {
            $verifier = new JWTVerifier([
                'supported_algs' => ['RS256'],
                'valid_audiences' => [env('AUTH0_AUTH_AUDIENCE')],
                'authorized_iss' => ['https://' . env('AUTH0_DOMAIN') . '/'],
                'client_secret' => env('AUTH0_CLIENT_SECRET'),
                'cache' => new Auth0Cache(),
            ]);

            return (array)$verifier->verifyAndDecode($token);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    private function unauthorized(string $message): JsonResponse
    {
        return new JsonResponse(['message' => $message], Response::HTTP_UNAUTHORIZED);
    }

}
