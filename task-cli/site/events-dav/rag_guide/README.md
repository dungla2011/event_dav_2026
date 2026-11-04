# rag_guide — tài liệu cho Trợ lý AI events.dav.edu.vn

Mọi file `*.txt` trong thư mục này được ghép (theo thứ tự tên file) vào prompt của trợ lý
(`../rag_guide_svc/guide_api.py`). File này là `.md` nên không bị nạp.

- Thêm/sửa file là có hiệu lực ngay câu hỏi kế tiếp, không cần khởi động lại service.
- Đặt tên `rag-hd-<chủ-đề>.txt`, UTF-8, viết văn thường như hướng dẫn cho người dùng. Tên menu/nút ghi đúng như
  trên giao diện, in đậm `**...**`.
- File quy tắc bắt buộc (cách trả lời, điều luôn phải nhắc) đặt tên bắt đầu bằng `00-` để nằm đầu prompt.
- Tổng dung lượng nên dưới ~150 KB (mỗi câu hỏi gửi cả bộ; có prompt cache nên vẫn rẻ).
- Bản gốc nằm trong repo Event-DavAppFace2026 `server-dav/task-cli/site/events-dav/rag_guide/`; sửa ở đó rồi
  `scp` lên, hoặc sửa thẳng trên server rồi chép ngược về repo.
- Xem câu hỏi người dùng đã hỏi: `/var/glx/weblog/rag_guide.log` — câu nào trợ lý trả lời "chưa có hướng dẫn"
  là gợi ý cần viết thêm file.
