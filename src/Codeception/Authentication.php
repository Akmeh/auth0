<?php
declare(strict_types=1);

namespace Akmeh\Codeception;

use \Auth0\SDK\API\Authentication as Auth0Authnetication;
use \Auth0\SDK\Exception\ApiException;
use \GuzzleHttp\Exception\ClientException;



/**
 * Trait Authentication
 *
 * For testing propose class in order to get Access Token.
 * This require has client credentials active and this can only be able for testing applications
 */
trait Authentication
{

    /**
     * Try to login in Auth0 using the configuration in the env file
     *
     * If the login succeed the access token will be returned
     * @return string $Id
     */
    public function getAccessToken()
    {

        $auth0_api = new Auth0Authnetication(env('AUTH0_DOMAIN'));

        $config = [
            // Required for a Client Credentials grant.
            // Application must allow this grant type and be authorized for the API requested
            'client_secret' => env('AUTH0_CLIENT_SECRET'),
            'client_id' => env('AUTH0_CLIENT_ID'),

            // Also required, found in the API settings page.
            'audience' => env('AUTH0_AUTH_AUDIENCE'),
        ];

        try {
            $result = $auth0_api->client_credentials($config);
            return $result['access_token'];
        } catch (ClientException $e) {
            echo 'Caught: ClientException - ' . $e->getMessage();
        } catch (ApiException $e) {
            echo 'Caught: ApiException - ' . $e->getMessage();
        }

    }
}



