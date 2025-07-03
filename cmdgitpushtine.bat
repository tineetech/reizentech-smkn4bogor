@echo off
SET /P commit_message="Masukkan pesan commit: "
git add .
git commit -m "%commit_message%"
git push origin justine_dev
echo.
echo Proses Gitpushtine selesai!