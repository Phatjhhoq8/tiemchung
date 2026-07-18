# -*- coding: utf-8 -*-
"""
Chức năng: Trích xuất trang đầu tiên của file PDF logo thành ảnh PNG sắc nét.
Lý do tạo: Sử dụng file logo PDF thực tế người dùng cung cấp làm ảnh logo hiển thị trên website.
"""

import fitz  # PyMuPDF
import os

pdf_path = r"c:\Users\Admin\Desktop\tiemchung\require\logo medicare - ok.pdf"
output_dir = r"c:\Users\Admin\Desktop\tiemchung\public\images"
output_path = os.path.join(output_dir, "logo.png")

try:
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
        print(f"[+] Da tao thu muc {output_dir}")

    doc = fitz.open(pdf_path)
    page = doc.load_page(0)  # Trang dau tien

    # Dung ma tran zoom 4x de anh logo xuat ra sac net
    zoom = 4
    mat = fitz.Matrix(zoom, zoom)
    pix = page.get_pixmap(matrix=mat, alpha=True)
    
    pix.save(output_path)
    print(f"[+] Da trich xuat logo tu PDF thanh cong: {output_path}")
except Exception as e:
    print(f"[LOI] Khong the trich xuat logo: {str(e)}")
