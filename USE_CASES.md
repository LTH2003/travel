# SƠ ĐỒ USE CASE - ỨNG DỤNG TRAVEL APP

## 📋 Tổng Quan

Sơ đồ Use Case mô tả toàn bộ các tương tác của **Khách Hàng (Customer)** với hệ thống Travel App, từ đăng nhập cho đến booking tour và đánh giá dịch vụ.

---

## 🎯 ROLE KHÁCH HÀNG - MÔ TẢ TỔNG QUÁT

### Khách Hàng Là Ai?

**Khách Hàng (Customer/User)** là bất kỳ người dùng nào muốn sử dụng ứng dụng Travel App để:
- Tìm kiếm và xem thông tin tours du lịch
- Tìm kiếm và xem thông tin khách sạn
- Đặt tours và phòng khách sạn
- Quản lý các đặt chỗ của mình
- Đánh giá và nhận xét dịch vụ

### Khách Hàng Có Những Quyền Lợi Gì?

| 🔑 Quyền | 📝 Mô Tả |
|---------|---------|
| **Xem Danh Sách** | Xem tất cả tours và khách sạn có sẵn trong hệ thống |
| **Tìm Kiếm** | Tìm kiếm tours/khách sạn theo nhiều tiêu chí (giá, vị trí, đánh giá, etc.) |
| **Xem Chi Tiết** | Xem chi tiết hoàn chỉnh của từng tour/khách sạn (hình ảnh, giá, thông tin chi tiết) |
| **Lưu Yêu Thích** | Lưu tours/khách sạn vào danh sách yêu thích để xem lại sau |
| **Đặt Booking** | Đặt tours và phòng khách sạn qua giỏ hàng |
| **Thanh Toán** | Thanh toán booking qua nhiều phương thức (thẻ, ví điện tử, ngân hàng) |
| **Quản Lý Đặt Chỗ** | Xem, sửa, hủy các booking đã thực hiện |
| **Tải Tài Liệu** | Tải xác nhận, QR code, hóa đơn của booking |
| **Đánh Giá** | Viết đánh giá, xếp hạng sao cho tours/khách sạn |
| **Hỗ Trợ** | Liên hệ với nhân viên hỗ trợ nếu có vấn đề |

### Khách Hàng Phải Làm Những Gì?

| ✅ Trách Vụ | 📝 Mô Tả |
|-----------|---------|
| **Đăng Ký Tài Khoản** | Tạo tài khoản với email hợp lệ và mật khẩu mạnh |
| **Xác Minh Email** | Xác minh email để kích hoạt tài khoản |
| **Đăng Nhập An Toàn** | Sử dụng mã OTP để đăng nhập an toàn |
| **Cung Cấp Thông Tin** | Cung cấp thông tin cá nhân, liên hệ chính xác khi booking |
| **Thanh Toán Đủ** | Thanh toán đầy đủ trước khi booking được xác nhận |
| **Tuân Thủ Chính Sách** | Tuân thủ chính sách hủy, đổi lịch của ứng dụng |
| **Không Spam** | Không tạo spam, không lạm dụng hệ thống |
| **Đánh Giá Công Bằng** | Viết đánh giá chân thực, không phỉ báng hoặc quảng cáo |

### Khách Hàng Thường Làm Những Gì? (Luồng Chính)

```
1️⃣ GIAI ĐOẠN KHÁM PHÁ
   └─ Truy cập ứng dụng → Duyệt tours/khách sạn → Xem chi tiết
   
2️⃣ GIAI ĐOẠN YÊU THÍCH
   └─ Lưu vào danh sách yêu thích → Xem lại → So sánh
   
3️⃣ GIAI ĐOẠN CHỌN MUA
   └─ Chọn sản phẩm → Thêm vào giỏ hàng → Xem giỏ hàng
   
4️⃣ GIAI ĐOẠN THANH TOÁN
   └─ Chọn ngày/điểm khởi hành → Nhập thông tin → Chọn cách thanh toán → Xác nhận
   
5️⃣ GIAI ĐOẠN HOÀN TẤT
   └─ Nhận xác nhận → Tải QR code → Nhận email
   
6️⃣ GIAI ĐOẠN SAU BOOKING
   └─ Xem lịch sử booking → Đánh giá → Liên hệ hỗ trợ nếu cần
```

### Các Tình Huống Thường Gặp

**Tình Huống 1: Khách hàng đầu tiên**
```
Bước 1: Truy cập website
Bước 2: Đăng ký tài khoản mới
Bước 3: Xác minh email
Bước 4: Đăng nhập lần đầu
Bước 5: Xem danh sách tours
Bước 6: Chọn tour yêu thích
Bước 7: Đặt booking
Bước 8: Thanh toán
Bước 9: Nhận xác nhận
```

**Tình Huống 2: Khách hàng lâu năm**
```
Bước 1: Đăng nhập bằng tài khoản sẵn có
Bước 2: Xem danh sách yêu thích
Bước 3: Chọn tour muốn book thêm
Bước 4: Thêm vào giỏ hàng
Bước 5: Thanh toán nhanh (lưu thông tin cũ)
Bước 6: Xem booking mới trong lịch sử
Bước 7: Đánh giá tour vừa kết thúc
```

**Tình Huống 3: Khách hàng cần hỗ trợ**
```
Bước 1: Xem lịch sử booking
Bước 2: Tìm thấy booking có vấn đề
Bước 3: Liên hệ qua hotline hoặc form
Bước 4: Nói chuyện với nhân viên hỗ trợ
Bước 5: Giải quyết vấn đề (hủy, đổi lịch, hoàn tiền)
```

### Các Hạn Chế / Quy Tắc

⚠️ **Khách hàng KHÔNG thể:**
- Chỉnh sửa thông tin sản phẩm (tours/khách sạn)
- Xóa hoặc ẩn đánh giá của mình sau khi gửi (Admin duyệt)
- Booking quá số lượng available
- Thanh toán không đủ
- Hủy booking sau khi chuyến tour/khách sạn đã bắt đầu
- Truy cập dữ liệu của khách hàng khác

