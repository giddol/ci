<?php

namespace App\Controllers;

use App\Models\BoardModel;

class Board extends BaseController
{
    protected $boardModel;

    public function __construct()
    {
        $this->boardModel = new BoardModel();
    }

    private function getBoardListData($id = '')
    {

        $isAdmin = session()->get('is_admin');
        $keyword = $this->request->getGet('keyword');
        $searchTp = $this->request->getGet('searchTp');
        $currentPage = (int) $this->request->getGet('page') ?: 1;

        // 검색 조건을 설정합니다.
        $visible_yn = $isAdmin ? "" : "Y";

        $limit = 10;
        $param = [];

        // 검색 타입에 따른 조건 처리
        if ($keyword) {
            $param['keyword'] = $keyword;
            $param['searchTp'] = $searchTp;
        }
        $addParam = $param ? http_build_query($param) : "";

        // 데이터 조회
        $posts = $this->boardModel->getList($keyword, $searchTp, $currentPage, $limit, 'N', $visible_yn);

        // 총 게시글 수 조회
        $total = $this->boardModel->getCount($keyword, $searchTp, 'N', $visible_yn);
        $totalPage = ceil($total / $limit);

        // 공지사항 데이터 조회
        $noticePosts = $this->boardModel->getList($keyword, $searchTp, '', '', 'Y', $visible_yn);


        $pageInterval = 5;
        $startPage = floor(($currentPage - 1) / $pageInterval) * $pageInterval + 1;
        $endPage = min($startPage + $pageInterval - 1, $totalPage);

        $prevPage = $startPage - 1;
        if ($prevPage < 1) {
            $prevPage = 1;
        }

        $nextPage = $endPage + 1;
        if ($nextPage > $totalPage) {
            $nextPage = $totalPage;
        }

        return [
            'rows' => $posts,
            'rows_notice' => $noticePosts,
            'totalPage' => $totalPage,
            'isAdmin' => $isAdmin,
            'addParam' => $addParam,
            'currentPage' => $currentPage,
            'prevPage' => $prevPage,
            'nextPage' => $nextPage,
            'startPage' => $startPage,
            'endPage' => $endPage,
            'searchTp' => $searchTp,
            'keyword' => $keyword,
            'id' => $id,
        ];
    }

    // 게시글 목록을 보여주는 메서드
    public function index()
    {
        // 게시글 목록 쿼리
        $data = $this->getBoardListData();

        return view('board/index', $data);
    }

    // 게시글 보기
    public function view($id)
    {
        $post = $this->boardModel->getOneById($id);

        $this->boardModel->incrementHit($id);
        $files = $this->boardModel->getFiles($id);

        foreach ($files as $key => $val) {
            if (isset($val['file_size'])) {
                $files[$key]['file_size'] = $this->formatFileSize($val['file_size']);
            }
        }

        $data = $this->getBoardListData($id);
        $data['post'] = $post;
        $data['files'] = $files;

        return view('board/view', $data);
    }

    public function login()
    {
        return view('login');
    }

    public function write()
    {
        $isAdmin = session()->get('is_admin');
        if (!$isAdmin) return redirect()->to('board/');
        $data = [];
        $data['tp'] = "등록";
        $data['id'] = '';
        $data['row'] = [
            'title' => '',
            'notice_yn' => '',
            'visible_yn' => 'Y',
            'content' => '',
        ];
        $data['fileList'] = [];
        return view('board/write', $data);
    }

    public function modify($id)
    {
        $isAdmin = session()->get('is_admin');
        if (!$isAdmin) return redirect()->to('board/');
        $row = $this->boardModel->getOneById($id);
        $files = $this->boardModel->getFiles($id);
        foreach ($files as $key => $val) {
            if (isset($val['file_size'])) {
                $files[$key]['file_size'] = $this->formatFileSize($val['file_size']);
            }
        }

        $data['tp'] = "수정";
        $data['id'] = $id;
        $data['row'] = $row;
        $data['fileList'] = $files;

        return view('board/write', $data);
    }

    public function modifyAction($id)
    {
        $isAdmin = session()->get('is_admin');
        if (!$isAdmin) return redirect()->to('board/');

        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');
        $notice_yn = $this->request->getPost('notice_yn') ? 'Y' : 'N';
        $visible_yn = $this->request->getPost('visible_yn') ? 'Y' : 'N';

        if (empty($title) || empty($content)) {
            echo "<script>alert('제목과 내용을 입력해주세요.'); history.back();</script>";
            exit;
        }


        $files = $this->boardModel->getFiles($id);

        $uploadedFiles = $this->uploadFiles(count($files));

        try {
            if (!empty($title) && !empty($content)) {
                $this->boardModel->savePost($title, $content, $notice_yn, $visible_yn, $id);

                // 파일 정보를 board_files 테이블에 저장
                if ($uploadedFiles) {
                    $this->boardModel->saveFiles($uploadedFiles, $id);
                }

                echo "<script>alert('수정에 성공했습니다.');window.location.href = '" . base_url('/board/view/' . $id) . "';</script>";
                exit;
            }
        } catch (\Exception $e) {
            echo "<script>alert('수정에 실패했습니다.'); history.back();</script>";
            exit;
        }
    }

