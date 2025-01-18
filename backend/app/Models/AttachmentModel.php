<?php

namespace App\Models;

use CodeIgniter\Model;

class AttachmentModel extends Model
{
    protected $table      = 'tblattachments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $allowedFields = ['userId', 'reqType', 'reqTitle', 'fileName', 'fileSize', 'status', 'uploadFile', 'remarks', 'createdDate'];

    protected $useTimestamps = false;
    protected $returnType     = 'array';
    protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function updateFileInfo($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    public function deleteFileInfo($where){
        $query = $this->db->table($this->table)->where($where)->delete();
        return $query ? true : false;
    }

}