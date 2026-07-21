import ftplib
import os

ftp_host = "ftp.npduylab.com"
ftp_port = 21
ftp_user = "tiemchung@tiemchung.npduylab.com"
ftp_pass = "tiemchung@2026"

files_to_upload = [
    "modules/VaccineRegistration/Http/Controllers/Admin/AdminLiveEditorController.php",
    "modules/VaccineRegistration/resources/views/admin/live_editor.blade.php",
    "modules/VaccineRegistration/resources/views/admin/live_editor_modals.blade.php",
    "modules/VaccineRegistration/resources/views/home.blade.php",
    "modules/VaccineRegistration/resources/views/about.blade.php",
    "modules/VaccineRegistration/routes/web.php",
    "CHANGELOG.md",
]

def ensure_remote_dirs(ftp, remote_dir):
    dirs = remote_dir.replace("\\", "/").split("/")
    acc = ""
    for d in dirs:
        if not d or d == ".":
            continue
        acc += "/" + d
        try:
            ftp.mkd(acc)
        except Exception:
            pass

print(f"Connecting to FTP server {ftp_host}...")
ftp = ftplib.FTP()
ftp.connect(ftp_host, ftp_port, timeout=15)
ftp.login(ftp_user, ftp_pass)
ftp.pasv = True
print("FTP Login Successful!\n-----------------------------------")

success_count = 0
fail_count = 0

for rel_path in files_to_upload:
    local_path = os.path.join(os.getcwd(), rel_path)
    if not os.path.exists(local_path):
        print(f"SKIP (Local missing): {rel_path}")
        continue

    remote_path = rel_path.replace("\\", "/")
    remote_dir = os.path.dirname(remote_path)
    if remote_dir:
        ensure_remote_dirs(ftp, remote_dir)

    try:
        with open(local_path, "rb") as f:
            ftp.storbinary(f"STOR {remote_path}", f)
        print(f"SUCCESS: Uploaded {remote_path}")
        success_count += 1
    except Exception as e:
        print(f"FAILED: {remote_path} -> {e}")
        fail_count += 1

ftp.quit()
print("-----------------------------------")
print(f"Deployment Complete! Success: {success_count}, Failed: {fail_count}")