    private function uploadFiles($prevFileCount = 0)
    {
        $uploadedFiles = [];
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/'; // 파일을 저장할 디렉토리
        $maxFileSize = 1 * 1024 * 1024; // 최대 파일 크기 5MB
        $maxFiles = 3; // 최대 파일 수

        // $_FILES['file_upload'] 배열에서 파일 처리
        if (isset($_FILES['file_upload']) && count($_FILES['file_upload']['name']) > 0) {
            foreach ($_FILES['file_upload']['error'] as $key => $val) {
                if ($val === UPLOAD_ERR_NO_FILE) {
                    unset($_FILES['file_upload']['tmp_name'][$key]);
                    unset($_FILES['file_upload']['name'][$key]);
                    unset($_FILES['file_upload']['size'][$key]);
                    unset($_FILES['file_upload']['type'][$key]);
                }
            }
            array_values($_FILES['file_upload']['tmp_name']);
            array_values($_FILES['file_upload']['name']);
            array_values($_FILES['file_upload']['size']);
            array_values($_FILES['file_upload']['type']);

            $fileCount = count($_FILES['file_upload']['name']);

            $sumFileCount = $fileCount + $prevFileCount;

            // 파일 개수 제한
            if ($sumFileCount > $maxFiles) {
                $_FILES['file_upload'] = null;
                echo "<script>alert('최대 {$maxFiles}개 파일만 첨부 가능합니다.'); history.back();</script>";
                exit;
            }

            // 파일 처리 반복문
            for ($i = 0; $i < $fileCount; $i++) {

                $fileTmpPath = $_FILES['file_upload']['tmp_name'][$i];
                $fileName = $_FILES['file_upload']['name'][$i];
                $fileSize = $_FILES['file_upload']['size'][$i];
                $fileType = $_FILES['file_upload']['type'][$i];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $newFileName = uniqid('file_', true) . '.' . $fileExtension;

                // 파일 크기 제한
                if ($fileSize > $maxFileSize) {
                    $_FILES['file_upload'] = null;
                    echo "<script>alert('파일 {$fileName}의 크기가 5MB를 초과합니다.'); history.back();</script>";
                    exit;
                }

                // 파일을 저장할 경로
                $destPath = $uploadDir . $newFileName;

                // 파일 이동
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // 업로드된 파일 정보 저장
                    $uploadedFiles[] = [
                        'file_name' => $fileName,
                        'file_path' => $destPath,
                        'file_size' => $fileSize
                    ];
                } else {
                    $_FILES['file_upload'] = null;
                    echo "<script>alert('파일 업로드에 실패했습니다: {$fileName}'); history.back();</script>";
                    exit;
                }
            }
        }
        return $uploadedFiles;
    }

    public function writeAction()
    {
        $isAdmin = session()->get('is_admin');
        if (!$isAdmin) return redirect()->to('board/');

        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');
        $notice_yn = $this->request->getPost('notice_yn') ? 'Y' : 'N';
        $visible_yn = $this->request->getPost('visible_yn') ? 'Y' : 'N';

        if (empty($title) || empty($content)) {
            echo "<script>alert('제목과 내용을 입력해주세요.'); history.back();</script>";
            exit;
        }

        $uploadedFiles = $this->uploadFiles();

        try {
            if (!empty($title) && !empty($content)) {
                $this->boardModel->savePost($title, $content, $notice_yn, $visible_yn);
                $id = $this->boardModel->insertID();

                // 파일 정보를 board_files 테이블에 저장
                if ($uploadedFiles) {
                    $this->boardModel->saveFiles($uploadedFiles, $id);
                }

                echo "<script>alert('등록에 성공했습니다.');window.location.href = '" . base_url('/board/view/' . $id) . "';</script>";
                exit;
            }
        } catch (\Exception $e) {
            echo "<script>alert('등록에 실패했습니다.'); history.back();</script>";
            exit;
        }
    }

    public function loginAction()
    {
        $password = $this->request->getPost('password');

        $adminPassword = 'admin123';

        // 비밀번호 확인
        if ($password === $adminPassword) {
            // 비밀번호가 맞으면 세션에 관리자 권한 부여
            session()->set('is_admin', true);
            return redirect()->to('board/');
        } else {
            // 비밀번호가 틀렸을 경우
            return view('login', ['errorMessage' => '비밀번호가 잘못되었습니다.']);
        }
    }

    public function logout()
    {
        $session = session();
        $session->remove('is_admin');
        $session->destroy();

        echo "<script>alert('로그아웃 되었습니다.'); window.location.href = '" . site_url('board') . "';</script>";
        exit;
    }

    public function delete($id)
    {
        if ($this->boardModel->deletePost($id)) {
            echo "<script>alert('게시글 삭제에 성공했습니다.');location.href='" . site_url('board') . "';</script>";
            exit;
        } else {
            echo "<script>alert('게시글 삭제에 실패했습니다:'); history.back();</script>";
            exit;
        }
    }


    function formatFileSize($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . ' byte';
        } elseif ($bytes < 1048576) { // 1024 * 1024 = 1048576
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) { // 1024 * 1024 * 1024 = 1073741824
            return number_format($bytes / 1048576, 2) . ' MB';
        } else {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
    }
}