✅ **Khách hàng CÓ thể:**
- Chỉnh sửa hồ sơ cá nhân bất cứ lúc nào
- Xem tất cả đánh giá từ khách hàng khác
- Hủy booking trước ngày khởi hành (có phí)
- Đơn giản hóa quá trình thanh toán bằng cách lưu thông tin

---

---

## 🎨 SƠ ĐỒ USE CASE FORMAL

### Sơ Đồ Tổng Quát - Khách Hàng & Các Use Case Chính

```
                            Tìm kiếm tour/khách sạn
                                   ▲
                                   │
                                  ╱ ╲
                                 ╱   ╲
                                ╱     ╲
                               │       │
                               │       │
          ┌─────────────────────┤       ├────────────────────┐
          │                     │       │                    │
          │                     │       │                    │
     ┌────▼────────┐       ┌────▼──┐ ┌──▼──────┐      ┌──────▼────┐
     │ Đặt lịch    │       │       │ │         │      │Biến thông  │
     │ khám phá    │       │   A   │ │    B    │      │tin liên hệ │
     │             │       │       │ │         │      │            │
     └─────────────┘       └───────┘ └─────────┘      └────────────┘
                                  ▲
                                  │
                                  │
                          ┌───────┴────────┐
                          │                │
                    ┌─────▼─────┐    ┌─────▼─────┐
                    │           │    │           │
                    │ Khách hàng │    │ Đánh giá  │
                    │           │    │           │
                    └───────────┘    └───────────┘
                          ▲
                          │
                    ┌─────┴─────┐
                    │           │
              ┌─────▼──┐   ┌─────▼──┐
              │        │   │        │
              │  Use   │   │ Quản lý│
              │        │   │ tài    │
              │        │   │ khoản  │
              └────────┘   └────────┘
```

### Sơ Đồ Chi Tiết - Tất Cả Use Cases

```
                                  ┌─────────────────────┐
                                  │ Tìm kiếm khách sạn  │
                                  └────────────────────┬┘
                                                       │
                  ┌──────────────────────────────────┐ │ ┌──────────────────────┐
                  │                                  │ │ │ Biến thông tin       │
                  │                                  │ │ │ liên hệ              │
         ┌────────▼────────┐                        │ │ │                      │
         │                 │                        │ │ └──────────────────────┘
    ┌────▼────┐   ┌────────▼────┐         ┌────────┴┐ │
    │ Tìm      │   │ Xem chi tiết│         │ Đặt     │ │
    │ kiếm     │   │ tour        │         │ lịch    │ │
    │ tour     │   │             │         │ khám    │ │
    │          │   └────────┬────┘         │ phá     │ │
    └────▲─────┘            │              │         │ │
         │                  │              └────┬────┘ │
         │       ┌──────────┴─────────┐          │      │
         │       │                    │          │      │
    ┌────┴───────▼─┐        ┌────────▼────┐    │      │
    │              │        │             │    │      │
    │  Khách hàng  ├────────▶  Thêm vào   │    │      │
    │              │        │  yêu thích  │    │      │
    └────┬─────────┘        │             │    │      │
         │                  └────────┬────┘    │      │
         │                           │        │      │
         │         ┌─────────────────┴────┐   │      │
         │         │                      │   │      │
    ┌────▼─────┐  ┌▼──────────┐   ┌──────▼─┐ │      │
    │           │  │           │   │        │ │      │
    │ Quản lý   │  │ Xem giỏ   │   │ Thanh  │ │      │
    │ tài khoản ├──▶ hàng      ├──▶  toán  ├─┘      │
    │           │  │           │   │        │        │
    └──────┬────┘  └──────┬────┘   └────┬───┘        │
           │              │             │            │
           │              │      ┌──────▼──────┐     │
           │              │      │             │     │
      ┌────▼────┐         │      │  Nhập       │     │
      │          │         │      │  thông tin  │     │
      │ Thay đổi │         │      │  khách      │     │
      │ mật khẩu │         │      │             │     │
      │          │         │      └──────┬──────┘     │
      └──────────┘         │             │            │
                           │      ┌──────▼──────┐     │
                           │      │             │     │
                           │      │ Chọn cách   │     │
                           │      │ thanh toán  │     │
                           │      │             │     │
                           │      └──────┬──────┘     │
                           │             │            │
                           │      ┌──────▼──────┐     │
                           └─────▶│             │     │
                                  │ Xác nhận    │◀────┘
                                  │ booking     │
                                  │             │
                                  └──────┬──────┘
                                         │
                           ┌─────────────┼─────────────┐
                           │             │             │
                      ┌────▼───┐   ┌─────▼───┐  ┌──────▼──┐
                      │ Xem    │   │ Hủy     │  │ Đánh    │
                      │ lịch sử│   │ booking │  │ giá     │
                      │ booking│   │         │  │         │
                      └────────┘   └─────────┘  └─────────┘
```

### Sơ Đồ Biểu Diễn Từng Nhóm Use Case

#### **Nhóm 1: Xác Thực & Quản Lý Tài Khoản**

```
                      ┌──────────────────┐
                      │  Đăng ký tài     │
                      │  khoản           │
                      └────────────────┬─┘
                                       │
                  ┌────────────────────┘ └────────────────────┐
                  │                                           │
         ┌────────▼────────┐                         ┌────────▼─────┐
         │                 │                         │              │
         │  Xác minh email  │                         │  Đăng nhập   │
         │                 │                         │  (2FA/OTP)   │
         └────────┬────────┘                         └────────┬─────┘
                  │                                           │
                  └────────────────────┬────────────────────┬─┘
                                       │
                        ┌──────────────┴──────────────┐
                        │                             │
                   ┌────▼────┐             ┌──────────▼───┐
                   │          │             │              │
                   │ Cập nhật │             │ Đổi mật      │
                   │ hồ sơ    │             │ khẩu         │
                   │          │             │              │
                   └──────────┘             └──────────────┘
                        │                             │
                        │      ┌──────────────┐      │
                        └─────▶│              │◀─────┘
                               │ Khách hàng   │
                               │              │
                               └──────────────┘
```

#### **Nhóm 2: Duyệt & Tìm Kiếm Sản Phẩm**

