<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('css/common.css') ?>" />
    <title><?= esc($post['title']) ?></title>
</head>

<body>
    <div id="wrap">
        <div class="view-container">
            <div class="view-info">
                <h1><?= esc($post['title']) ?></h1>
                <p>작성일: <?= esc($post['created_at']) ?>
                    <?= ($post['updated_at'] > $post['created_at']) ? '<br>수정일: ' . esc($post['updated_at']) : ""; ?>
                    <?php foreach ($files as $file): ?>
                        <br>첨부파일: <a class="add-file" href="<?= site_url('file/download/' . $file['id']) ?>"><?= esc($file['file_name']) ?> </a>(<?= $file['file_size'] ?>)
                    <?php endforeach; ?>
                </p>
            </div>
            <div class="view-content"><?= $post['content'] ?></div>
        </div>
        <div class="btns-container pb30">
            <div class="btns-container--left">
                <input type="button" class="btn-normal" value="목록" onclick="location.href='<?= site_url('board') ?>';">
                <?= $isAdmin ? '<input type="button" class="btn-normal" value="수정" onclick="location.href=\'' . site_url('board/modify/') . esc($post['id']) . '\';">' : "" ?>
                <?= $isAdmin ? '<input type="button" class="btn-normal" value="삭제" onclick="board_delete(' . esc($post['id']) . ');">' : "" ?>
            </div>
            <div class="btns-container--right">
                <?= $isAdmin ? '<input type="button" class="btn-normal" value="글쓰기" onclick="location.href=\'' . site_url('board/write') . '\';">' : "" ?>
            </div>
        </div>
        <?= view('board/_list') ?>
    </div>

    <script>
        function board_delete(id) {
            if (confirm('삭제하시겠습니까?')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= site_url('board/delete/') ?>' + id;

                // 숨겨진 필드로 _method 지정 (DELETE 시뮬레이션)
                var methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                // 폼 제출
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>