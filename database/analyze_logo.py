from PIL import Image

image_path = r"c:\Users\Admin\Desktop\tiemchung\public\images\logo.png"
try:
    img = Image.open(image_path).convert("RGBA")
    width, height = img.size
    
    # Khoi tao toa do min/max
    min_x, min_y = width, height
    max_x, max_y = 0, 0
    found = False
    
    # Quet qua tung pixel
    for y in range(height):
        for x in range(width):
            r, g, b, a = img.getpixel((x, y))
            # Neu pixel co mau sac thuc te (khong trong suot va khong phai màu trang)
            if a > 10 and not (r > 245 and g > 245 and b > 245):
                found = True
                if x < min_x: min_x = x
                if y < min_y: min_y = y
                if x > max_x: max_x = x
                if y > max_y: max_y = y
                
    if found:
        print(f"Bbox found: left={min_x}, top={min_y}, right={max_x}, bottom={max_y}")
        # Crop thu
        cropped = img.crop((min_x, min_y, max_x, max_y))
        print(f"Cropped size: {cropped.size}")
    else:
        print("No content pixel found!")
except Exception as e:
    print(f"Error: {e}")
