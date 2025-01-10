<?php namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\AuthModel;
use App\Models\MiscModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
                $user->courseDetails = $this->miscModel->getCourse(['id' => $user->courseId]);
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

        $where = [
            "username" => $data->username,
            // "firstName" => $data->username,
            // "lastName" => $data->username,
            // "middleName" => $data->username,
        ];

        
        // exit();
        $check = $this->userModel->validateUser($where);

        if(sizeof($check) > 0){
            $response = [
                'error' => 200,
                'title' => 'Registration Failed!',
                'message' => 'This user is already exist'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
        $query = $this->userModel->insert($data);

        if($query){
            $lastId = $this->userModel->getInsertID();
            $this->sendEmail($data->email, $lastId, $data);

            $response = [
                'title' => 'Registration Complete',
                'message' => 'User data has been successfully submitted. you can now login.'
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

    public function sendEmail($emailAdd, $id, $details){
        $mail = new PHPMailer(true);
        $userID = base64_encode($id);

        try {
            // Server settings
            $mail->SMTPDebug = 0;                                       // Enable verbose debug output
            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'acostanonuevodizonorpia@gmail.com';    // SMTP username
            $mail->Password   = 'wviv drce qlka zuge';                  // SMTP password
            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = 587;                                    // TCP port to connect to
        
            // Recipients
            $mail->setFrom('acostanonuevodizonorpia@gmail.com', 'ASCOT Schedule Management');
            $mail->addAddress($emailAdd);     // Add a recipient
        
            // Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');                 // Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');            // Optional name
        
            // Content
            $mail->isHTML(true);                                          // Set email format to HTML
            $mail->Subject = 'ASCOT Scholarship Verification';
            $mail->Body    = '';
            $mail->Body .= '<h1>Hi '. $details->firstName .' </h1><br/>';
            $mail->Body .= 'To proceed further and login to your account, please click the link below to verify your account.<br/><br/>';
            $mail->Body .= '<a href="https://thesis-ascots-endpoints.site/scholarship/backend/public/ascots/api/v1auth/verified/'.$userID.'">Verify</a>';
        
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
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

    public function updateUserStatus(){
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

        $where = ['id' => $payload->id];
        $updateData = ['status' => $payload->status];

        $updatePassword =  $this->userModel->updatePassword($where, $updateData);

        if($updatePassword){
            $response = [
                'title' => 'User Status Update',
                'message' => 'Your successfully update status.'
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
        
        $where = [];
        $list = [];
        // $list['list'] = $this->userModel->getAllUserInfo($where);

        $query = $this->userModel->getAllUserInfo();
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