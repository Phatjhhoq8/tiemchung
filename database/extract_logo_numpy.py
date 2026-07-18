# -*- coding: utf-8 -*-
"""
Chức năng: Trích xuất và tự động crop logo từ PDF bằng Numpy, lọc bỏ các nét viền xám/đen của trang giấy bằng cách nhận diện màu sắc thực tế.
Lý do tạo: Loại bỏ các khung viền/dấu trang xám ở rìa file PDF gốc, chỉ giữ lại logo có màu sắc (Đỏ/Vàng).
"""

import fitz
import numpy as np
from PIL import Image
import os

pdf_path = r"c:\Users\Admin\Desktop\tiemchung\require\logo medicare - ok.pdf"
output_dir = r"c:\Users\Admin\Desktop\tiemchung\public\images"
output_path = os.path.join(output_dir, "logo.png")

try:
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)

    # 1. Render PDF sang ảnh RGBA 4x độ nét cao
    doc = fitz.open(pdf_path)
    page = doc.load_page(0)
    
    zoom = 4
    mat = fitz.Matrix(zoom, zoom)
    pix = page.get_pixmap(matrix=mat, alpha=True)
    
    img = Image.frombytes("RGBA", [pix.width, pix.height], pix.samples)
    
    # 2. Dùng Numpy phân tích mảng pixel
    arr = np.array(img)
    r, g, b, a = arr[:,:,0].astype(int), arr[:,:,1].astype(int), arr[:,:,2].astype(int), arr[:,:,3].astype(int)
    
    # Tính độ lệch màu giữa các kênh R, G, B để nhận diện pixel có màu thực sự (không phải xám/đen/trắng)
    diff_rg = np.abs(r - g)
    diff_gb = np.abs(g - b)
    diff_rb = np.abs(r - b)
    max_diff = np.maximum(np.maximum(diff_rg, diff_gb), diff_rb)
    
    # Điều kiện lọc:
    # - Có độ mờ lớn (a > 10)
    # - Không phải màu gần trắng (R,G,B không đồng thời > 245)
    # - Có màu sắc thực sự (chênh lệch giữa các kênh màu > 20 để loại bỏ viền xám/đen của trang giấy)
    mask = (a > 10) & ((r < 245) | (g < 245) | (b < 245)) & (max_diff > 20)
    coords = np.argwhere(mask)
    
    if coords.size > 0:
        min_y, min_x = coords.min(axis=0)
        max_y, max_x = coords.max(axis=0)
        
        # Thêm padding 30px xung quanh logo
        padding = 30
        left = max(0, min_x - padding)
        top = max(0, min_y - padding)
        right = min(img.width, max_x + padding)
        bottom = min(img.height, max_y + padding)
        
        # Cắt logo
        cropped_img = img.crop((left, top, right, bottom))
        cropped_img.save(output_path)
        print(f"[+] Da crop logo loc màu thanh cong: {output_path} (Size: {cropped_img.size})")
    else:
        # Nếu lọc màu không ra, thử lọc pixel phi trắng thông thường nhưng thụt lề 300px để bỏ viền trang
        mask_basic = (a > 10) & ((r < 245) | (g < 245) | (b < 245))
        coords_basic = np.argwhere(mask_basic)
        if coords_basic.size > 0:
            min_y, min_x = coords_basic.min(axis=0)
            max_y, max_x = coords_basic.max(axis=0)
            
            # Thụt lề bỏ viền trang (giả định viền nằm ở sát biên cách 250px)
            left = max(0, min_x + 250)
            top = max(0, min_y + 250)
            right = min(img.width, max_x - 250)
            bottom = min(img.height, max_y - 250)
            
            cropped_img = img.crop((left, top, right, bottom))
            cropped_img.save(output_path)
            print(f"[+] Da crop logo thut le bỏ vien thanh cong: {output_path} (Size: {cropped_img.size})")
        else:
            img.save(output_path)
            print("[!] Khong tim thay pixel nao, luu anh goc.")
        
except Exception as e:
    print(f"[LOI] Loi khi thuc hien crop: {str(e)}")
