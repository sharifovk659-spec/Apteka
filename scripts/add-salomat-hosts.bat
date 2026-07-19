@echo off
:: Run as Administrator: adds salomat.local to Windows hosts file
set HOSTS=%SystemRoot%\System32\drivers\etc\hosts
findstr /C:"salomat.local" "%HOSTS%" >nul 2>&1
if %errorlevel%==0 (
    echo salomat.local already exists in hosts.
    goto :done
)
echo.>> "%HOSTS%"
echo # Salomat Laravel>> "%HOSTS%"
echo 127.0.0.1 salomat.local www.salomat.local>> "%HOSTS%"
echo Added salomat.local to hosts file.

:done
pause