```
                      ┌──────────────────────┐
                      │ Tìm kiếm tour/khách  │
                      │ sạn                  │
                      └────────────────┬─────┘
                                       │
                     ┌─────────────────┴─────────────────┐
                     │                                   │
         ┌───────────▼──────────┐        ┌──────────────▼──────┐
         │                      │        │                     │
         │ Duyệt danh sách      │        │ Xem chi tiết tour   │
         │ tours                │        │                     │
         └───────────┬──────────┘        └──────────────┬──────┘
                     │                                  │
                     │       ┌──────────────────┐       │
                     └──────▶│                  │◀──────┘
                            │ Khách hàng      │
                            │                  │
                            └──────────────────┘
                                      │
                    ┌─────────────────┘ └──────────────────┐
                    │                                      │
         ┌──────────▼────────┐                   ┌─────────▼────────┐
         │                   │                   │                  │
         │ Thêm vào yêu      │                   │ Xem chi tiết     │
         │ thích             │                   │ khách sạn        │
         │                   │                   │                  │
         └───────────────────┘                   └──────────────────┘
```

#### **Nhóm 3: Quản Lý Giỏ Hàng & Booking**

```
                    ┌────────────────────┐
                    │ Thêm vào giỏ hàng  │
                    └────────────┬───────┘
                                 │
                    ┌────────────▼────────────┐
                    │                        │
         ┌──────────▼────────┐      ┌────────▼───────┐
         │                   │      │                │
         │ Xem giỏ hàng      │      │ Cập nhật số    │
         │                   │      │ lượng          │
         └──────────┬────────┘      │                │
                    │               └────────┬───────┘
                    └───────────┬────────────┘
                                │
                    ┌───────────▼───────────┐
                    │                       │
         ┌──────────▼────────┐   ┌──────────▼────────┐
         │                   │   │                   │
         │ Xóa khỏi giỏ      │   │ Thanh toán        │
         │                   │   │ (Checkout)        │
         └───────────────────┘   └──────────┬────────┘
                                            │
                    ┌───────────────────────┴────────────────┐
                    │                                        │
         ┌──────────▼────────────┐            ┌─────────────▼──────┐
         │                       │            │                    │
         │ Nhập thông tin khách  │            │ Nhập thông tin     │
         │                       │            │ liên hệ            │
         └──────────┬────────────┘            │                    │
                    │                         └─────────────┬──────┘
                    └────────────┬────────────────────────┬─┘
                                 │
                    ┌────────────▼────────────┐
                    │                        │
                    │ Chọn cách thanh toán   │
                    │                        │
                    └───────────┬────────────┘
                                │
                    ┌───────────▼───────────┐
                    │                       │
                    │ Xác nhận booking      │
                    │                       │
                    └───────────┬───────────┘
                                │
             ┌──────────────────┴──────────────────┐
             │                                     │
        ┌────▼────┐                      ┌─────────▼────┐
        │          │                      │              │
        │ Tạo mã   │                      │ Gửi email    │
        │ QR Code  │                      │ xác nhận     │
        │          │                      │              │
        └──────────┘                      └──────────────┘
```

#### **Nhóm 4: Quản Lý Lịch Sử & Đánh Giá**

```
                      ┌──────────────────────┐
                      │ Xem lịch sử booking  │
                      └────────────┬─────────┘
                                   │
                 ┌─────────────────┴─────────────────┐
                 │                                   │
         ┌───────▼──────────┐         ┌──────────────▼───────┐
         │                  │         │                      │
         │ Xem chi tiết     │         │ Tải xác nhận/QR      │
         │ booking          │         │                      │
         └────────┬─────────┘         └──────────────┬───────┘
                  │                                  │
                  │      ┌─────────────────────┐     │
                  └─────▶│                     │◀────┘
                         │ Khách hàng         │
                         │                     │
                         └──────────┬──────────┘
                                    │
              ┌─────────────────────┴─────────────────────┐
              │                                           │
        ┌─────▼────────┐                      ┌──────────▼────┐
        │              │                      │               │
        │ Hủy booking  │                      │ Đánh giá tour │
        │              │                      │               │
        └──────────────┘                      └──────────────┘
```

#### **Nhóm 5: Hỗ Trợ Khách Hàng**

```
                 ┌──────────────────────┐
                 │ Liên hệ hỗ trợ       │
                 └──────────┬───────────┘
                            │
            ┌───────────────┼───────────────┐
            │               │               │
      ┌─────▼─────┐  ┌──────▼──────┐  ┌───▼──────┐
      │            │  │             │  │          │
      │ Gọi hotline│  │ Gửi tin nhắn│  │ Chat trực│
      │            │  │             │  │ tiếp     │
      └─────┬──────┘  └──────┬──────┘  └───┬──────┘
            │                │             │
            └────────────────┼─────────────┘
                             │
                    ┌────────▼────────┐
                    │                 │
                    │ Nhân viên hỗ trợ│
                    │                 │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │                 │
                    │ Giải quyết vấn đề│
                    │                 │
                    └─────────────────┘
```

---

### 1️⃣ XÁC THỰC VÀ QUẢN LÝ TÀI KHOẢN

```
┌─────────────────────────────────────────────────────────┐
│     Xác Thực và Quản Lý Tài Khoản                       │
└─────────────────────────────────────────────────────────┘

    Khách Hàng
        │
    ┌───┴───────────────────────────────┐
    ▼                                   ▼
┌──────────────┐              ┌─────────────────────┐
│ Đăng Ký      │              │ Đăng Nhập           │
│ Tài Khoản    │              │ (Xác thực 2 lớp)    │
└──────────────┘              │ - Email             │
    │                         │ - Mật khẩu          │
    │                         │ - Mã OTP            │
    │                         └─────────────────────┘
    │                                  │
    ▼                                  ▼
┌──────────────┐                  ┌─────────────────┐
│ Kiểm tra     │                  │ Xác thực OTP    │
│ Email        │                  │ qua SMS         │
│ Xác minh     │                  └─────────────────┘
│ Tài khoản    │                          │
└──────────────┘                          ▼
    │                          ┌─────────────────────┐
    │                          │ Đăng nhập thành     │
    │                          │ công               │
    │                          └─────────────────────┘
    │                                  │
    └──────────────┬───────────────────┘
                   ▼
        ┌──────────────────────────┐
        │ Vào Dashboard Khách      │
        │ Hàng                     │
        └──────────────────────────┘
                   │
    ┌──────────────┼──────────────┐
    ▼              ▼              ▼
┌──────────┐  ┌───────────┐  ┌──────────────┐
│Cập nhật  │  │Đổi mật    │  │Đăng xuất     │
│Hồ sơ    │  │khẩu       │  │(Logout)      │
└──────────┘  └───────────┘  └──────────────┘
```

