<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
</head>
<body>
    <h1>관리자 로그인</h1>
    <form method="POST" action="<?= site_url('login') ?>">
        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">로그인</button>
    </form>
    <?php if (isset($errorMessage)): ?>
        <p style="color:red;"><?= $errorMessage ?></p>
    <?php endif; ?>
</body>
</html>