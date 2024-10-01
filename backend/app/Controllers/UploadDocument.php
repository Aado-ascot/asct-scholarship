<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\UsersModel;
use App\Models\AuthModel;
use App\Models\AttachmentModel;
use \Firebase\JWT\JWT;

class UploadDocument extends BaseController
{
    public function __construct(){
        //Models
        $this->userModel = new UsersModel();
        $this->authModel = new AuthModel();
        $this->attachModel = new AttachmentModel();
    }

    public function insertAttachment(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        $payload = json_decode(json_encode($data), true);
        
        //Select Query for finding User Information
        $query = $this->attachModel->insert($payload);

        if($query){

            $response = [
                'title' => 'Upload successful',
                'message' => 'Your attachment is updated and uploaded'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'title' => 'Registration Failed!',
                'message' => 'Please check your data and connect to your Admin'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }

    public function getAttachment(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        //Select Query for finding User Information
        $query = $this->attachModel->where(['reqType' => $data->type,'userId' => $data->uid])->get()->getRow();

        if($query){
            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($query));
            
        } else {
            $response = [
                'error' => 404,
                'title' => 'Get Attachment failed!',
                'message' => 'No Result Found'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    

}