<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết bệnh nhân - MVC DEMO</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container narrow">
    <section class="panel">
        <a href="index.php">← Quay lại danh sách</a>
        <h1>Chi tiết bệnh nhân #<?= (int) $patient['id'] ?></h1>
        <dl class="details">
            <dt>Họ tên</dt><dd><?= h($patient['name']) ?></dd>
            <dt>Năm sinh</dt><dd><?= (int) $patient['birth_year'] ?></dd>
            <dt>Ngày tạo</dt><dd><?= h($patient['created_at']) ?></dd>
        </dl>
        <a class="button" href="index.php?action=edit&amp;id=<?= (int) $patient['id'] ?>">Sửa thông tin</a>
    </section>
</main>
</body>
</html>
