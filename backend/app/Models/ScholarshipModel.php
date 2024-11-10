<?php

namespace App\Models;

use CodeIgniter\Model;

class ScholarshipModel extends Model
{
    protected $table      = 'tblscholarships';
    protected $tableApplication      = 'tblscholar_application';
    protected $tableUser      = 'tblusers';
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

        $query = $this->db->table($this->table)->where($where)->get();
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
    public function updateScholarshipSlot($where){
        $query = $this->db->table($this->table)->set('applied', 'applied+1', false)->where($where)->update();
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

}

