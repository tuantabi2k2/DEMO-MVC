<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - MVC DEMO</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container narrow">
    <section class="panel">
        <h1>Đăng nhập MVC DEMO</h1>
        <?php if ($error !== null): ?><p class="alert error"><?= h($error) ?></p><?php endif; ?>
        <form method="post" action="index.php?action=login" class="stack">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <div class="field"><label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" maxlength="50" autocomplete="username" required>
            </div>
            <div class="field"><label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit">Đăng nhập</button>
        </form>
    </section>
</main>
</body>
</html>
