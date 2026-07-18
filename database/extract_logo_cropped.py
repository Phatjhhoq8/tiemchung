# -*- coding: utf-8 -*-
"""
Chức năng: Trích xuất logo từ PDF, tự động nhận diện và cắt bỏ (autocrop) khoảng trắng thừa xung quanh để lấy đúng logo Medicare Cờ Đỏ sát lề.
Lý do tạo: Tránh việc logo bị quá nhỏ hoặc lệch do khoảng trống khổ giấy A4 của file PDF gốc.
"""

from PIL import Image, ImageChops
import fitz
import os

pdf_path = r"c:\Users\Admin\Desktop\tiemchung\require\logo medicare - ok.pdf"
output_dir = r"c:\Users\Admin\Desktop\tiemchung\public\images"
output_path = os.path.join(output_dir, "logo.png")

try:
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)

    # 1. Render PDF sang ảnh RGBA độ phân giải cao
    doc = fitz.open(pdf_path)
    page = doc.load_page(0)
    
    zoom = 4
    mat = fitz.Matrix(zoom, zoom)
    pix = page.get_pixmap(matrix=mat, alpha=True)
    
    # Chuyển đổi sang PIL Image
    img = Image.frombytes("RGBA", [pix.width, pix.height], pix.samples)
    
    # 2. Tìm bounding box của vùng chứa logo (loại bỏ màu trắng nền)
    # Chuyển sang ảnh grayscale để tính toán
    gray = img.convert("L")
    
    # Đảo ngược màu: Pixel gần trắng (> 240) -> 0 (nền), các pixel khác -> 255 (nội dung logo)
    inv_gray = gray.point(lambda x: 0 if x > 240 else 255)
    bbox = inv_gray.getbbox()
    
    if bbox:
        # Thêm padding 20px để logo hiển thị thoáng, không bị cắt sát quá
        padding = 20
        w, h = img.size
        left = max(0, bbox[0] - padding)
        top = max(0, bbox[1] - padding)
        right = min(w, bbox[2] + padding)
        bottom = min(h, bbox[3] + padding)
        
        # Cắt ảnh theo khung bao mới
        cropped_img = img.crop((left, top, right, bottom))
        cropped_img.save(output_path)
        print(f"[+] Da autocrop logo thanh cong: {output_path} (Khung cu: {img.size} -> Khung moi: {cropped_img.size})")
    else:
        # Nếu không tìm thấy bounding box, lưu ảnh gốc
        img.save(output_path)
        print("[!] Khong the tu dong tim bounding box, luu anh goc.")
        
except Exception as e:
    print(f"[LOI] Loi khi thuc hien crop logo: {str(e)}")
