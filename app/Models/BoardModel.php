<?php

namespace App\Models;

use CodeIgniter\Model;

class BoardModel extends Model
{
    protected $table = 'board_notice';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'content', 'created_at', 'updated_at', 'hit', 'notice_yn', 'visible_yn'];

    // 게시글 목록 가져오기
    public function getList($keyword = '', $searchTp = '', $page = 0, $limit = 0, $notice_yn = '', $visible_yn = '')
    {
        $builder = $this->builder();
        if ($keyword) {
            switch ($searchTp) {
                case 'A':
                    $builder->like('title', $keyword);
                    break;
                case 'B':
                    $builder->like('content', $keyword);
                    break;
                case 'C':
                    $builder->groupStart()
                        ->like('title', $keyword)
                        ->orLike('content', $keyword)
                        ->groupEnd();
                    break;
                default:
                    $builder->like('title', $keyword);
            }
        }

        if ($visible_yn) $builder->where('visible_yn', $visible_yn);
        if ($notice_yn) $builder->where('notice_yn', $notice_yn);
        if ($page && $limit) {
            $offset = ($page - 1) * $limit;
            // echo $offset; echo $limit; exit;
            $builder->limit($limit, $offset);
        }


        return $builder->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->get()->getResultArray();
    }

    public function getCount($keyword = '', $searchTp = '', $notice_yn = '', $visible_yn = '')
    {
        $builder = $this->builder();
        if ($keyword) {
            switch ($searchTp) {
                case 'A':
                    $builder->like('title', $keyword);
                    break;
                case 'B':
                    $builder->like('content', $keyword);
                    break;
                case 'C':
                    $builder->groupStart()
                        ->like('title', $keyword)
                        ->orLike('content', $keyword)
                        ->groupEnd();
                    break;
                default:
                    $builder->like('title', $keyword);
            }
        }

        if ($visible_yn) $builder->where('visible_yn', $visible_yn);
        if ($notice_yn) $builder->where('notice_yn', $notice_yn);

        return $builder->countAllResults();
    }

    // 게시글 수 가져오기
    public function countPosts($where = '', $keyword = '')
    {
        $query = $this->db->table($this->table)
            ->selectCount('id')
            ->where('notice_yn', 'N')
            ->where($where, $keyword ? ['keyword' => "%$keyword%"] : []);

        return $query->get()->getRow()->id;
    }

    // 게시글 상세 조회
    public function getOneById($id)
    {
        return $this->where('id', $id)->first();
    }

    // 조회수 증가
    public function incrementHit($id)
    {
        $this->db->table($this->table)
            ->set('hit', 'hit + 1', false)
            ->set('updated_at', 'updated_at')
            ->where('id', $id)
            ->update();
    }

    // 첨부파일 조회
    public function getFiles($id)
    {
        $fileModel = new \App\Models\FileModel();
        return  $fileModel
            ->where('notice_id', $id)
            ->get()
            ->getResultArray();
    }

    // 파일 ID로 파일 정보 가져오기
    public function getFileById($fileId)
    {
        return $this->db->table('board_files')
            ->join('board_notice', 'board_files.notice_id = board_notice.id')
            ->where('board_files.id', $fileId)
            ->get()
            ->getRowArray();
    }

    // 게시물 저장
    public function savePost($title, $content, $notice_yn, $visible_yn, $id = null)
    {
        $data = [
            'title' => $title,
            'content' => $content,
            'notice_yn' => $notice_yn,
            'visible_yn' => $visible_yn
        ];

        if ($id) {
            return $this->update($id, $data);
        } else {
            return $this->insert($data);
        }
    }

    // board_files에 인서트
    public function saveFiles($files, $notice_id)
    {
        $fileModel = new \App\Models\FileModel();

        foreach ($files as $file) {
            $fileData = [
                'notice_id' => $notice_id,
                'file_name' => $file['file_name'],
                'file_path' => $file['file_path'],
                'file_size' => $file['file_size']
            ];
            $fileModel->insert($fileData);
        }
    }

    //게시물 삭제
    public function deletePost($id)
    {
        $files = $this->getFiles($id);
        foreach ($files as $file) {
            $filePath = $file['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath); // 서버에서 파일 삭제
            }
        }

        //board_files 테이블에서 삭제
        $this->db->table('board_files')->where('notice_id', $id)->delete();

        //board_notice에서 삭제
        return $this->delete($id);
    }
}