**Chi tiết các hoạt động:**
- ✅ Đăng ký tài khoản mới với email/mật khẩu
- ✅ Xác thực email để kích hoạt tài khoản
- ✅ Đăng nhập với 2FA (Mã OTP gửi qua SMS)
- ✅ Cập nhật thông tin hồ sơ cá nhân
- ✅ Đổi mật khẩu
- ✅ Đăng xuất khỏi hệ thống

---

### 2️⃣ DUYỆT VÀ TÌM KIẾM SẢN PHẨM

```
┌─────────────────────────────────────────────────────┐
│    Duyệt và Tìm Kiếm Tour/Khách Sạn                 │
└─────────────────────────────────────────────────────┘

    Khách Hàng
        │
    ┌───┴──────────────────────────────────┐
    ▼                                      ▼
┌─────────────────┐              ┌──────────────────┐
│ Tìm Kiếm Tour   │              │ Duyệt Khách Sạn  │
└─────────────────┘              └──────────────────┘
    │                                    │
    ├─ Theo từ khóa                     ├─ Tất cả khách sạn
    ├─ Theo điểm đến                    ├─ Theo vị trí
    ├─ Theo giá tiền                    ├─ Theo xếp hạng
    ├─ Theo rating                      └─ Theo loại phòng
    └─ Theo ngày khởi hành
        │
        ▼
    ┌──────────────────────┐
    │ Xem Danh Sách Kết    │
    │ Quả Tìm Kiếm         │
    └──────────────────────┘
        │
        ▼
    ┌──────────────────────────────────────┐
    │ Xem Chi Tiết Sản Phẩm                │
    │ (Tour/Khách Sạn)                     │
    └──────────────────────────────────────┘
        │
    ┌───┴──────────────────────────────────┐
    ▼                                      ▼
┌──────────────────────┐        ┌────────────────────────┐
│ Chi Tiết Tour        │        │ Chi Tiết Khách Sạn     │
├──────────────────────┤        ├────────────────────────┤
│ - Hình ảnh           │        │ - Hình ảnh             │
│ - Mô tả tour         │        │ - Mô tả cơ sở          │
│ - Điểm khởi hành     │        │ - Tiện ích             │
│ - Lịch trình (Day)   │        │ - Loại phòng           │
│ - Điểm nổi bật       │        │ - Giá phòng            │
│ - Bao gồm (Include)  │        │ - Đánh giá             │
│ - Giá tour           │        │ - Vị trí               │
│ - Xếp hạng sao       │        │ - Liên hệ              │
│ - Đánh giá của KH    │        │ - Đánh giá của KH      │
└──────────────────────┘        └────────────────────────┘
```

**Chi tiết các hoạt động:**
- ✅ Tìm kiếm tour theo tiêu chí khác nhau
- ✅ Duyệt danh sách tour/khách sạn
- ✅ Xem chi tiết với hình ảnh, mô tả, giá
- ✅ Xem lịch trình, điểm nổi bật, dịch vụ bao gồm
- ✅ Xem đánh giá và xếp hạng từ khách hàng khác

---

### 3️⃣ QUẢN LÝ DANH SÁCH YÊU THÍCH

```
┌──────────────────────────────────────────┐
│    Quản Lý Danh Sách Yêu Thích           │
└──────────────────────────────────────────┘

    Khách Hàng
        │
        ▼
    ┌────────────────────┐
    │ Đang xem Tour/     │
    │ Khách Sạn          │
    └────────────────────┘
        │
    ┌───┴─────────────────────────┐
    ▼                             ▼
┌──────────────────┐      ┌──────────────────┐
│ Nhấn Nút Trái    │      │ Nhấn Nút Trái    │
│ Tim (Favorite)   │      │ Tim (Bỏ yêu      │
│ để Thêm Tour     │      │ thích) để Xóa    │
└──────────────────┘      └──────────────────┘
    │                             │
    ▼                             ▼
┌──────────────────┐      ┌──────────────────┐
│ Tour Được Thêm   │      │ Tour Bị Xóa      │
│ vào Favorites    │      │ khỏi Favorites   │
│ (Lưu Database)   │      │ (Xóa Database)   │
└──────────────────┘      └──────────────────┘
    │                             │
    └──────────────┬──────────────┘
                   ▼
        ┌──────────────────────┐
        │ Xem Trang Yêu Thích  │
        └──────────────────────┘
                   │
        ┌──────────┴──────────┐
        ▼                     ▼
    ┌──────────┐        ┌──────────┐
    │Tour Yêu  │        │Khách Sạn │
    │Thích     │        │Yêu Thích │
    └──────────┘        └──────────┘
        │                   │
        └──────────┬────────┘
                   ▼
        ┌──────────────────────┐
        │ Quản Lý Yêu Thích:   │
        │ - Xem danh sách      │
        │ - So sánh các item   │
        │ - Xóa khỏi yêu thích │
        │ - Thêm vào giỏ hàng  │
        └──────────────────────┘
```

**Chi tiết các hoạt động:**
- ✅ Thêm tour/khách sạn vào danh sách yêu thích
- ✅ Xóa khỏi danh sách yêu thích
- ✅ Xem toàn bộ yêu thích
- ✅ So sánh các tour/khách sạn yêu thích
- ✅ Thêm trực tiếp từ yêu thích vào giỏ hàng

---

### 4️⃣ QUẢN LÝ GIỎ HÀNG

