<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Danh sách bệnh nhân - MVC DEMO</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container">
    <header class="topbar">
        <div>
            <h1>Quản lý bệnh nhân MVC</h1>
            <p class="muted">Xin chào, <?= h((string) $_SESSION['username']) ?></p>
        </div>
        <form method="post" action="index.php?action=logout">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <button type="submit" class="danger-outline">Đăng xuất</button>
        </form>
    </header>

    <?php if ($success !== null): ?><p class="alert success"><?= h($success) ?></p><?php endif; ?>
    <?php if ($error !== null): ?><p class="alert error"><?= h($error) ?></p><?php endif; ?>

    <section class="panel">
        <h2>Thêm bệnh nhân</h2>
        <form method="post" action="index.php?action=store" class="inline-form">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <div class="field grow"><label for="name">Họ và tên</label>
                <input id="name" name="name" type="text" maxlength="150" required>
            </div>
            <div class="field"><label for="birth_year">Năm sinh</label>
                <input id="birth_year" name="birth_year" type="number" min="1900" max="<?= date('Y') ?>" required>
            </div>
            <button type="submit">Thêm bệnh nhân</button>
        </form>
    </section>

    <section class="panel">
        <h2>Tìm kiếm và lọc</h2>
        <form method="get" action="index.php" class="inline-form">
            <input type="hidden" name="action" value="index">
            <div class="field grow"><label for="q">Tên bệnh nhân hoặc ID</label>
                <input id="q" name="q" type="text" value="<?= h($q) ?>" placeholder="Nhập tên hoặc ID">
            </div>
            <div class="field"><label for="year">Năm sinh (tùy chọn)</label>
                <input id="year" name="year" type="number" min="1900" max="<?= date('Y') ?>" value="<?= h((string) ($year ?? '')) ?>">
            </div>
            <button type="submit">Tìm kiếm</button>
            <a class="button secondary" href="index.php">Xóa lọc</a>
        </form>
    </section>

    <section class="panel">
        <div class="section-title"><h2>Danh sách bệnh nhân</h2><span class="muted"><?= $total ?> kết quả</span></div>
        <div class="table-scroll"><table>
            <thead><tr><th>ID</th><th>Họ tên</th><th>Năm sinh</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead>
            <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?= (int) $patient['id'] ?></td>
                    <td><?= h($patient['name']) ?></td>
                    <td><?= (int) $patient['birth_year'] ?></td>
                    <td><?= h($patient['created_at']) ?></td>
                    <td>
                        <div class="actions">
                            <a href="index.php?action=show&amp;id=<?= (int) $patient['id'] ?>">Chi tiết</a>
                            <a href="index.php?action=edit&amp;id=<?= (int) $patient['id'] ?>">Sửa</a>
                            <form method="post" action="index.php?action=delete&amp;id=<?= (int) $patient['id'] ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa bệnh nhân này?');">
                                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                                <button class="link-danger" type="submit">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$patients): ?><tr><td colspan="5" class="empty">Không tìm thấy bệnh nhân phù hợp.</td></tr><?php endif; ?>
            </tbody>
        </table></div>
        <?php if ($totalPages > 1): ?>
            <nav class="pagination" aria-label="Phân trang danh sách bệnh nhân">
                <?php for ($n = 1; $n <= $totalPages; $n++): ?>
                    <?php $href = 'index.php?' . http_build_query(['action' => 'index', 'q' => $q, 'year' => $year ?? '', 'page' => $n]); ?>
                    <a href="<?= h($href) ?>" <?= $n === $page ? 'class="current" aria-current="page"' : '' ?>><?= $n ?></a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
