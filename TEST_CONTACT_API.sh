curl --location 'http://localhost:8000/api/contacts' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer YOUR_TOKEN_HERE' \
--data '{
  "subject": "Chủ đề test tin nhắn",
  "message": "Đây là nội dung tin nhắn test từ API. Tin nhắn này sẽ được gửi với thông tin của user đang đăng nhập."
}'
