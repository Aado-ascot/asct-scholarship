<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\UsersModel;
use App\Models\AuthModel;
use App\Models\MiscModel;
use \Firebase\JWT\JWT;

class Users extends BaseController
{
    public function __construct(){
        //Models
        $this->userModel = new UsersModel();
        $this->authModel = new AuthModel();
        $this->miscModel = new MiscModel();
    }

    public function getUserDetails(){
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

        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 

        //Select Query for finding User Information
        $user = $this->authModel->where(['id' => $data->id])->get()->getRow();
        
        //Set Api Response return to the FE
        if($user){

            if($user->status == 1){
                unset($user->password);
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($user));
            } else {
                $response = [
                    'title' => 'Account Deactivated',
                    'message' => 'Please contact your adminitrator for more information'
                ];
    
                return $this->response
                        ->setStatusCode(404)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }
            
        } else {
            $response = [
                'title' => 'Please contact admin',
                'message' => 'Please check your data.'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }


        // print_r(json_encode($data));
        
    }

    public function registerUser(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        $data->password = sha1($data->password);
        
        $query = $this->userModel->insert($data);

        if($query){

            $response = [
                'title' => 'Registration Complete',
                'message' => 'User data has been successfully submitted.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'title' => 'Registration Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        // print_r($data);
        // exit();
        
    }

    public function ChangePassword(){
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

        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();
        $payload->newPassword = sha1($payload->newPassword);

        $where = ['id' => $payload->id];
        $updateData = ['password' => $payload->newPassword];

        $updatePassword =  $this->userModel->updatePassword($where, $updateData);

        if($updatePassword){
            $response = [
                'title' => 'Change Password',
                'message' => 'Your successfully change password.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Change Password Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }
    
    public function getAllUserList(){
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

        // $header = $this->request->getHeader("");
        
        $where = [
            'status'=> 1
        ];
        $list = [];
        // $list['list'] = $this->userModel->getAllUserInfo($where);

        $query = $this->userModel->getAllUserInfo($where);
        foreach ($query as $key => $value) {
            $list['list'][$key] = [
                "key" => $value['id'],
                "username" => $value['username'],
                "name" => $value['firstName'] .' '. $value['middleName'] .' '. $value['lastName'] .' '. $value['suffix'],
                "email" =>  $value['email'],
                "contact" =>  $value['contact'],
                "status" =>  $value['status'],
                "createdAt" =>  $value['createdAt'],
                "userType" =>  $value['userType'],
                "desc" =>  $value['userTypeDescription'],
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
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }


    public function getAllInactiveUserList(){
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

        $list = [];
        $list['list'] = $this->userModel->getAllUserInfo(['userType' => 1, 'status' => 0]);

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    } 

}