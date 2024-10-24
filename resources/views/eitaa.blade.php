<!DOCTYPE html>

<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}"/>

        <title>{{ config('app.name') }}</title>

    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <script src="https://developer.eitaa.com/eitaa-web-app.js"></script>

    <h2>Eitaa Web App</h2>

    <p id="p_token"></p>


    <script>

        let data = 'user=%7B%22id%22%3A279058397%2C%22first_name%22%3A%22Vladislav%22%2C%22last_name%22%3A%22Kibenko%22%2C%22username%22%3A%22vdkfrost%22%2C%22language_code%22%3A%22en%22%2C%22is_premium%22%3Atrue%2C%22allows_write_to_pm%22%3Atrue%7D&chat_instance=-3788475317572404878&chat_type=private&auth_date=1709144340&hash=371697738012ebd26a111ace4aff23ee265596cd64026c8c3677956a85ca1827';
        data = window.Eitaa.WebApp.initData;

        const token = '60307504:}Jd*4noxl6-$0JXOQ8qcp-G?,mu3uD.P-^E%B0Ybf2g-h5Fg]#Ap.E-T3ekyFJdUT-{CbRzT58tu-BOMFh]xdD1-Y90)FPv0NZ-6hYERp{fqC-L,uwWP7vg5-Q2X6u)3o$S-2Jxv4sS7ek-#Y';
        const webAppData = 'WebAppData';

        p_token.textContent = 'Token: ' + token

        async function hmacSha256(key, message) {
            const encoder = new TextEncoder();
            const keyData = encoder.encode(key);
            const messageData = encoder.encode(message);

            const cryptoKey = await crypto.subtle.importKey(
                'raw',
                keyData,
                { name: 'HMAC', hash: 'SHA-256' },
                false,
                ['sign']
            );

            const signature = await crypto.subtle.sign('HMAC', cryptoKey, messageData);

            // تبدیل امضای باینری به هگزادسیمال
            return Array.from(new Uint8Array(signature))
                .map(byte => byte.toString(16).padStart(2, '0'))
                .join('');
        }

        // تابع اصلی برای پردازش داده‌ها و بررسی هش
        async function processData(data) {
            const params = new URLSearchParams(data);
            const request = {};

            // تجزیه داده‌ها به جفت‌های کلید-مقدار
            for (let [key, value] of params) {
                request[key] = value;
            }

            // مرتب‌سازی جفت‌ها بر اساس کلیدها
            const sortedKeys = Object.keys(request).sort();

            // استخراج hash و حذف آن از جفت‌های کلید-مقدار
            const hash = request['hash'];
            delete request['hash'];

            // تبدیل رشته user به JSON
            if (request['user']) {
                request['user'] = JSON.parse(decodeURIComponent(request['user']));
            }

            // محاسبه HMAC-SHA256 برای WebAppData و توکن ربات
            const hmacKey = await hmacSha256(webAppData, token);

            // ساخت رشته نهایی از جفت‌های مرتب شده
            const dataString = sortedKeys
                .filter(key => key !== 'hash')
                .map(key => `${key}=${typeof request[key] === 'object' ? JSON.stringify(request[key]) : request[key]}`)
                .join('\n');

            // محاسبه HMAC-SHA256 نهایی
            const calculatedHash = await hmacSha256(hmacKey, dataString);

            console.log('Hash:', hash);
            console.log('Calculated Hash:', calculatedHash);

            // بررسی تطابق هش‌ها
            const isValid = hash === calculatedHash;
            console.log('Is valid:', isValid);
        }

        processData(data);


    </script>

    </body>
</html>
