
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('css/common.css') ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript" language="javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-multifile@2.2.2/jquery.MultiFile.min.js" integrity="sha256-TiSXq9ubGgxFwCUu3belTfML3FOjrdlF0VtPjFLpksk=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?= base_url('se2/js/service/HuskyEZCreator.js')?>" charset="utf-8"></script>
    <title><?= $tp ?></title>
</head>

<body>
    <div id="write-wrap" style="text-align: center;">
        <h1><?= $tp ?></h1>
        <form method="post" enctype="multipart/form-data" name="frm" >
            <input type="hidden" name="type" value="act">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="form-group">
                <label for="title" class="write-label">제목</label>
                <input type="text" id="title" style="width:650px;" name="title" value="<?= htmlspecialchars($row['title']) ?>" required>
            </div>

            <div class="form-group mt10">
                <div class="write-label">설정</div>
                <label for="notice_yn" class="write-config-label">공지 여부</label>
                <input type="checkbox" id="notice_yn" name="notice_yn" value="Y" <?= $row['notice_yn'] === 'Y' ? 'checked' : '' ?>>
                <label for="visible_yn" class="write-config-label">공개 여부</label>
                <input type="checkbox" id="visible_yn" name="visible_yn" value="Y" <?= $row['visible_yn'] === 'Y' ? 'checked' : '' ?>>
            </div>
            <?php if (count($fileList) > 0): ?>
            <div class="form-group mt10">
                <p class="write-label">기존 첨부파일</p>
                <ul class="prevFile">
                    <?php foreach ($fileList as $file): ?>
                    <li>
                        <span class="file-name"><?= htmlspecialchars($file['file_name']) ?></span>
                        <span class="file-size">(<?=$file['file_size']?>)</span>
                        <button type="button" class="delete-file" data-file-id="<?= $file['id'] ?>">삭제</button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="form-group mt10">
                <label for="addfile" class="write-label">파일첨부</label>
                <div class="file-input">
                    <button type="button">파일 선택</button>
                    <input type="file" style="width:85px;" multiple="multiple" id="file_upload" name="file_upload[]" class="maxsize-5120" />
                </div>
                <div id="file_upload_list"></div>
            </div>

            <div class="form-group mt10">
                <label for="ir1" class="write-label">내용</label>
                <textarea id="ir1" name="content" style="width:650px;height: 300px;"><?= htmlspecialchars($row['content']) ?></textarea>
            </div>

            <button type="submit" class="btn-normal mt10" onclick="submitContents();"><?= $tp ?></button>
            <button type="button" class="btn-normal mt10" onclick="history.back();">취소</button>
        </form>
    </div>
    <script>
    var oEditors = [];
    nhn.husky.EZCreator.createInIFrame({
        oAppRef: oEditors,
        elPlaceHolder: "ir1",
        sSkinURI: "/ci/public/se2/SmartEditor2Skin.html",
        fCreator: "createSEditor2"
    });
    $(function() { // wait for document to load
        $('#file_upload').MultiFile({
            list: '#file_upload_list',
            max:3,
            STRING: {
                toomany:'파일은 최대 3개까지만 업로드할 수 있습니다.'
            }
        });

        $(document).on('click', '.delete-file', function () {
            const fileId = $(this).data('file-id');
            if (confirm('이 파일을 삭제하시겠습니까?')) {
                $.ajax({
                    url: '<?= site_url('file/delete') ?>', // 파일 삭제를 처리하는 PHP 파일
                    method: 'POST',
                    data: { id: fileId },
                    success: function (response) {
                        if (response.success) {
                            alert('파일이 삭제되었습니다.');
                            $(`button[data-file-id="${fileId}"]`).closest('li').remove();
                        } else {
                            alert('파일 삭제에 실패했습니다.');
                        }
                    }
                });
            }
        });
    });

    function submitContents() {
        if (!document.frm.checkValidity()) {
            return false;
        }
        event.preventDefault();
        oEditors.getById["ir1"].exec("UPDATE_CONTENTS_FIELD", []);
        let content = document.getElementById("ir1").value;
        content = stripHTMLTags(content);
        if(content.length < 1) {
            alert('내용을 입력해주세요');
            return false;
        }
        document.frm.submit();
    }

    function stripHTMLTags(html) {
        const div = document.createElement("div");
        div.innerHTML = html; // HTML 내용을 DOM에 삽입
        div.remove();
        return div.textContent || div.innerText || ""; // 텍스트만 반환
    }
    </script>

</body>

</html>