```
┌──────────────────────────────────────────┐
│    Quản Lý Giỏ Hàng                      │
└──────────────────────────────────────────┘

    Khách Hàng
        │
        ▼
    ┌────────────────────┐
    │ Xem Chi Tiết       │
    │ Tour/Khách Sạn     │
    └────────────────────┘
        │
        ▼
    ┌────────────────────────┐
    │ Nhấn "Thêm vào Giỏ"    │
    └────────────────────────┘
        │
        ▼
    ┌────────────────────────────────────┐
    │ Giỏ Hàng Được Cập Nhật             │
    │ (Số lượng item tăng)               │
    └────────────────────────────────────┘
        │
        ▼
    ┌────────────────────────────────────┐
    │ Xem Giỏ Hàng                       │
    └────────────────────────────────────┘
        │
    ┌───┴────────────────────────────────┐
    │                                    │
    ▼                                    ▼
┌──────────────────┐            ┌────────────────────┐
│ Danh Sách Tour   │            │ Danh Sách Khách    │
│ trong Giỏ        │            │ Sạn trong Giỏ      │
├──────────────────┤            ├────────────────────┤
│ - Tên tour       │            │ - Tên khách sạn    │
│ - Ngày khởi      │            │ - Loại phòng       │
│   hành           │            │ - Check-in/out     │
│ - Số khách       │            │ - Số phòng         │
│ - Giá/người      │            │ - Giá/phòng        │
│ - Tổng giá       │            │ - Tổng giá         │
└──────────────────┘            └────────────────────┘
        │                               │
        └───────────────┬───────────────┘
                        ▼
                ┌──────────────────────┐
                │ Tổng Cộng Giỏ Hàng:  │
                │ XXX.XXX VND          │
                └──────────────────────┘
                        │
        ┌───────────────┼───────────────┐
        ▼               ▼               ▼
    ┌────────┐    ┌──────────┐    ┌──────────┐
    │Cập     │    │Xóa Sản   │    │Thanh     │
    │Nhật    │    │Phẩm      │    │Toán      │
    │Số      │    │khỏi Giỏ  │    │(Checkout)│
    │Lượng   │    │          │    │          │
    └────────┘    └──────────┘    └──────────┘
        │               │              │
        └───────────────┴──────────────┘
                        │
                        ▼
            ┌──────────────────────┐
            │ Cập Nhật Giỏ Hàng    │
            │ (Lưu vào Browser)    │
            └──────────────────────┘
```

**Chi tiết các hoạt động:**
- ✅ Thêm tour/khách sạn vào giỏ hàng
- ✅ Xem danh sách sản phẩm trong giỏ
- ✅ Cập nhật số lượng/ngày ở lại
- ✅ Xóa sản phẩm khỏi giỏ
- ✅ Tính tổng tiền
- ✅ Tiến hành thanh toán

---

### 5️⃣ QUẢN LÝ BOOKING (ĐẶT TOUR/KHÁCH SẠN)

```
┌──────────────────────────────────────────┐
│    Booking - Đặt Tour/Khách Sạn          │
└──────────────────────────────────────────┘

    Khách Hàng (Trong Giỏ Hàng)
        │
        ▼
    ┌────────────────────┐
    │ Nhấn "Thanh Toán"  │
    └────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ BƯỚC 1: Chọn Ngày & Điểm Khởi    │
    │ Hành                             │
    ├──────────────────────────────────┤
    │ - Chọn ngày khởi hành            │
    │ - Chọn điểm khởi hành            │
    │ - Xác nhận số khách/phòng        │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ BƯỚC 2: Nhập Thông Tin Khách     │
    │ Hàng                             │
    ├──────────────────────────────────┤
    │ - Họ tên                         │
    │ - Ngày sinh                      │
    │ - Giới tính                      │
    │ - Số CMND/Passport               │
    │ - Quốc tịch                      │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ BƯỚC 3: Nhập Thông Tin Liên Hệ  │
    ├──────────────────────────────────┤
    │ - Email                          │
    │ - Số điện thoại                  │
    │ - Địa chỉ                       │
    │ - Thành phố                      │
    │ - Quốc gia                       │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ BƯỚC 4: Chọn Phương Thức Thanh   │
    │ Toán                             │
    ├──────────────────────────────────┤
    │ - Thẻ tín dụng/Ghi nợ            │
    │ - Ví điện tử                     │
    │ - Chuyển khoản ngân hàng         │
    │ - Thanh toán tại quầy            │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ BƯỚC 5: Xem Lại Thông Tin        │
    │ Booking                          │
    ├──────────────────────────────────┤
    │ - Tour/Khách sạn                 │
    │ - Ngày khởi hành                 │
    │ - Điểm khởi hành                 │
    │ - Thông tin khách                │
    │ - Tổng tiền                      │
    │ - Phí dịch vụ                    │
    │ - Tổng thanh toán                │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ Nhấn "Xác Nhận Booking"           │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ Hệ Thống Xử Lý:                  │
    │ - Kiểm tra thông tin             │
    │ - Xử lý thanh toán               │
    │ - Tạo mã Booking                 │
    │ - Tạo mã QR Code                 │
    │ - Gửi email xác nhận             │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ TRANG "BOOKING THÀNH CÔNG"       │
    ├──────────────────────────────────┤
    │ ✅ Mã Booking: BOOK2024-001      │
    │ ✅ Ngày Booking                  │
    │ ✅ Mã QR Code                    │
    │ ✅ Các nút tải:                  │
    │    - Download Xác nhận (PDF)     │
    │    - Download QR Code            │
    │    - Download Hóa đơn            │
    │ ✅ Liên kết:                     │
    │    - Xem chi tiết booking        │
    │    - Quay lại trang chủ          │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ Email Xác Nhận Gửi đến Khách     │
    ├──────────────────────────────────┤
    │ - Mã booking                     │
    │ - Chi tiết tour/khách sạn        │
    │ - Ngày khởi hành                 │
    │ - Thông tin liên hệ              │
    │ - Mã QR Code                     │
    │ - Link tải xác nhận              │
    └──────────────────────────────────┘
```

