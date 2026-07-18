import fitz

pdf_path = r"c:\Users\Admin\Desktop\tiemchung\require\logo medicare - ok.pdf"
try:
    doc = fitz.open(pdf_path)
    print(f"Total pages: {len(doc)}")
    for i, page in enumerate(doc):
        print(f"\n--- Page {i+1} ---")
        print(f"Page rect: {page.rect}")
        images = page.get_images(full=True)
        print(f"Number of embedded images: {len(images)}")
        for img_idx, img in enumerate(images):
            xref = img[0]
            base_image = doc.extract_image(xref)
            print(f"Image {img_idx+1}: xref={xref}, ext={base_image['ext']}, size={len(base_image['image'])} bytes, width={base_image['width']}, height={base_image['height']}")
except Exception as e:
    print(f"Error: {e}")
