<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa bệnh nhân - MVC DEMO</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container narrow">
    <section class="panel">
        <a href="index.php">← Quay lại danh sách</a>
        <h1>Sửa bệnh nhân #<?= (int) $patient['id'] ?></h1>
        <form method="post" action="index.php?action=update&amp;id=<?= (int) $patient['id'] ?>" class="stack">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <div class="field"><label for="name">Họ và tên</label>
                <input type="text" id="name" name="name" value="<?= h($patient['name']) ?>" maxlength="150" required>
            </div>
            <div class="field"><label for="birth_year">Năm sinh</label>
                <input type="number" id="birth_year" name="birth_year" value="<?= (int) $patient['birth_year'] ?>" min="1900" max="<?= date('Y') ?>" required>
            </div>
            <button type="submit">Lưu thay đổi</button>
        </form>
    </section>
</main>
</body>
</html>
