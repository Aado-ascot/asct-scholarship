<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\MiscModel;

class Misc extends BaseController
{

    public function __construct(){
        $this->miscModel = new MiscModel();
    }

    public function getUserTypes(){
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

        //Select Query for finding User Information
        $categories = [];
        $categories['list'] = $this->miscModel->getTypeList();

        //Set Api Response return to the FE
        if($categories){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($categories));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }   

    public function getCourses(){
        // Check Auth header bearer
        //Select Query for finding User Information
        $courses = [];
        $courses['list'] = $this->miscModel->getCourseList();

        //Set Api Response return to the FE
        if($courses){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($courses));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    } 
    public function getProviders(){
        // Check Auth header bearer
        //Select Query for finding User Information
        $courses = [];
        $courses['list'] = $this->miscModel->getProviderList();

        //Set Api Response return to the FE
        if($courses){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($courses));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    } 

    public function getUserNotification(){
        // Check Auth header bearer
        //Select Query for finding User Information
        $data = $this->request->getJSON();
        $notifs = [];
        $notifs['list'] = $this->miscModel->getNotification(['toUser' => $data->uId]);

        //Set Api Response return to the FE
        if($notifs){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($notifs));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    } 

    public function getUserNotificationUnseen(){
        // Check Auth header bearer
        //Select Query for finding User Information
        $data = $this->request->getJSON();
        $notifs = [];
        $notifs['list'] = $this->miscModel->getNotification([
            'toUser' => $data->uId,
            'seen' => 0
        ]);

        //Set Api Response return to the FE
        if($notifs){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($notifs));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    } 

    public function updateNotificationStatus(){
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
        
        
        if($data->type === "read"){
            $query = $this->miscModel->readNotification(["toUser" => $data->uId]);
        } else {
            $query = $this->miscModel->seenNotification(["toUser" => $data->uId]);
        }
        

        if($query){

            $response = [
                'title' => 'Notification Update',
                'message' => 'Update Complete'
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


}