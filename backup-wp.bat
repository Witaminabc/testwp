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
REM 🔧 УМНЫЙ COMPOSER
echo 📦 Проверяем зависимости Composer...
if exist composer.json (
    echo 📄 composer.json найден.

    REM 1. Сохраняем текущее состояние (опционально, для отката)
    copy composer.json composer.json.bak >nul

    REM 2. Обновляем зависимости с перехватом ошибок
    echo 🔄 Запускаем composer install...
    call composer install --no-dev --optimize-autoloader --no-interaction 2> composer-error.log

    REM 3. Проверяем, была ли ошибка
    if errorlevel 1 (
        echo ⚠️ Composer завершился с ошибкой! См. composer-error.log
        echo ⚠️ Восстанавливаем старый composer.json...
        copy composer.json.bak composer.json /Y >nul
    ) else (
        echo ✅ Зависимости успешно обновлены.
        del composer-error.log 2>nul
    )
    del composer.json.bak 2>nul
) else (
    echo ℹ️ composer.json не найден, пропускаем.
)


pause
