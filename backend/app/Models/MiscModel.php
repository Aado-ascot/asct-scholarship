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
    

}