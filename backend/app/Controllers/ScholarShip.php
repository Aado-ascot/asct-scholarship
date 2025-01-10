<?php namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\ScholarshipModel;
use App\Models\MiscModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    public function getScholarshipDetails(){
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
        
        //INSERT QUERY TO APPLICATION
        $query = $this->scholarModel->where("id", $payload->appId)->get()->getRow();

        if($query){
            $response = [
                'title' => 'Scholarship Program',
                'data' => $query,
                'message' => 'Your program has been fetched.'
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
            $lastInserted = $this->scholarModel->insertID();
            $where = [
                'id' => $payload['scholarId']
            ];
            $update = $this->scholarModel->updateScholarshipSlot($where);
            $response = [
                'title' => 'Apply Scholarship',
                'message' => 'Your application has been submitted.'
            ];

            // Send Notification
            $this->sendAdminsNotification(3, $payload['studentId'], "Student send an application for evaluation", $lastInserted);

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
    public function rejectRestOfApplication($userId){
        $query = $this->scholarModel->getUserApplications(["uid" => $userId]);
        foreach ($query as $key => $value) {
            $details = [
                "appStatus" => 3,
                "evaluatedBy" => 0,
                "approvedBy" => 0,
                "rejectedBy" => 1,
                "status" => "Rejected by the system",
                "remarks" => "Application automatically cancelled by the system"
            ];
            $this->scholarModel->updateApplication(["id"=>$value->id], $details);
            $where = [
                'id' => $value->scholarId
            ];
            $update = $this->scholarModel->updateMinusScholarshipSlot($where);

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
                $this->rejectRestOfApplication($data->studentId);
                // Send Email
                $this->sendEmail($data->email);
            } else if($data->actionType === "reject"){
                // Update scholarship
                $where = [
                    'id' => $data->scholarId
                ];
                $update = $this->scholarModel->updateMinusScholarshipSlot($where);
            } else if($data->actionType === "evaluate"){
                // Send Notification
                $this->sendAdminsNotification(4, $data->uid, "Evaluator send an application for approval", $data->appId);
            }
            // Send Notification
            $notif = json_decode(json_encode($data->notification), true);
            $this->miscModel->sendNotification($notif);

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

    public function sendEmail($emailAdd){
        $mail = new PHPMailer(true);

        $filePath = WRITEPATH . 'certificate/certificate.png';
        if (!file_exists($filePath)) {
            return $this->response
                ->setStatusCode(404)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'File not found']));
        }
        

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
            if (file_exists($filePath)) {
                $mail->addAttachment($filePath); // Add attachment
            }

            // Content
            $mail->isHTML(true);                                          // Set email format to HTML
            $mail->Subject = 'ASCOT Scholarship Certificate';
            $mail->Body    = '';
            $mail->Body = 'This is to certify that you have been approved for the scholarship program. Please download the certificate attached to this email.';
            
            

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function deleteScholarshipProgram(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        $where = [
            'id' => $data->scholarId
        ];
        // before delete validate if there is anyone applied
        
        
        //Select Query for finding User Information
        $query = $this->scholarModel->deleteScholarshipProgram($where);

        if($query){

            $response = [
                'title' => 'Delete successful',
                'message' => 'Scholarship Program is deleted'
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

    public function validateAppliedScholarship(){
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
            'scholarId'=> $payload->scholarId,
        ];
        $list = [];

        $list = $this->scholarModel->getScholarshipByUser($where);
        
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
            $sinfo->qualification = json_decode($sinfo->qualification);
            $sinfo->requirements = json_decode($sinfo->requirements);
            $value['familyBackground'] = json_decode($value['familyBackground']);

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

    public function getListDeclinedApplications(){
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
            'appStatus'=> 3,
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
    public function getListAdmin(){
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

        $list = [];

        $query = $this->scholarModel->getScholarshipListAdmin();
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
    public function updateScholarship(){
        $payload = $this->request->getJSON();

        $where = [
            'id' => $payload->scholarId
        ];
        $update = $this->scholarModel->updateScholarshipStatus($where, $payload->status);
        
        if($update){
            $response = [
                'title' => 'Update Status',
                'message' => 'Scholarship status updated'
            ];
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
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

    public function updateScholarshipDetails(){
        $payload = $this->request->getJSON();
        // $payload['qualification'] = json_encode($payload['qualification']);
        // $payload['requirements'] = json_encode($payload['requirements']);
        $payload->details->qualification = json_encode($payload->details->qualification);
        $payload->details->requirements = json_encode($payload->details->requirements);
        $payload->details = json_decode(json_encode($payload->details), true);
        $where = [
            'id' => $payload->sId
        ];
        $update = $this->scholarModel->updateScholarship($where, $payload->details);
        
        if($update){
            $response = [
                'title' => 'Update Status',
                'message' => 'Scholarship status updated'
            ];
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
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