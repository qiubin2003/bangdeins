<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;

class Books extends REST_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    /**
     * 获取邦德TOKEN
     */
    public function users_get()
    {
        $appid = 'cxn1812202131290et';
        $appSecrectKey = '1rTG1JCyaDKXQNaMGxaz7ghHxI6kB7Fc7cVqu1CJIkM=';

        $this->load->library(
            'bandins/bandins',
            ['appid' => $appid, 'appSecrectKey' => $appSecrectKey],
            'Bandins'
        );

        //获取TOKEN
        $body = [
            'userName' => '18611125903',
            'password' => '18611125903'
        ];

        $res = $this->Bandins->exec('token', [], $body, 'post');
        $res = json_decode($res);
        print_r($res);
        die();

        $this->load->library(
            'bandins/bandins',
            ['appid' => $appid, 'appSecrectKey' => $appSecrectKey, 'Authorization' => 'hvmZiiyOrF4gP1jvgdEMVYWjb3jq283rwArywhMzhedYqZbcWGkqzjO4UJ0CSd1CUozzsiiYh/AKu5uK9X9WwALMqIwKO6P/PM4EGoVvs1BmOt/HWlpWNr0RL5gteBhj5Koh+qzbmv8Z9sDxn7LIJS/k4g8j0R/pUhOCTcOYv/E+84cDm1el1UyAPX92aB7SpWfh1sQB/+uMNExe5FXZ0JwrMMYuJDRMgTch9v/GrGA='],
            'Bandins'
        );

        $query = [
            'offset' => 0,
            'limit' => 10,
            'model.policyNo' => 'AA12345',
            'model.plateNo' => '湘F20998',
            'model.vin' => '123456',
            'model.startDate' => '2018-08-22',
            'model.endDate' => '2018-12-23'
        ];

        $res = $this->Bandins->exec('policies', $query, []);
        $res = json_decode($res);

        print_r($res);
        die();


//
//        $bodyStr = [];
//        foreach ($data as $k => $v){
//            $bodyStr[] = $k . $v;
//        }
//        sort($bodyStr);
//
//        $bodyStr = implode('', $bodyStr);
//        $vvv = md5($appid . $bodyStr . '');
//
//        $headers = [
//            'x-appid' => $appid
//        ];
//
//        $options = [];
//        $request = Requests::get(
//            'http://apis.bandins.com/vi/policies?' . $queryStr . '&securitySign=' . $vvv,
//            $headers,
//            $options
//        );

        //-----------------------------------
        $url = 'http://apis.bandins.com/vi/token?securitySign=%s';

        $headers = [
            'x-appid' => $appid,
            'Accept' => 'application/json'
        ];
        $token = [
            'userName' => '18611125903',
            'password' => '18611125903'
        ];
        $token = json_encode($token);
        $token = base64_encode($token);

        $sMsgEncrypt = base64_encode($aes->encrypt($token));
        $securitySign = md5($appid . '' . $sMsgEncrypt);
        $url = sprintf($url, $securitySign);

        $options = [];
        $request = Requests::post(
            $url,
            $headers,
            $sMsgEncrypt,
            $options
        );

        $data = $aes->decrypt(base64_decode($request->body));
        print_r($data);
        die();


        // Users from a data store e.g. database
        $users = [
            ['id' => 1, 'name' => '邱斌', 'email' => 'john@example.com', 'fact' => 'Loves coding'],
            ['id' => 2, 'name' => 'Jim', 'email' => 'jim@example.com', 'fact' => 'Developed on CodeIgniter'],
            ['id' => 3, 'name' => 'Jane', 'email' => 'jane@example.com', 'fact' => 'Lives in the USA', ['hobbies' => ['guitar', 'cycling']]],
        ];

        $id = $this->get('id');

        // If the id parameter doesn't exist return all the users

        if ($id === NULL) {
            // Check if the users data store contains users (in case the database result returns NULL)
            if ($users) {
                // Set the response and exit
                $this->response($users, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No users were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        } // Find and return a single record for a particular user.
        else {
            $id = (int)$id;

            // Validate the id.
            if ($id <= 0) {
                // Invalid id, set the response and exit.
                $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }

            // Get the user from the array, using the id as key for retrieval.
            // Usually a model is to be used for this.

            $user = NULL;

            if (!empty($users)) {
                foreach ($users as $key => $value) {
                    if (isset($value['id']) && $value['id'] === $id) {
                        $user = $value;
                    }
                }
            }

            if (!empty($user)) {
                $this->set_response($user, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'User could not be found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }

    public function users_post()
    {
        // $this->some_model->update_user( ... );
        $message = [
            'id' => 100, // Automatically generated by the model
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'message' => 'Added a resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function users_delete()
    {
        $id = (int)$this->get('id');

        // Validate the id.
        if ($id <= 10) {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }
}
