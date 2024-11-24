<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AuthModel;
use App\Models\ScholarshipModel;
use App\Models\MiscModel;
use \Firebase\JWT\JWT;

class ScholarShip extends BaseController
{
    public function __construct(){
        //Models
        $this->authModel = new AuthModel();
        $this->scholarModel = new ScholarshipModel();
        $this->miscModel = new MiscModel();
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

    public function submitApplication(){
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
        $payload['familyBackground'] = json_encode($payload['familyBackground']);
        
        //INSERT QUERY TO APPLICATION
        $query = $this->scholarModel->submitApplication($payload);

        if($query){
            $where = [
                'id' => $payload['scholarId']
            ];
            $update = $this->scholarModel->updateScholarshipSlot($where);
            $response = [
                'title' => 'Apply Scholarship',
                'message' => 'Your application has been submitted.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function updateScholarshipStatus(){
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
        $data = $this->request->getJSON();
        $payload = json_decode(json_encode($data->updateDetails), true);
        
        
        //INSERT QUERY TO APPLICATION
        $query = $this->scholarModel->updateApplication(["id"=>$data->appId], $payload);

        if($query){
            if($data->actionType === "approve"){
                // execute the Rejection of other submitted application
            } else if($data->actionType === "reject"){
                // Update scholarship
                $where = [
                    'id' => $data->scholarId
                ];
                $update = $this->scholarModel->updateMinusScholarshipSlot($where);

                // Send Notification
                $notif = json_decode(json_encode($data->notification), true);
                $this->miscModel->sendNotification($notif);
            }

            $response = [
                'title' => 'Evaluation Complete',
                'message' => 'This application has been moved to the next step'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function getListUserApplied(){
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
        
        $payload = $this->request->getJSON();
        
        $where = [
            'studentId'=> $payload->sId,
        ];
        $list = [];

        $query = $this->scholarModel->getScholarshipByUser($where);
        // print_r($query);
        // exit();
        foreach ($query as $key => $value) {
            $sinfo = $value['scholarship'];

            $list['list'][$key] = [
                "key" => $value['id'],
                "title" => $sinfo->title,
                "provider" => $sinfo->provider,
                "status" => $value['status'],
                "remarks" => $value['remarks'],
                "data" => $value,
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

    public function getListUserApplications(){
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
        
        $payload = $this->request->getJSON();


        $where = [
            'appStatus'=> $payload->uType === 3 ? 0 : 1,
        ];
        $list = [];

        $query = $this->scholarModel->getAppliedScholarships($where);
        // print_r($query);
        // exit();
        foreach ($query as $key => $value) {
            $sinfo = $value['scholarship'];
            $sinfo->requirements = json_decode($sinfo->requirements);
            $sinfo->qualification = json_decode($sinfo->qualification);
            $value['familyBackground'] = json_decode($value['familyBackground']);

            $stdinfo = $value['student'];

            $list['list'][$key] = [
                "key" => $value['id'],
                "studentNumber" => $stdinfo->username,
                "studentName" => $stdinfo->firstName ." ". $stdinfo->middleName ." ". $stdinfo->lastName ." ". $stdinfo->suffix,
                "title" => $sinfo->title,
                "provider" => $sinfo->provider,
                "status" => $value['status'],
                "remarks" => $value['remarks'],
                "data" => $value,
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

    public function getListApproveApplications(){
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

        $where = [
            'appStatus'=> 2,
        ];
        $list = [];

        $query = $this->scholarModel->getAppliedScholarships($where);
        // print_r($query);
        // exit();
        foreach ($query as $key => $value) {
            $sinfo = $value['scholarship'];
            $sinfo->requirements = json_decode($sinfo->requirements);
            $sinfo->qualification = json_decode($sinfo->qualification);
            $value['familyBackground'] = json_decode($value['familyBackground']);

            $stdinfo = $value['student'];

            $list['list'][$key] = [
                "key" => $value['id'],
                "studentNumber" => $stdinfo->username,
                "studentName" => $stdinfo->firstName ." ". $stdinfo->middleName ." ". $stdinfo->lastName ." ". $stdinfo->suffix,
                "title" => $sinfo->title,
                "provider" => $sinfo->provider,
                "status" => $value['status'],
                "remarks" => $value['remarks'],
                "data" => $value,
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