<?php

namespace App\Models;

use CodeIgniter\Model;

class MiscModel extends Model
{
    protected $usertypeTable = "tblusertypes";
    protected $providerTable = "tblproviders";
    protected $catTable = "tblcategory";
    protected $courseList = "tblcourses";
    protected $typesTable = "tbltypes";
    protected $notifTable = "tblnotifications";
    protected $tableUser = "tblusers";


    public function getTypeList(){
        $query = $this->db->table($this->usertypeTable)->get();
        $results = $query->getResult();
        return $results;
    }
    public function getCatList(){
        $query = $this->db->table($this->catTable)->get();
        $results = $query->getResult();

        $all = array_map(function($el){

            foreach($el as $key => $val){
                $unitList = $this->db->table($this->unitTable)->get();
                $typeList = $this->db->table($this->typesTable)->where('catId', $el->id)->get();
                $el->unitsList = $unitList->getResult();
                $el->typeList = $typeList->getResult();
            }
            return $el;
        }, $results);

        return $all;
        // return $results;
    }
    public function getCourseList(){
        $query = $this->db->table($this->courseList)->get();
        $results = $query->getResult();
        return $results;
    }
    public function getCourse($where){
        $query = $this->db->table($this->courseList)->where($where)->get();
        $results = $query->getRow();
        return $results;
    }

    public function getUserType($userType){

        $query = $this->db->table($this->usertypeTable)->where('id', $userType)->get();
        $results = $query->getRow();

        return $results;

    }
    public function getProviderList(){
        $sql = "SELECT a.id, a.name FROM ".$this->providerTable." a";
        $query = $this->db->query($sql);
        $results = $query->getResult();
        // $query = $this->db->table($this->providerTable)->get();
        // $results = $query->getRow();

        return $results;

    }
    
    public function getNotification($where){
        $query = $this->db->table($this->notifTable)->where($where)->get();
        $results = $query->getResult();

        $all = array_map(function($el){
            foreach($el as $key => $val){
                // User Info
                $userParams = [
                    "id" => $el->fromUser
                ];
                $lq = "SELECT firstName, lastName FROM ". $this->tableUser ." WHERE id = :id:";
                $sInfo = $this->db->query($lq, $userParams);
                $el->sender = $sInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }
    public function sendNotification($data){
        $query = $this->db->table($this->notifTable)->insert($data);
        return $query ? true : false;
    }
    public function seenNotification($where){
        $query = $this->db->table($this->notifTable)->set('seen', 1)->where($where)->update();
        return $query ? true : false;
    }
    public function readNotification($where){
        $query = $this->db->table($this->notifTable)->set('isRead', 1)->where($where)->update();
        return $query ? true : false;
    }
    

}