**Chi tiết các hoạt động:**
- ✅ Chọn ngày khởi hành và điểm khởi hành
- ✅ Nhập thông tin khách hàng (Họ tên, CMND, Passport)
- ✅ Nhập thông tin liên hệ (Email, SDT, Địa chỉ)
- ✅ Chọn phương thức thanh toán
- ✅ Xem lại toàn bộ thông tin trước khi xác nhận
- ✅ Xác nhận booking
- ✅ Tạo mã QR Code
- ✅ Gửi email xác nhận
- ✅ Tải xuống Xác nhận, QR Code, Hóa đơn

---

### 6️⃣ SAU BOOKING - QUẢN LÝ ĐƠN HÀNG

```
┌──────────────────────────────────────────┐
│    Sau Booking - Quản Lý Đơn Hàng        │
└──────────────────────────────────────────┘

    Khách Hàng
        │
        ▼
    ┌────────────────────────┐
    │ Vào Trang "Lịch Sử     │
    │ Mua Hàng"              │
    └────────────────────────┘
        │
        ▼
    ┌────────────────────────────────────┐
    │ Danh Sách Các Booking:             │
    ├────────────────────────────────────┤
    │ Booking #1                         │
    │ ├─ Tour: Hạ Long Bay 3N2Đ         │
    │ ├─ Ngày: 15/01/2025               │
    │ ├─ Trạng thái: Đã xác nhận        │
    │ ├─ Tổng tiền: 2.500.000 VND       │
    │ ├─ Nút: [Xem Chi Tiết]            │
    │ ├─ Nút: [Tải QR Code]             │
    │ ├─ Nút: [Hủy Booking]             │
    │ └─ Nút: [Đánh Giá]                │
    │                                    │
    │ Booking #2                         │
    │ ├─ Khách Sạn: Hilton Hà Nội       │
    │ ├─ Check-in: 20/01/2025           │
    │ ├─ Trạng thái: Đang xử lý         │
    │ ├─ Tổng tiền: 3.000.000 VND       │
    │ └─ Nút: [Xem Chi Tiết]            │
    └────────────────────────────────────┘
        │
    ┌───┴──────────────────────────────┐
    ▼                                  ▼
┌────────────────────────┐    ┌──────────────────────┐
│ Nút "Xem Chi Tiết"     │    │ Nút "Hủy Booking"    │
├────────────────────────┤    ├──────────────────────┤
│ - Mã Booking           │    │ Xác nhận hủy?        │
│ - Thông tin tour/ks    │    │ ├─ Tiền hoàn lại:    │
│ - Thông tin khách      │    │ │  2.250.000 VND     │
│ - Ngày khởi hành       │    │ │  (chiết khấu 10%)  │
│ - Điểm khởi hành       │    │ ├─ Lý do hủy         │
│ - Tổng tiền            │    │ └─ [Xác Nhận Hủy]    │
│ - Link tải xác nhận    │    └──────────────────────┘
│ - Link tải QR Code     │           │
└────────────────────────┘           ▼
        │                  ┌──────────────────────┐
        │                  │ Hủy Thành Công       │
        │                  │ - Email xác nhận hủy │
        │                  │ - Chi tiết hoàn lại  │
        │                  │ - Ngày ghi nhận      │
        │                  └──────────────────────┘
        │
        ▼
┌────────────────────────┐
│ Nút "Đánh Giá"         │
├────────────────────────┤
│ - Xếp hạng (1-5 sao)  │
│ - Viết nhận xét        │
│ - Lưu đánh giá         │
└────────────────────────┘
        │
        ▼
    ┌──────────────────┐
    │ Cảm ơn đánh giá  │
    └──────────────────┘
```

**Chi tiết các hoạt động:**
- ✅ Xem lịch sử tất cả booking
- ✅ Xem chi tiết từng booking (mã, thông tin, giá)
- ✅ Tải QR Code từ booking
- ✅ Hủy booking (với hoàn lại tiền)
- ✅ Đánh giá tour/khách sạn (sao + nhận xét)

---

### 7️⃣ ĐÁNH GIÁ VÀ XÉP HẠNG

```
┌──────────────────────────────────────────┐
│    Đánh Giá và Xếp Hạng                  │
└──────────────────────────────────────────┘

    Khách Hàng (Sau khi booking)
        │
        ▼
    ┌──────────────────────┐
    │ Vào Booking cũ       │
    │ nhấn "Đánh Giá"      │
    └──────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ Form Đánh Giá                    │
    ├──────────────────────────────────┤
    │ 1. Xếp Hạng Sao:                 │
    │    [⭐⭐⭐⭐☆] 4 sao             │
    │                                  │
    │ 2. Viết Nhận Xét:                │
    │    "Tour rất tuyệt vời! Hướng    │
    │    dẫn viên chuyên nghiệp, cơ    │
    │    sở sạch sẽ. Sẽ quay lại!"     │
    │                                  │
    │ 3. Tải Hình Ảnh:                 │
    │    [Chọn hình ảnh] (3 ảnh)       │
    │                                  │
    │ 4. Nút [Gửi Đánh Giá]            │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ Hệ Thống Xử Lý:                  │
    │ - Lưu đánh giá vào database      │
    │ - Cập nhật rating trung bình     │
    │ - Xác thực đánh giá              │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ ✅ Cảm ơn bạn đã đánh giá!       │
    │ - Đánh giá của bạn:              │
    │   ⭐⭐⭐⭐ (4 sao)             │
    │ - Bình luận sẽ được hiển thị      │
    │   sau khi kiểm duyệt             │
    └──────────────────────────────────┘
        │
        ▼
    ┌──────────────────────────────────┐
    │ Đánh Giá Hiển Thị Trên Sản Phẩm  │
    ├──────────────────────────────────┤
    │ Khách Hàng #123                  │
    │ ⭐⭐⭐⭐ (4 sao)              │
    │ "Tour rất tuyệt vời! Hướng..."   │
    │                                  │
    │ [Hình ảnh 1] [Hình ảnh 2]        │
    │                                  │
    │ Ngày: 15/01/2025                 │
    │ [Like] [Không Hữu Ích]           │
    └──────────────────────────────────┘
```

