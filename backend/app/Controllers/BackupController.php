<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AuthModel;
use CodeIgniter\RESTful\ResourceController;

class BackupController extends BaseController
{
    public function __construct(){
        //Models
        $this->authModel = new AuthModel();
    }

    public function backupDatabase(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        $hasPass = sha1($data->password);

        //Select Query for finding User Information
        $user = $this->authModel->where(['id' => $data->userId, 'password' => $hasPass])->get()->getRow();
        
        //Set Api Response return to the FE
        if($user){
            $db = db_connect();
            $dbutil = \Config\Database::utils();
            $tables = $db->listTables();

            $prefs = array(
                'tables'        => $tables,   // Array of tables to backup.
                'ignore'        => array(),                     // List of tables to omit from the backup
                'format'        => 'txt',                       // gzip, zip, txt
                'filename'      => 'ascots_db.sql',              // File name - NEEDED ONLY WITH ZIP FILES
                'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
                'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
                'newline'       => "\n"                         // Newline character used in backup file
            );

            $backup = $dbutil->backup($prefs);

            ob_start();

            return $this->response
                ->setStatusCode(200)
                ->setContentType('text/plain')
                ->setBody(json_encode($backup));
        } else {
            $response = [
                'error' => 401,
                'title' => 'Backup Denied',
                'message' => 'Unauthoried user is trying to backup'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }
    

}