FROM php:8.3-fpm

# تثبيت الحزم المطلوبة
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    nginx \
    supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت امتدادات PHP
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعداد مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# تثبيت اعتماديات Composer (بدون dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# تثبيت اعتماديات NPM وبناء الأصول
RUN npm install && npm run build

# إعداد الصلاحيات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# نسخ إعدادات Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# نسخ سكربت التشغيل
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]