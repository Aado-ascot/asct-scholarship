<?php

namespace App\Models;

use CodeIgniter\Model;

class ScholarshipModel extends Model
{
    protected $table      = 'tblscholarships';
    protected $tableApplication      = 'tblscholar_application';
    protected $tableUser      = 'tblusers';
    protected $tableCourse      = 'tblcourses';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    
    protected $useSoftDeletes = false;

    protected $allowedFields = ['title', 'slot', 'provider', 'qualification', 'coveredCourses', 'otherDetailsLink', 'requirements', 'dueDate', 'createdDate', 'createdBy', 'status'];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getScholarshipList($where) {
        $sql = "SELECT * FROM ".$this->table." WHERE dueDate > CURDATE()";
        $query = $this->db->query($sql);
        $results = $query->getResult('array');
        // $query = $this->db->table($this->table)->where($where)->get();
        // $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $userParams = [
                    "id" => $el["createdBy"]
                ];
                $lq = "SELECT id, lastName, firstName, suffix, middleName FROM ". $this->tableUser ." WHERE id = :id:";
                $userInfo = $this->db->query($lq, $userParams);
                $el["creator"] = $userInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

    public function deleteScholarshipProgram($where){
        $query = $this->db->table($this->table)->where($where)->delete();
        return $query ? true : false;
    }

    public function getScholarshipListAdmin() {

        $query = $this->db->table($this->table)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $userParams = [
                    "id" => $el["createdBy"]
                ];
                $lq = "SELECT id, lastName, firstName, suffix, middleName FROM ". $this->tableUser ." WHERE id = :id:";
                $userInfo = $this->db->query($lq, $userParams);
                $el["creator"] = $userInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

    public function getScholarshipByUser($where) {

        $query = $this->db->table($this->tableApplication)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $userParams = [
                    "id" => $el["scholarId"]
                ];
                $lq = "SELECT * FROM ". $this->table ." as a WHERE id = :id:";
                $sInfo = $this->db->query($lq, $userParams);
                $el["scholarship"] = $sInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }
    
    public function getScholarshipByUserDetails($where) {

        $query = $this->db->table($this->tableApplication)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $userParams = [
                    "id" => $el["scholarId"]
                ];
                $lq = "SELECT * FROM ". $this->table ." as a WHERE id = :id:";
                $sInfo = $this->db->query($lq, $userParams);
                $el["scholarship"] = $sInfo->getRow();

                $studentParams = [
                    "id" => $el["studentId"]
                ];
                $lsq = "SELECT * FROM ". $this->tableUser ." as a WHERE id = :id:";
                $stdInfo = $this->db->query($lsq, $studentParams);
                $el["student"] = $stdInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

    public function getAppliedScholarships($where) {

        $query = $this->db->table($this->tableApplication)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                // Scholarship Info
                $infoParams = [
                    "id" => $el["scholarId"]
                ];
                $lq = "SELECT * FROM ". $this->table ." as a WHERE id = :id:";
                $sInfo = $this->db->query($lq, $infoParams);
                $el["scholarship"] = $sInfo->getRow();

                // User Info
                $userParams = [
                    "id" => $el["studentId"]
                ];
                $lq = "SELECT * FROM ". $this->tableUser ." as a WHERE id = :id:";
                $sInfo = $this->db->query($lq, $userParams);
                $el["student"] = $sInfo->getRow();

                unset($el["student"]->password);
            }
            return $el;
        }, $results);

        return $all;
    }
    public function updateScholarship($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    public function updateScholarshipStatus($where, $status){
        $query = $this->db->table($this->table)->set('status', $status)->where($where)->update();
        return $query ? true : false;
    }
    public function updateScholarshipSlot($where){
        $query = $this->db->table($this->table)->set('applied', 'applied+1', false)->where($where)->update();
        return $query ? true : false;
    }
    public function updateMinusScholarshipSlot($where){
        $query = $this->db->table($this->table)->set('applied', 'applied-1', false)->where($where)->update();
        return $query ? true : false;
    }




    public function submitApplication($data) {
        $query = $this->db->table($this->tableApplication)->insert($data);
        return $query ? true : false;
    }
    public function updateApplication($where, $setData) {
        $query = $this->db->table($this->tableApplication)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    public function getUserApplications($where) {
        $sql = "SELECT a.id, a.scholarId FROM ".$this->tableApplication." a WHERE a.appStatus IN (0,1) AND a.studentId=". $where['uid'];
        $query = $this->db->query($sql);
        $results = $query->getResult();

        return $results;
    }


    // Dashboard
    
    public function getUsers() {
        $sql = "SELECT a.id FROM ".$this->tableUser." a WHERE a.userType = '2'";
        $query = $this->db->query($sql);
        $results = $query->getResult();

        return $results;
    }
    public function getApplications() {
        $sql = "SELECT a.id, a.scholarId, a.dateApplied, a.appStatus, b.title FROM ".$this->tableApplication." a LEFT JOIN ". $this->table ." b ON b.id = a.scholarId ORDER BY a.dateApplied DESC";
        $query = $this->db->query($sql);
        $results = $query->getResult();
        $list = [];
        foreach ($results as $key => $value) {
            $list[$value->title][$key] = $value->appStatus;
            // $list[$key] = $value;
        }

        return $list;
    }
    public function getEachCourse() {
        $sql = "SELECT a.id, a.scholarId, a.dateApplied, a.appStatus, c.title FROM ".$this->tableApplication." a LEFT JOIN ". $this->tableUser ." b ON b.id = a.studentId LEFT JOIN ". $this->tableCourse ." c ON c.id = b.courseId ORDER BY a.dateApplied DESC";
        $query = $this->db->query($sql);
        $results = $query->getResult();
        $list = [];
        foreach ($results as $key => $value) {
            $list[$value->title][$key] = $value->appStatus;
            // $list[$key] = $value;
        }

        return $list;
    }
    public function getQualified() {
        $sql = "SELECT a.id FROM ".$this->tableApplication." a WHERE a.appStatus = '2'";
        $query = $this->db->query($sql);
        $results = $query->getResult();

        return $results;
    }
    public function getUnQualified() {
        $sql = "SELECT a.id FROM ".$this->tableApplication." a WHERE a.appStatus = '3'";
        $query = $this->db->query($sql);
        $results = $query->getResult();

        return $results;
    }
    public function getPendings() {
        $sql = "SELECT a.id FROM ".$this->tableApplication." a WHERE a.appStatus IN (0,1)";
        $query = $this->db->query($sql);
        $results = $query->getResult();

        return $results;
    }
    

}

