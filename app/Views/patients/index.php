
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Quản lý bệnh nhân MVC</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 24px;
            background: #f4f6f8;
            color: #17212b;
        }

        main {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 24px;
            border-radius: 10px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 12px;
            margin-bottom: 24px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1 1 180px;
        }

        input {
            padding: 10px;
            border: 1px solid #aaa;
            border-radius: 5px;
            width: 100%;
        }

        input:focus-visible,
        button:focus-visible {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #e9efff;
        }

        .error {
            color: #b91c1c;
        }
    </style>
</head>
<body>
<main>
    <h1>Quản lý bệnh nhân</h1>
    <h2>Thêm bệnh nhân</h2>

    <?php if ($error !== null): ?>
        <p class="error">
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <form action="index.php?action=store"
          method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <div class="field">
            <label for="name">Họ và tên</label>
            <input
                type="text"
                id="name"
                name="name"
                maxlength="150"
                required
            >
        </div>

        <div class="field">
            <label for="birth_year">Năm sinh</label>
            <input
                type="number"
                id="birth_year"
                name="birth_year"
                min="1900"
                max="<?= (int) date('Y') ?>"
                required
            >
        </div>

        <button type="submit">
            Thêm bệnh nhân
        </button>
    </form>

    <h2>Danh sách bệnh nhân</h2>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Năm sinh</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td>
                            <?= (int) $patient['id'] ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $patient['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>
                        <td>
                            <?= (int) $patient['birth_year'] ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($patients)): ?>
                    <tr>
                        <td colspan="3">
                            Chưa có bệnh nhân nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