**Chi tiết các hoạt động:**
- ✅ Viết đánh giá cho tour/khách sạn đã booking
- ✅ Xếp hạng từ 1-5 sao
- ✅ Thêm bình luận chi tiết
- ✅ Tải hình ảnh minh họa
- ✅ Xem đánh giá từ khách hàng khác trên sản phẩm
- ✅ Like/Dislike đánh giá của người khác

---

### 8️⃣ HỖ TRỢ KHÁCH HÀNG

```
┌──────────────────────────────────────┐
│    Hỗ Trợ Khách Hàng                 │
└──────────────────────────────────────┘

    Khách Hàng
        │
    ┌───┴──────────────────────────────┐
    ▼                                  ▼
┌──────────────────┐        ┌──────────────────┐
│ Gọi Hotline      │        │ Gửi Tin Nhắn     │
│ Hỗ Trợ           │        │ Hỗ Trợ           │
├──────────────────┤        ├──────────────────┤
│ Số Điện Thoại:   │        │ Form Liên Hệ:    │
│ 0123 456 789     │        │ - Họ tên         │
│                  │        │ - Email          │
│ Giờ làm việc:    │        │ - Số điện thoại  │
│ 8:00 - 22:00     │        │ - Tiêu đề        │
│ (Hàng ngày)      │        │ - Nội dung       │
└──────────────────┘        │ - Loại vấn đề    │
        │                   │ - Tệp đính kèm   │
        ▼                   │ - [Gửi]          │
    ┌──────────────┐        └──────────────────┘
    │Tư vấn viên   │                │
    │Trả lời       │                ▼
    │ngay lập tức  │        ┌──────────────────┐
    │              │        │Hệ thống gửi      │
    │Xử lý vấn đề: │        │email xác nhận    │
    │- Booking     │        │Sẽ trả lời        │
    │- Thanh toán  │        │trong 24h         │
    │- Hủy, Đổi    │        └──────────────────┘
    │  Booking     │
    └──────────────┘
        │
    ┌───┴──────────┐
    ▼              ▼
┌────────┐    ┌──────────┐
│Vấn đề  │    │Gửi lại  │
│được    │    │thông tin │
│giải    │    │chi tiết  │
│quyết   │    │          │
└────────┘    └──────────┘
    │              │
    ▼              ▼
┌────────────────────────────┐
│ Khách hàng nhận kết quả    │
│ - Qua điện thoại           │
│ - Qua email                │
│ - Qua chat                 │
└────────────────────────────┘
```

**Chi tiết các hoạt động:**
- ✅ Gọi hotline hỗ trợ khách hàng
- ✅ Gửi tin nhắn qua form liên hệ
- ✅ Chat trực tiếp với tư vấn viên
- ✅ Báo cáo sự cố về booking
- ✅ Hỏi về chính sách hoàn hủy
- ✅ Nhận hỗ trợ từ nhân viên

---

## 📊 BIỂU ĐỒ LUỒNG TỔNG QUÁT

```
START (Khách hàng)
  │
  ├─ Chưa có tài khoản? → Đăng Ký
  │                        │
  │                        ▼
  │                    Xác Minh Email
  │                        │
  │                        ▼
  │                    Đăng Nhập 2FA
  │                        │
  ├────────────────────────┘
  │
  ▼
Trang Chủ (Dashboard)
  │
  ├─ Tìm Kiếm Tour/Khách Sạn
  │   │
  │   ├─ Xem Chi Tiết
  │   │   │
  │   │   ├─ Thêm vào Yêu Thích ⭐
  │   │   │
  │   │   └─ Thêm vào Giỏ Hàng 🛒
  │   │
  │   └─ Xem Danh Sách Kết Quả
  │
  ├─ Xem Yêu Thích
  │   │
  │   └─ Quản Lý Yêu Thích
  │       └─ Thêm vào Giỏ Hàng
  │
  ├─ Xem Giỏ Hàng
  │   │
  │   ├─ Cập Nhật Số Lượng
  │   ├─ Xóa Sản Phẩm
  │   │
  │   └─ Thanh Toán (Checkout)
  │       │
  │       ├─ Chọn Ngày & Điểm Khởi Hành
  │       ├─ Nhập Thông Tin Khách
  │       ├─ Nhập Thông Tin Liên Hệ
  │       ├─ Chọn Phương Thức Thanh Toán
  │       ├─ Xem Lại Thông Tin
  │       │
  │       └─ Xác Nhận Booking
  │           │
  │           ├─ Tạo Mã Booking
  │           ├─ Tạo Mã QR Code
  │           ├─ Gửi Email Xác Nhận
  │           │
  │           └─ Trang "Booking Thành Công"
  │               ├─ Tải Xác Nhận (PDF)
  │               ├─ Tải QR Code
  │               └─ Tải Hóa Đơn
  │
  ├─ Xem Lịch Sử Mua Hàng
  │   │
  │   ├─ Xem Chi Tiết Booking
  │   │   ├─ Tải QR Code
  │   │   ├─ Tải Xác Nhận
  │   │   └─ Hủy Booking (nếu còn thời gian)
  │   │
  │   └─ Đánh Giá Tour/Khách Sạn
  │       ├─ Xếp Hạng Sao (1-5)
  │       ├─ Viết Bình Luận
  │       ├─ Tải Hình Ảnh
  │       │
  │       └─ Xem Trên Sản Phẩm
  │
  ├─ Quản Lý Hồ Sơ
  │   ├─ Cập Nhật Thông Tin
  │   └─ Đổi Mật Khẩu
  │
  ├─ Hỗ Trợ Khách Hàng
  │   ├─ Gọi Hotline
  │   ├─ Gửi Tin Nhắn
  │   └─ Chat Trực Tiếp
  │
  └─ Đăng Xuất (Logout)
     │
     └─ END

```

---

## 🎭 DANH SÁCH USE CASE CHI TIẾT

