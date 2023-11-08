<?php

namespace App\Http\Controllers;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Psr\Http\Message\StreamInterface;

class ProcessHubController extends Controller
{
    private string $endpoint = "https://portal.kursusonline.com/includes/api.php";
    private string $username = "gBa2QEO6z0iHLlaKwtz5QUtGW13Gjh1T";
    private string $password = "gk0WErM6OG5K9LIvhzXD20IDUXY69qem";

    /**
     * Main Logic for POST Request HUB
     * @throws GuzzleException
     */
    public function process(Request $request, $param): JsonResponse|int|string|null
    {

        if ($param === 'register'){
            // validate request
            $validator = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required|email',
                'address1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'postcode' => 'required',
                'country' => 'required',
                'phonenumber' => 'required',
                'password2' => 'required',
                'clientip' => 'required',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }
            return $this->addClient(
                $request->firstname,
                $request->lastname,
                $request->email,
                $request->address1,
                $request->city,
                $request->state,
                $request->postcode,
                $request->country,
                $request->phonenumber,
                $request->password,
                $request->clientip
            );
        } elseif ($param === 'login') {
            // validate request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }
            return $this->login($request->email, $request->password);

        } elseif ($param === 'invoice') {
            // validate request
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'paymentmethod' => 'required',
                'date' => 'required|date',
                'duedate' => 'required|date',
                'itemdescription1' => 'required',
                'itemamount1' => 'required',
                'itemtaxed1' => 'required',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }
            return $this->purchase(
                $request->userid,
                $request->paymentmethod,
                $request->date,
                $request->duedate,
                $request->itemdescription1,
                $request->itemamount1,
                $request->itemtaxed1,
            );
        } elseif ($param === 'add-product') {
            // validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'shortdescription' => 'required',
                'pricing' => 'required',
                'topics' => 'required|array',
                'overview' => 'required|string',
                'facilitator' => 'required|string',
                'review' => 'required|string',
            ]);


            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }
            return $this->addProduct(
                $request->name,
                $request->description,
                $request->shortdescription,
                $request->pricing,
                $request->topics,
                $request->overview,
                $request->facilitator,
                $request->review
            );
        }

        return 0;
    }

    /**
     * Add new CLient Register
     * @throws GuzzleException
     */
    private function addClient(string $firstname, string $lastname, string $email, string $address1, string  $city, string $state, $postcode, string $country, $phonenumber, $password, $clientip): string
    {
        $client = new Client();

        $clientData = [
            'action' => 'AddClient',
            'username' => $this->username,
            'password' => $this->password,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'address1' => $address1,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country,
            'phonenumber' => $phonenumber,
            'password2' => $password,
            'clientip' => $clientip,
            'responsetype' => 'json',
        ];

        $response = $client->post($this->endpoint, [
            'form_params' => $clientData
        ]);


        // Get the response body
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Failed to parse JSON');
        }

        // Return the JSON response
        return $jsonResponse;
    }


    /**
     * Login System
     * @throws GuzzleException
     */
    private function login(string $email, string $password2) {
        //create instance
        $client = new Client();

        // bind data
        $postField = [
            'username' => $this->username,
            'password' => $this->password,
            'email' => $email,
            'password2' => $password2,
            'action' => 'ValidateLogin',
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
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Failed to parse JSON');
        }

        // Return the JSON response
        return $jsonResponse;
    }

    /**
     * Make Invoice
     * @throws GuzzleException
     */
    private function purchase($userid, $paymentmethod, $date, $duedate, $itemdescription1, $itemamount1, $itemtaxed1) {

        //create instance
        $client = new Client();

        // bind data
        $postField = [
            'identifier' => $this->username,
            'secret' => $this->password,
            'action' => 'CreateInvoice',
            'userid' => $userid,
            'status' => 'Paid',
            'sendinvoice' => '1',
            'paymentmethod' => $paymentmethod,
            'taxrate' => '10.00',
            'date' => $date,
            'duedate' => $duedate,
            'itemdescription1' => $itemdescription1,
            'itemamount1' => $itemamount1,
            'itemtaxed1' => $itemtaxed1,
            'notes' => 'Terima Kasih telah melakukan pembelian kursus-online',
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
     * Add Product
     * @throws GuzzleException
     */
    private function addProduct( $name, $description, $shortdescription, $pricing, $topics, $overview, $facilitator, $review) {
        //create instance
        $client = new Client();

        // bind data
        $postField = [
            'username' => $this->username,
            'password' => $this->password,
            'action' => 'AddProduct',
            'gid' => '1',
            'name' => $name,
            'welcomeemail' => '5',
            'description' => $description,
            'shortdescription' => $shortdescription,
            'pricing' => $pricing,
            'paytype' => 'free',
            'configoption1' => $overview,
            'configoption2' => $facilitator,
            'configoption3' => $review,
            'configoption4' => $topics,
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
     * Get Product By id
     * @throws GuzzleException
     */
    public function getProductById($id){
        //create instance
        $client = new Client();

        // bind data
        $postField = [
            'action' => 'GetProducts',
            'username' => $this->username,
            'password' => $this->password,
            'pid' => $id,
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
