# MVC DEMO - Nâng cấp 5 nhóm chức năng

Dùng để **chép đè/bổ sung vào dự án MVC DEMO hiện có** tại `C:\xampp\htdocs\MVC DEMO`.

## Chức năng
- CRUD: danh sách, xem chi tiết, thêm, sửa, xóa bệnh nhân
- Tìm kiếm theo tên hoặc ID; lọc năm sinh
- Phân trang mặc định 10 bệnh nhân/trang
- Đăng nhập/đăng xuất bằng PHP Session, mật khẩu hash
- REST API JSON với GET/POST/PUT/DELETE, phiên đăng nhập và CSRF

## Cài đặt
1. Sao lưu dự án và xuất database nếu có dữ liệu quan trọng.
2. Giải nén **nội dung** ZIP vào `C:\xampp\htdocs\MVC DEMO`, cho phép ghi đè các file cũ.
3. Trong XAMPP bật **Apache** và **MySQL**.
4. Trong phpMyAdmin chọn SQL, chạy `database_upgrade.sql` để tạo bảng `users`. Không chạy lại `schema.sql` nếu đã có dữ liệu bệnh nhân.
5. Giữ nguyên `config/database.php` đang kết nối MySQL; nếu thiếu, sao chép `config/database.example.php` thành `config/database.php` và chỉnh các thông số.
6. Mở VS Code Terminal (PowerShell) tại thư mục dự án, chạy:

   ```powershell
   & "C:\xampp\php\php.exe" tools\create_user.php
   ```

   Nhập tên đăng nhập và mật khẩu tối thiểu 10 ký tự (mật khẩu sẽ hiển thị trong cửa sổ terminal). Chỉ tạo tài khoản demo, không dùng mật khẩu thật.
7. Mở `http://localhost/MVC%20DEMO/public/index.php?action=login` và đăng nhập.

## API
Nếu Apache bật mod_rewrite và cho phép `.htaccess`, URL nền là:
`http://localhost/MVC%20DEMO/public/api`

Nếu bị 404 do Rewrite chưa hoạt động, có thể dùng URL tương đương: `http://localhost/MVC%20DEMO/public/index.php?route=api/...` (kèm `&q=...` thay cho `?q=...`).

| Method | Path | Ý nghĩa |
|---|---|---|
| GET | /csrf | Lấy CSRF token cho phiên |
| POST | /login | Đăng nhập bằng JSON |
| POST | /logout | Đăng xuất |
| GET | /patients?q=An&year=1995&page=1&per_page=10 | Danh sách, tìm kiếm, lọc, phân trang |
| GET | /patients/1 | Chi tiết |
| POST | /patients | Tạo |
| PUT | /patients/1 | Sửa |
| DELETE | /patients/1 | Xóa |

**Postman:**
1. Gửi `GET /api/csrf`, lấy `csrf_token`; giữ cookie PHP session do Postman lưu.
2. Gửi `POST /api/login` với header `X-CSRF-Token: <token>`, `Content-Type: application/json` và body:
   ```json
   {"username":"TEN_TAI_KHOAN","password":"MAT_KHAU_BAN_DA_TAO"}
   ```
3. Gửi `GET /api/patients` để xem danh sách (nhớ giữ cookie cùng phiên).
4. Khi POST/PUT/DELETE, thêm header `X-CSRF-Token` và `Content-Type: application/json` với body (cho POST/PUT):
   ```json
   {"name":"Nguyễn Văn A","birth_year":1995}
   ```
5. `POST /api/logout` với header `X-CSRF-Token` để thoát.

**Lưu ý:** API dùng Session nên là API JSON phục vụ học MVC; không phải REST stateless với token chuyên dụng. Chưa có rate limiting, logging kiểm toán, RBAC, transaction nghiệp vụ, tests tự động. Không đưa vào production nếu chưa bổ sung.

## GitHub
```powershell
cd "C:\xampp\htdocs\MVC DEMO"
git status
git add app public tools config/database.example.php database_upgrade.sql README_UPGRADE.md
git commit -m "feat: MVC CRUD search pagination auth REST API"
git push origin main
```

Thư mục `config/database.php` nằm trong `.gitignore` và không được upload lên GitHub.
