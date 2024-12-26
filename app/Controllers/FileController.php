<?php

namespace App\Controllers;

use App\Models\FileModel;
use CodeIgniter\Controller;

class FileController extends Controller
{
    public function download($id)
    {
        $fileModel = new FileModel();

        // 파일 정보 조회
        $file = $fileModel->getFileById($id);

        // 파일이 없거나 경로에 파일이 존재하지 않으면 404 에러
        if (!$file || !file_exists($file['file_path'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('파일을 찾을 수 없습니다.');
        }

        // 파일 다운로드 헤더 설정
        return $this->response->download($file['file_path'], null)->setFileName($file['file_name']);
    }

    public function deleteFile()
    {
        $isAdmin = session()->get('is_admin');
        if (!$isAdmin) return redirect()->to('board/');

        $fileId = $this->request->getPost('id');

        $fileModel = new FileModel();

        // 파일 정보 가져오기
        $file = $fileModel->find($fileId);

        if ($file) {
            // 파일 경로 가져오기
            $filePath = $file['file_path'];

            // 파일 삭제 시도
            if (unlink($filePath)) {
                // DB에서 파일 삭제
                $fileModel->delete($fileId);

                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['success' => false, 'error' => '파일 삭제 실패']);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'error' => '파일을 찾을 수 없습니다.']);
        }
    }
}
