# trainee006
- Bài tập training thứ 6

# Mô tả
Khách hàng cần 1 chương trình chạy trên nền web với các tính năng sau:
- Hệ thống lưu trữ profile của các thành viên trong 1 nhóm hội. Có thể xem profile public, có thể đăng nhập để chỉnh sửa các thông tin của mình trên hệ thống.
- Người dùng hệ thống này chia làm 4 loại:
- Khách (guest) : là người bất kỳ truy cập vào trang web. Khách có thể truy cập để xem profile của các user trên hệ thống.
- Người dùng (user) : là khách sau khi đăng ký hệ thống thông qua tính năng Đăng ký.
- Quản lý (admod) : là người dùng có quyền chỉnh sửa thông tin của toàn bộ các user khác ngoại trừ các admod khác và administrator.
- Quản trị (administrator) : là người dùng có quyền chỉnh sửa thông tin của tất cả những người khác, có quyền nâng cấp user lên admod và ngược lại. Administrator là duy nhất.

# Yêu cầu chi tiết:
- Đăng ký : Bất kỳ ai cũng có thể đăng ký với các thông tin sau
- Tên đầy đủ (bắt buộc).
- Tên đăng nhập (bắt buộc, duy nhất).
- Email (bắt buộc, duy nhất).
- Phone (bắt buộc, duy nhất).
- Ảnh đại diện (không bắt buộc).
- Mật khẩu (bắt buộc, nhập 2 lần giống nhau).
- Đăng nhập : đăng nhập bằng 2 thông tin Email và Mật khẩu.
- Chỉnh sửa thông tin : Có thể sửa lại toàn bộ các thông tin như trên phần đăng ký, nhưng toàn bộ các trường đều là bắt buộc. Ngoài ra thêm 1 trường Mô tả để mô tả qua về bản thân, ngày sinh, giới tính. user chỉ có thể chỉnh sửa thông tin của mình, admod có thể chỉnh sửa của mình và các user, administrator có thể chỉnh sửa thông tin của bất kỳ ai.
- Xem chi tiết người dùng : hiển thị chi tiết của 1 thành viên, khách cũng có thể xem.
- Danh sách user : danh sách toàn bộ user(đây cũng chính là trang chủ, trang mặc định khi người dùng truy cập trang web). Đối với admod, administrator thì có nút sửa, xóa để sửa thông tin người được chọn hoặc xóa người được chọn khỏi hệ thống.

# Level 1:
- Thực hiện các yêu cầu trên với tiêu chí:
- Sử dụng HTML, CSS, JS làm giao diện (không dùng framework).
- Sử dụng PHP làm ngôn ngữ logic ở backend (không dùng framework, libs ngoài).
- Sử dụng MySQL để lưu trữ dữ liệu.
- Danh sách user sử dụng phân trang với mỗi trang là 10 người. Hiển thị thông tin: Ảnh đại diện, Tên đầy đủ, Giới thiệu, Ngày tham gia.

# Level 2:
- Thỏa mãn tiêu chí level 1 và:
- Danh sách user không dùng table. Thêm lựa chọn hiển thị 10,20,50,100 user trên một trang. Có thể sắp xếp theo tên hoặc ngày tham gia.
- Đăng nhập bằng email hoặc username đều được.
- Nếu là khách, khi xem trang chi tiết 1 người dùng thì chỉ hiển thị tên đầy đủ và ảnh. Kèm theo thống báo đăng nhập để xem đầy đủ.

# Level 3:
- Thỏa mãn tiêu chí level 2 và:
- Thêm 1 trường khi đăng ký là Người giới thiệu, trường này là tên đăng nhập hoặc email của 1 người đã có trên hệ thống.
- Trong trang chi tiết của 1 user sẽ hiển thị những người được giới thiệu bởi user này.
- Khi đăng nhập, nếu trong 1 phút đăng nhập sai 3 lần thì khóa 2 tiếng mới cho đăng nhập tiếp.
