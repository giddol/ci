<?php

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table      = 'board_files';
    protected $primaryKey = 'id';
    protected $allowedFields = ['notice_id', 'file_name', 'file_path', 'file_size'];

    // 파일 정보를 조회하는 메서드
    public function getFileById($id)
    {
        return $this->where('id', $id)->first();
    }
}