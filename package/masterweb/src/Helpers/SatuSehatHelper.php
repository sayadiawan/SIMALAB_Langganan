<?php

namespace Smt\Masterweb\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class SatuSehatHelper
{

    protected $client;
    protected $baseUri;
    protected $authUrl;
    protected $clientId;
    protected $clientSecret;
    protected $orgId;
    protected $token;

    public function __construct()
    {
      $this->baseUri = config('services.satu_sehat.base_uri');
      $this->authUrl = config('services.satu_sehat.auth_url');
      $this->clientId = config('services.satu_sehat.client_id');
      $this->clientSecret = config('services.satu_sehat.client_secret');
      $this->orgId = config('services.satu_sehat.org_id');

      $this->client = new Client([
        'base_uri' => $this->baseUri,
      ]);

      $this->token = $this->authenticate();
    }

  protected function authenticate()
  {
    try {
      $authUrl = rtrim($this->authUrl, '/') . '/accesstoken';
      $queryParams = http_build_query([
        'grant_type' => 'client_credentials',
      ]);

      $response = $this->client->request('POST', $authUrl . '?' . $queryParams, [
        'form_params' => [
          'client_id' => $this->clientId,
          'client_secret' => $this->clientSecret,
        ],
      ]);

      $body = json_decode($response->getBody(), true);

      if (isset($body['access_token'])) {
        return $body['access_token'];
      }

      return null;
    } catch (RequestException $e) {
      return null;
    }
  }


  public function get($endpoint, $queryParams = [])
  {
    try {
      $response = $this->client->request('GET', $endpoint, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json',
          'X-Organization-ID' => $this->orgId,
        ],
        'query' => $queryParams,
      ]);

      return [
        'status_code' => $response->getStatusCode(),
        'body' => json_decode($response->getBody(), true)
      ];
    } catch (RequestException $e) {
      return $this->handleException($e);
    }
  }

  public function post($endpoint, $data = [])
  {
    try {
      $response = $this->client->post(  env('BASE_URL_SATUSEHAT').$endpoint, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json',
          'X-Organization-ID' => $this->orgId,
        ],
        'json' => $data,
      ]);

      return [
        'status_code' => $response->getStatusCode(),
        'body' => json_decode($response->getBody(), true)
      ];
    } catch (RequestException $e) {
      return $this->handleException($e);
    }
  }

  public function put($endpoint, $id, $data = [])
  {
    try {
      $response = $this->client->put(env('BASE_URL_SATUSEHAT') . $endpoint."/".$id, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json',
          'X-Organization-ID' => $this->orgId,
        ],
        'json' => $data,
      ]);

      return [
        'status_code' => $response->getStatusCode(),
        'body' => json_decode($response->getBody(), true)
      ];
    } catch (RequestException $e) {
      return $this->handleException($e);
    }
  }

  private function handleException(RequestException $e)
  {
    if ($e->hasResponse()) {
      $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
      return [
        'status_code' => $statusCode,
        'body' => json_decode($e->getResponse()->getBody()->getContents(), true)
      ];
    }

    return ['error' => 'An error occurred'];
  }
}
