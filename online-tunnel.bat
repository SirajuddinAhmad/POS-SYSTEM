@echo off
title POS SYSTEM - PUBLISH ONLINE INTERNET TUNNEL
color 0B

echo ==============================================================
echo     POS SYSTEM - JARINGAN INTERNET ONLINE PUBLIC ACCESS
echo ==============================================================
echo.
echo Script ini akan membuat URL Publik (HTTPS) yang bisa diakses
echo dari mana saja di seluruh dunia melalui internet tanpa perlu IP Publik!
echo.
echo Pastikan server Laravel sedang berjalan (di port 8000).
echo.
echo Memulai koneksi Tunnel ke Internet...
echo --------------------------------------------------------------
ssh -R 80:localhost:8000 a.pinggy.io
pause
