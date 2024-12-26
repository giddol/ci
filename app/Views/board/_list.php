<table border="0" cellspacing="0" cellpadding="0" width="100%" class="list">
    <colgroup>
        <col style="width:60px;">
        <col style="width:auto">
        <col style="width:100px;">
        <col style="width:60px;">
    </colgroup>
    <thead>
        <tr class="listhead" align="center">
            <th class="tdnum">번호</th>
            <th class="tdsub">제목</th>
            <th class="tddate">날짜</th>
            <th class="tdhit">조회</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows_notice as $row) { ?>
            <tr class="listnotice listtr <?= (int)$row['id'] === $id ? "currenttr" : "" ?> <?= $row['visible_yn'] === 'N' ? "novisible" : ""?>">
                <td class="tdnum">공지</td>
                <td class="tdsub"><a href="<?= site_url('board/view/' . $row['id'] . '?' . $addParam . "&page=" . $currentPage) ?>"><?= htmlspecialchars($row['title']) ?></a></td>
                <td class="tdname"><?= substr($row['created_at'], 0, 10) ?></td>
                <td class="tdhit"><?= $row['hit'] ?></td>
            </tr>
        <?php } ?>
        <?php foreach ($rows as $row) { ?>
            <tr class="listtr <?= $row['id'] === $id ? "currenttr" : "" ?> <?= $row['visible_yn'] === 'N' ? "novisible" : ""?>">
                <td class="tdnum"><?= $row['id'] ?></td>
                <td class="tdsub"><a href="<?= site_url('board/view/' . $row['id'] . '?' . $addParam . "&page=" . $currentPage) ?>"><?= htmlspecialchars($row['title']) ?></a></td>
                <td class="tdname"><?= substr($row['created_at'], 0, 10) ?></td>
                <td class="tdhit"><?= $row['hit'] ?></td>
            </tr>
        <?php } ?>
        <?php if (!$rows) { ?>
            <tr>
                <td class="tdnum" colspan="4">검색 결과가 없습니다.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<div class="btns-container">
    <div class="btns-container--left">
        <input type="button" class="btn-normal" value="목록" onclick="location.href='<?=site_url('board')?>';">
    </div>
    <div class="btns-container--right">
        <?= $isAdmin ? '<input type="button" class="btn-normal" value="글쓰기" onclick="location.href=\''.site_url('board/write').'\';">' : "" ?>
        <?= $isAdmin ? '<input type="button" class="btn-normal" value="로그아웃" onclick="location.href=\''.site_url('logout').'\';">' : "" ?>
    </div>
</div>
<div class="pagination">
    <ul>

        <?php if ($startPage > 1) { ?>
            <li><a href="<?=site_url('board/?page=' . $prevPage . '&' . $addParam)?>">&lt;</a></li>
        <?php } ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++) { ?>
            <li><?= $i == $currentPage ? "<a class=\"currentPage\">$i</a>" : "<a href=\"".site_url('board/?page=' . $i . '&' . $addParam)."\">$i</a>" ?></li>
        <?php } ?>

        <?php if ($endPage < $totalPage) { ?>
            <li><a href="<?=site_url('board/?page=' . $nextPage . '&' . $addParam)?>">&gt;</a></li>
        <?php } ?>

    </ul>
</div>
<form method="get" name="search" action="<?=site_url('board')?>">
    <div class="search">
        <select name="searchTp">
            <option value="A" <?=$searchTp === 'A' ? 'selected' : '' ?>>제목</option>
            <option value="B" <?=$searchTp === 'B' ? 'selected' : '' ?>>내용</option>
            <option value="C" <?=$searchTp === 'C' ? 'selected' : '' ?>>제목+내용</option>
        </select>
        <input type="text" name="keyword" class="searchKeyword" value="<?=$keyword ?? '' ?>" size="20">
        <input type="submit" class="btn-normal" value="검색">
    </div>
</form>