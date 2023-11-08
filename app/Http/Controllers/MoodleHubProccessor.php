<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EncryptCookies;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Termwind\Components\Dd;

class MoodleHubProccessor extends Controller
{
    private string $token = '8a0df214ee7b7da5d4393761e3ba42f2';

    private string $token_auth = 'cd172edac6745476d9f780084bc419b0';
    private string $moddleEndpoint = 'https://lms.kursusonline.com/webservice/rest/server.php';
    private  string $login = 'https://lms.kursusonline.com/login/token.php';

    /**
     * Get All Course & Curiculum
     * @throws GuzzleException
     */
    public function getCourse() {
        $client = new Client();

        // bind data
        $postField = [
            'wstoken' => $this->token,
            'wsfunction' => 'core_course_get_courses',
            'moodlewsrestformat' => 'json',
        ];

        //send request
        $response = $client->post($this->moddleEndpoint, [
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
     *Get Course By ID
     * @throws GuzzleException
     */
    public function getCourseById($id) {
        $client = new Client();

        // bind data
        $postField = [
            'wstoken' => $this->token,
            'wsfunction' => 'core_course_get_contents',
            'courseid' => $id,
            'moodlewsrestformat' => 'json',
        ];

        //send request
        $response = $client->post($this->moddleEndpoint, [
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
     * Create a new Moodle user account.
     * @throws GuzzleException|Exception
     */
    public function createUser(Request $request) {
        $client = new Client();

        // Bind data
        $user = [
            "username" => $request->username,
            "password" => $request->password,
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "email" => $request->email,
            "auth" => 'manual', // Change the authentication method if necessary
            // Add other custom fields if needed
        ];

        $users = [$user];
        $param = ["users" => $users];

        try {
            $response = $client->post($this->moddleEndpoint . '?wstoken=' . $this->token_auth . '&wsfunction=core_user_create_users&moodlewsrestformat=json', [
                'form_params' => $param,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                throw new Exception('Request failed with code: ' . $statusCode);
            }

            $jsonResponse = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to parse JSON');
            }

            // Return the JSON response
            return $jsonResponse;
        } catch (GuzzleException | Exception $e) {
            throw new Exception('Error: ' . $e->getMessage());
        }

    }

    /**
     * Authenticate a user in Moodle.
     * @throws Exception
     */
    public function login(Request $request) {
        $client = new Client();

        // Assuming $username and $password are obtained from the login form
        $username = $request->username;
        $password = $request->password;

        try {
            // First, attempt to authenticate the user
            $response = $client->post($this->login . '?wstoken=' . $this->token_auth . '&service=moodle_mobile_app&moodlewsrestformat=json', [
                'form_params' => [
                    'username' => $username,
                    'password' => $password
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);



            $jsonResponse = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to parse JSON');
            }

            return $jsonResponse;


        } catch (GuzzleException | Exception $e) {
            throw new Exception('Error: ' . $e->getMessage());
        }
    }


    /**
     * Enroll a user in a course.
     * @throws GuzzleException|Exception
     */
    public function enrollUserInCourse(Request $request)
    {
        $client = new Client();

        // Bind data
        $enrollment = [
            "enrolments" => [
                [
                    "roleid" => $request->roleId,
                    "userid" => $request->userId,
                    "courseid" => $request->courseId
                ]
            ]
        ];

        try {
            $response = $client->post($this->moddleEndpoint . '?wstoken=' . $this->token_auth . '&wsfunction=enrol_manual_enrol_users&moodlewsrestformat=json', [
                'form_params' => $enrollment,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                throw new Exception('Request failed with code: ' . $statusCode);
            }

            $jsonResponse = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to parse JSON');
            }

            // Return the JSON response
            return $jsonResponse;
        } catch (GuzzleException|Exception $e) {
            throw new Exception('Error: ' . $e->getMessage());
        }
    }

    /**
     * Get all enrollment methods.
     * @throws GuzzleException|Exception
     */
    public function getAllEnrollmentMethods() {
        $client = new Client();

        // Bind data
        $postField = [
            'wstoken' => $this->token_auth,
            'wsfunction' => 'core_enrol_get_enrolment_methods',
            'moodlewsrestformat' => 'json',
        ];

        try {
            $response = $client->post($this->moddleEndpoint, [
                'form_params' => $postField
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                throw new Exception('Request failed with code: ' . $statusCode);
            }

            $jsonResponse = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to parse JSON');
            }

            // Return the JSON response
            return $jsonResponse;
        } catch (GuzzleException | Exception $e) {
            throw new Exception('Error: ' . $e->getMessage());
        }
    }

}
