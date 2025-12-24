# Nhanh.vn WooCommerce Sync Plugin

Plugin đồng bộ sản phẩm giữa Nhanh.vn và WooCommerce thông qua API.

## Tính năng

- ✅ Đồng bộ sản phẩm từ Nhanh.vn về WooCommerce
- ✅ Tự động tải và cập nhật hình ảnh sản phẩm
- ✅ Đồng bộ thông tin: giá, tồn kho, mô tả, trọng lượng, kích thước
- ✅ Đồng bộ tự động theo lịch trình (mỗi giờ, 2 lần/ngày, mỗi ngày, mỗi tuần)
- ✅ Đồng bộ thủ công qua trang admin
- ✅ Cập nhật sản phẩm đã tồn tại hoặc chỉ tạo mới
- ✅ Kiểm tra kết nối API

## Yêu cầu

- WordPress 5.0 trở lên
- WooCommerce 3.0 trở lên
- PHP 7.2 trở lên
- Tài khoản Nhanh.vn với API Key

## Cài đặt

1. Upload thư mục `nhanhvn` vào thư mục `/wp-content/plugins/`
2. Kích hoạt plugin qua menu 'Plugins' trong WordPress
3. Đảm bảo WooCommerce đã được cài đặt và kích hoạt

## Cấu hình

1. Vào menu **Nhanh.vn Sync > Cài đặt**
2. Nhập thông tin API:
   - **API URL**: URL API của Nhanh.vn (mặc định: https://open.nhanh.vn/api)
   - **API Key**: API Key từ tài khoản Nhanh.vn
   - **Store ID**: ID cửa hàng trên Nhanh.vn
3. Nhấn **Kiểm tra kết nối** để xác minh thông tin
4. Cấu hình các tùy chọn:
   - **Đồng bộ tự động**: Bật/tắt đồng bộ tự động
   - **Tần suất đồng bộ**: Chọn tần suất đồng bộ tự động
   - **Cập nhật sản phẩm đã tồn tại**: Cho phép cập nhật sản phẩm đã có
5. Nhấn **Lưu cài đặt**

## Sử dụng

### Đồng bộ thủ công

1. Vào menu **Nhanh.vn Sync > Đồng bộ sản phẩm**
2. Nhấn nút **Bắt đầu đồng bộ**
3. Chờ quá trình đồng bộ hoàn tất

### Đồng bộ tự động

Khi bật **Đồng bộ tự động**, plugin sẽ tự động đồng bộ sản phẩm theo lịch trình đã cấu hình.

## Cách hoạt động

- Plugin sử dụng SKU hoặc Nhanh.vn ID để xác định sản phẩm đã tồn tại
- Nếu sản phẩm chưa tồn tại, plugin sẽ tạo sản phẩm mới
- Nếu sản phẩm đã tồn tại và bật tùy chọn cập nhật, plugin sẽ cập nhật thông tin
- Hình ảnh sẽ được tải về và lưu vào thư viện media của WordPress
- Hình ảnh đầu tiên sẽ được đặt làm ảnh đại diện (featured image)

## Lưu ý

- Quá trình đồng bộ có thể mất vài phút tùy thuộc vào số lượng sản phẩm
- Nên sao lưu dữ liệu trước khi đồng bộ lần đầu
- Đảm bảo API Key và Store ID chính xác để tránh lỗi

## Hỗ trợ

Nếu gặp vấn đề, vui lòng kiểm tra:
1. API Key và Store ID có chính xác không
2. WooCommerce đã được cài đặt và kích hoạt chưa
3. Kết nối internet có ổn định không
4. Log lỗi trong WordPress (nếu có)

## Phiên bản

Version: 1.0.0

