<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\UsersModel;
use App\Models\AuthModel;
use App\Models\AttachmentModel;
use App\Models\MiscModel;
use \Firebase\JWT\JWT;

class UploadDocument extends BaseController
{
    public function __construct(){
        //Models
        $this->userModel = new UsersModel();
        $this->authModel = new AuthModel();
        $this->attachModel = new AttachmentModel();
        $this->miscModel = new MiscModel();
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

    public function updateAttachmentStatus(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        $payload = json_decode(json_encode($data->updateDetails), true);

        $where = [
            'id' => $data->fileId
        ];
        
        //Select Query for finding User Information
        $query = $this->attachModel->updateFileInfo($where, $payload);

        if($query){
            if($data->notify && !$data->isStudent){
                $notif = json_decode(json_encode($data->notification), true);
                $this->miscModel->sendNotification($notif);
            } else if($data->notify && $data->isStudent){
                $this->sendAdminsNotification(
                    3, 
                    $data->notification->fromUser, 
                    $data->notification->message
                );
            }

            $response = [
                'title' => 'Update successful',
                'message' => 'Your attachment is updated'
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

    public function sendAdminsNotification($type, $from, $contents, $appId = 0){
        $query = $this->authModel->where(['userType' => $type])->get()->getResult();
        foreach ($query as $key => $value) {
            $notif = [
                "applicationId" => $appId,
                "toUser" => $value->id,
                "fromUser" => $from, 
                "message" => $contents, 
                "notifType" => "green", 
                "seen" => 0, 
            ];
            $this->miscModel->sendNotification($notif);
        }
    }

    public function deleteAttachmentStatus(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        $where = [
            'id' => $data->fileId
        ];
        
        //Select Query for finding User Information
        $query = $this->attachModel->deleteFileInfo($where);

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

    public function getAttachments(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        //Select Query for finding User Information
        $query = $this->attachModel->where(['userId' => $data->uid])->get()->getResult();
        $list = [];
        foreach ($query as $key => $value) {
            $list['list'][$key] = $value;
        }
        if($query){
            
            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($list));
            
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