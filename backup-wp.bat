@echo off
chcp 65001 >nul
cd /d "D:\osPanel543\OSPanel\domains\test"

echo 🚀 Автобэкап WordPress + Git...

if not exist backups mkdir backups

REM OSPanel пути (ВАШИ точные пути!)
set PHP_PATH=D:\osPanel543\OSPanel\modules\PHP\PHP_8.0\php.exe
set MYSQL_PATH=D:\osPanel543\OSPanel\modules\database\MySQL-5.6\bin\mysqldump.exe

REM Дата
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "timestamp=%dt:~0,8%-%dt:~8,6%"
set "filename=wp-content\backups\db-%timestamp%.sql"

REM ПРЯМОЙ дамп через mysqldump OSPanel (ОБХОДИТ WP-CLI!)
echo 📦 Бэкап БД: %filename%
"%MYSQL_PATH%" --user=root --password="" --host=127.0.0.1 --port=3306 test > "%filename%"

REM ПРОВЕРКА результата
if exist "%filename%" (
    REM Проверка что файл не пустой
    for %%F in ("%filename%") do if %%~zF GTR 1000 (
        echo 💾 Git...
        git add .
        git add "%filename%"
        git commit -m "backup: БД %timestamp% + Doctors CPT"
        git push origin main
        echo ✅ Готово! %filename%
    ) else (
        echo ❌ Бэкап пустой!
        del "%filename%"
    )
) else (
    echo ❌ Бэкап не создался!
)

pause