| # | Use Case | Mô Tả | Actor |
|---|----------|-------|-------|
| 1 | Đăng Ký Tài Khoản | Khách hàng tạo tài khoản mới với email và mật khẩu | Khách Hàng |
| 2 | Xác Minh Email | Khách hàng xác minh email để kích hoạt tài khoản | Khách Hàng |
| 3 | Đăng Nhập 2FA | Đăng nhập bằng email, mật khẩu và mã OTP SMS | Khách Hàng |
| 4 | Cập Nhật Hồ Sơ | Cập nhật thông tin cá nhân (tên, số điện thoại, địa chỉ) | Khách Hàng |
| 5 | Đổi Mật Khẩu | Thay đổi mật khẩu hiện tại | Khách Hàng |
| 6 | Tìm Kiếm Tour | Tìm tour theo tiêu chí (điểm đến, giá, ngày) | Khách Hàng |
| 7 | Xem Chi Tiết Tour | Xem thông tin chi tiết tour (hình, mô tả, giá, review) | Khách Hàng |
| 8 | Tìm Kiếm Khách Sạn | Tìm khách sạn theo tiêu chí (vị trí, giá, xếp hạng) | Khách Hàng |
| 9 | Xem Chi Tiết Khách Sạn | Xem thông tin chi tiết khách sạn (phòng, tiện ích, giá) | Khách Hàng |
| 10 | Thêm Vào Yêu Thích | Thêm tour/khách sạn vào danh sách yêu thích | Khách Hàng |
| 11 | Xóa Khỏi Yêu Thích | Xóa tour/khách sạn khỏi danh sách yêu thích | Khách Hàng |
| 12 | Xem Danh Sách Yêu Thích | Xem tất cả tour/khách sạn được yêu thích | Khách Hàng |
| 13 | Thêm Vào Giỏ Hàng | Thêm tour/khách sạn vào giỏ hàng | Khách Hàng |
| 14 | Xem Giỏ Hàng | Xem danh sách sản phẩm trong giỏ hàng | Khách Hàng |
| 15 | Cập Nhật Số Lượng | Thay đổi số lượng/ngày ở lại của sản phẩm | Khách Hàng |
| 16 | Xóa Khỏi Giỏ | Xóa sản phẩm khỏi giỏ hàng | Khách Hàng |
| 17 | Thanh Toán | Tiến hành quy trình thanh toán | Khách Hàng |
| 18 | Chọn Điểm Khởi Hành | Chọn nơi bắt đầu chuyến tour | Khách Hàng |
| 19 | Chọn Ngày Khởi Hành | Chọn ngày bắt đầu tour | Khách Hàng |
| 20 | Nhập Thông Tin Khách | Nhập họ tên, CMND, passport, ngày sinh | Khách Hàng |
| 21 | Nhập Thông Tin Liên Hệ | Nhập email, số điện thoại, địa chỉ | Khách Hàng |
| 22 | Chọn Phương Thức Thanh Toán | Lựa chọn cách thanh toán (thẻ, ví, ngân hàng) | Khách Hàng |
| 23 | Xác Nhận Booking | Xác nhận booking và tiến hành thanh toán | Khách Hàng |
| 24 | Tạo Mã QR Code | Hệ thống tạo mã QR cho booking | Hệ Thống |
| 25 | Gửi Email Xác Nhận | Gửi email xác nhận booking với mã QR | Hệ Thống |
| 26 | Xem Lịch Sử Booking | Xem danh sách tất cả booking của khách | Khách Hàng |
| 27 | Xem Chi Tiết Booking | Xem chi tiết từng booking đã thực hiện | Khách Hàng |
| 28 | Tải Xác Nhận | Tải file PDF xác nhận booking | Khách Hàng |
| 29 | Tải QR Code | Tải mã QR từ booking | Khách Hàng |
| 30 | Tải Hóa Đơn | Tải file hóa đơn/invoice | Khách Hàng |
| 31 | Hủy Booking | Hủy booking và yêu cầu hoàn tiền | Khách Hàng |
| 32 | Viết Đánh Giá | Viết bình luận và xếp hạng tour/khách sạn | Khách Hàng |
| 33 | Xếp Hạng Sao | Cho điểm 1-5 sao cho sản phẩm | Khách Hàng |
| 34 | Tải Ảnh Đánh Giá | Tải hình ảnh minh họa đánh giá | Khách Hàng |
| 35 | Xem Đánh Giá | Xem các bình luận từ khách hàng khác | Khách Hàng |
| 36 | Like/Dislike Đánh Giá | Đánh dấu đánh giá hữu ích hoặc không | Khách Hàng |
| 37 | Gọi Hotline | Liên hệ qua điện thoại | Khách Hàng |
| 38 | Gửi Tin Nhắn Hỗ Trợ | Gửi tin nhắn qua form liên hệ | Khách Hàng |
| 39 | Chat Trực Tiếp | Chat với nhân viên hỗ trợ trực tuyến | Khách Hàng |
| 40 | Nhận Phản Hồi | Nhận phản hồi từ nhân viên hỗ trợ | Hệ Thống |
| 41 | Đăng Xuất | Đăng xuất khỏi hệ thống | Khách Hàng |

---

## ⚙️ CÁC ĐIỀU KIỆN TIÊN QUYẾT

```
Tất cả Use Case đều yêu cầu:
✅ Khách hàng đã đăng nhập (ngoại trừ Đăng Ký & Đăng Nhập)
✅ Kết nối internet ổn định
✅ Trình duyệt web hỗ trợ hiện đại
✅ JavaScript được bật

Thêm điều kiện cho một số use case:
✅ "Thanh Toán" → Giỏ hàng không rỗng
✅ "Hủy Booking" → Booking chưa hoàn thành/chưa khởi hành
✅ "Đánh Giá" → Đã hoàn thành booking
✅ "Tải QR Code" → Booking đã được tạo
```

---

## 📝 GHI CHÚ

- **Khách Hàng (Customer/User)**: Người sử dụng ứng dụng để booking tour và khách sạn
- **Hệ Thống (System)**: Ứng dụng Travel App xử lý các yêu cầu từ khách hàng
- **Vai trò (Actor)**: Thực thể tương tác với hệ thống (Khách Hàng, Hệ Thống, Admin - không có trong sơ đồ này)

---

**Cập nhật lần cuối**: 11/12/2025 | **Version**: 1.0 | **Tiếng**: Tiếng Việt
