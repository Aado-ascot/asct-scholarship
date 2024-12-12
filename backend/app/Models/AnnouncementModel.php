<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table      = 'tblannouncements';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $allowedFields = ['title','announcement', 'postedBy'];

    protected $useTimestamps = false;
    protected $returnType     = 'array';
    protected $createdField  = 'postedDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function updateFileInfo($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    public function deleteAnnounceInfo($where){
        $query = $this->db->table($this->table)->where($where)->delete();
        return $query ? true : false;
    }

}