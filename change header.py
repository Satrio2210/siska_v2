import os
import re
import shutil

ROOT = "."        # folder project
BACKUP_ROOT = os.path.join(ROOT, "backupcg")

REPLACEMENT = '<?php include "inc/header.php"; ?>'

header_start = re.compile(
    r'<div\s+class\s*=\s*["\']header["\']',
    re.IGNORECASE
)

div_open = re.compile(r'<div\b', re.IGNORECASE)
div_close = re.compile(r'</div>', re.IGNORECASE)

total_file = 0
total_replace = 0


def find_matching_div(text, start_pos):
    """
    Mengembalikan posisi setelah </div> penutup
    dari <div class="header">.
    """

    level = 0
    pos = start_pos

    while True:

        open_match = div_open.search(text, pos)
        close_match = div_close.search(text, pos)

        if close_match is None:
            return None

        if open_match and open_match.start() < close_match.start():
            level += 1
            pos = open_match.end()
        else:
            level -= 1
            pos = close_match.end()

            if level == 0:
                return pos


for root, dirs, files in os.walk(ROOT):

    dirs[:] = [
        d for d in dirs
        if d not in (
            ".git",
            ".vscode",
            "__pycache__",
            "vendor",
            "node_modules"
        )
    ]

    for file in files:

        if not file.lower().endswith(".php"):
            continue

        path = os.path.join(root, file)

        try:

            with open(path, "r", encoding="utf-8", errors="ignore") as f:
                text = f.read()

            changed = False
            pos = 0

            while True:

                m = header_start.search(text, pos)

                if not m:
                    break

                end = find_matching_div(text, m.start())

                if end is None:
                    break

                # Hilangkan komentar HTML setelah </div> bila ada
                comment = re.match(
                    r'\s*<!--.*?-->',
                    text[end:],
                    re.DOTALL
                )

                final_end = end

                if comment:
                    final_end += comment.end()

                text = (
                    text[:m.start()]
                    + REPLACEMENT
                    + text[final_end:]
                )

                pos = m.start() + len(REPLACEMENT)

                changed = True
                total_replace += 1

            if changed:

                # Path relatif dari project
                relative_path = os.path.relpath(path, ROOT)

                # Lokasi backup
                backup_path = os.path.join(BACKUP_ROOT, relative_path)

                # Buat folder jika belum ada
                os.makedirs(os.path.dirname(backup_path), exist_ok=True)

                # Copy file
                shutil.copy2(path, backup_path)

                with open(path, "w", encoding="utf-8", newline="") as f:
                    f.write(text)

                total_file += 1
                print(f"[OK] {path}")

        except Exception as e:
            print(f"[ERROR] {path}")
            print(e)

print("=" * 60)
print("SELESAI")
print("File diubah :", total_file)
print("Header diganti :", total_replace)