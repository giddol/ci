url: http://112.172.166.48/ci/public/board/

http://112.172.166.48/ci/public/login 주소창에 치고 들어가서 
비밀번호: admin123 로그인하면 글쓰기/수정/삭제 가능, 공개여부 체크 안돼있는 게시글 제목 취소선인 상태로 노출

$routes->get('board', 'Board::index');  게시판 목록  
$routes->get('board/view/(:num)', 'Board::view/$1');  게시글 보기  
$routes->get('login', 'Board::login');  로그인
$routes->post('login', 'Board::loginAction');
$routes->get('logout', 'Board::logout');  로그아웃
$routes->get('board/write/', 'Board::write');  글쓰기
$routes->post('board/write/', 'Board::writeAction');
$routes->get('board/modify/(:num)', 'Board::modify/$1');  수정
$routes->post('board/modify/(:num)', 'Board::modifyAction/$1');
$routes->get('file/download/(:num)', 'FileController::download/$1');  첨부파일 다운로드
$routes->delete('board/delete/(:num)', 'Board::delete/$1');  게시글 삭제
$routes->post('file/delete', 'FileController::deleteFile');  첨부파일 삭제
