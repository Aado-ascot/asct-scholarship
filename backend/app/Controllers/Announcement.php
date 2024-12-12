<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\UsersModel;
use App\Models\AnnouncementModel;
use App\Models\AuthModel;
use App\Models\MiscModel;
use \Firebase\JWT\JWT;

class Announcement extends BaseController
{
    public function __construct(){
        //Models
        $this->userModel = new UsersModel();
        $this->authModel = new AuthModel();
        $this->miscModel = new MiscModel();
        $this->announceModel = new AnnouncementModel();
    }

    public function createAnnouncement(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 

        $query = $this->announceModel->insert($data);
        // print_r($query);
        // exit();
        if($query){

            $response = [
                'title' => 'Announcement',
                'message' => 'Your announcement is posted successfully'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'title' => 'Posting Failed!',
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

    public function getList(){
        // Check Auth header bearer
        $list = [];
        $list['list'] = $this->announceModel->get()->getResult();

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

    public function deleteAnnouncement(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        $where = [
            'id' => $data->aid
        ];
        
        //Select Query for finding User Information
        $query = $this->announceModel->deleteAnnounceInfo($where);

        if($query){

            $response = [
                'title' => 'Delete successful',
                'message' => 'Your attachment is deleted'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'title' => 'Update Failed!',
                'message' => 'Please check your data and connect to your Admin'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }

}