<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class GetDetailsProcess extends Controller
{
    private string $endpoint = "https://portal.kursusonline.com/includes/api.php";
    private string $username = "gBa2QEO6z0iHLlaKwtz5QUtGW13Gjh1T";
    private string $password = "gk0WErM6OG5K9LIvhzXD20IDUXY69qem";

    /**
     * Get all Product
     * @throws GuzzleException
     */
    public function getProducts() {
        //create instance
        $client = new Client();

        // bind data
        $postField = [
            'identifier' => $this->username,
            'secret' => $this->password,
            'action' => 'GetProducts',
            'responsetype' => 'json',
        ];

        //send request
        $response = $client->post($this->endpoint, [
            'form_params' => $postField
        ]);

        // Check for any errors
        if ($response->getStatusCode() !== 200) {
            die('Unable to connect: ' . $response->getStatusCode() . ' - ' . $response->getReasonPhrase());
        }

        // Get the response body
        // Get the response body
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Failed to parse JSON');
        }

        // Return the JSON response
        return $jsonResponse;
    }

    /**
     * Get all clients
     * @throws GuzzleException
     */
    public function getClient() {
        //create instance
        $client = new Client();

        // bind data
        $postField = [
            'identifier' => $this->username,
            'secret' => $this->password,
            'action' => 'GetClients',
            'responsetype' => 'json',
        ];

        //send request
        $response = $client->post($this->endpoint, [
            'form_params' => $postField
        ]);

        // Check for any errors
        if ($response->getStatusCode() !== 200) {
            die('Unable to connect: ' . $response->getStatusCode() . ' - ' . $response->getReasonPhrase());
        }

        // Get the response body
        // Get the response body
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Failed to parse JSON');
        }

        // Return the JSON response
        return $jsonResponse;
    }


}
