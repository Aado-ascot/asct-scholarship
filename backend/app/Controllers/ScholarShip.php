<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AuthModel;
use App\Models\ScholarshipModel;
use \Firebase\JWT\JWT;

class ScholarShip extends BaseController
{
    public function __construct(){
        //Models
        $this->authModel = new AuthModel();
        $this->scholarModel = new ScholarshipModel();
    }

    public function createNewScholarship(){
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if(!$authorization){
            $response = [
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            exit();
        }

        //Get API Request Data
        $payload = $this->request->getJSON();
        $payload = json_decode(json_encode($payload), true);
        $payload['qualification'] = json_encode($payload['qualification']);
        $payload['requirements'] = json_encode($payload['requirements']);
        
        //INSERT QUERY TO APPLICATION
        $query = $this->scholarModel->insert($payload);

        if($query){
            $response = [
                'title' => 'Scholarship Program',
                'message' => 'Your program has been created.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function getList(){
        // Check Auth header bearer
        // $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        // if(!$authorization){
        //     $response = [
        //         'message' => 'Unauthorized Access'
        //     ];

        //     return $this->response
        //             ->setStatusCode(401)
        //             ->setContentType('application/json')
        //             ->setBody(json_encode($response));
        //     exit();
        // }

        
        $where = [
            'status'=> 1,
        ];
        $list = [];

        $query = $this->scholarModel->getScholarshipList($where);
        // print_r($query);
        // exit();
        foreach ($query as $key => $value) {
            $cinfo = $value['creator'];
            $value['qualification'] = json_decode($value['qualification']);
            $value['requirements'] = json_decode($value['requirements']);

            $list['list'][$key] = [
                "key" => $value['id'],
                "title" => $value['title'],
                "provider" => $value['provider'],
                "slotAvailable" => $value['slot'],
                "dueDate" => $value['dueDate'],
                "creator" => $cinfo->firstName .' '. $cinfo->middleName .' '. $cinfo->lastName .' '. $cinfo->suffix,
                "original" =>  $value,
            ];
        }
        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'error' => 404,
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    

}