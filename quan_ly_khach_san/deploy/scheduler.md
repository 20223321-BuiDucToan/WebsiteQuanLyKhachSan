# Scheduler Production

Chuc nang tu dong xu ly don dat phong online qua han nam trong `routes/console.php`.

Laravel scheduler can duoc goi moi phut. Command ben trong da cau hinh chay `booking:process-overdue-arrivals` moi 5 phut.

## Linux cron

```cron
* * * * * cd /var/www/quan_ly_khach_san && php artisan schedule:run >> /dev/null 2>&1
```

## Windows Task Scheduler

- Program/script: `C:\xampp\php\php.exe`
- Add arguments: `artisan schedule:run`
- Start in: `C:\Users\ADMIN\Documents\chuyen_de_1\WebsiteQuanLyKhachSan\quan_ly_khach_san`
- Trigger: repeat every 1 minute.

## Manual test

```bash
php artisan booking:process-overdue-arrivals
php artisan schedule:run
```
