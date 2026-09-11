# ============================================
# Folklore - Dockerfile للإنتاج على Render
# ============================================

FROM php:8.3-cli

# ============================================
# 1. تثبيت الحزم الأساسية
# ============================================
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
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ============================================
# 2. تثبيت إضافات PHP
# ============================================
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# ============================================
# 3. تثبيت Composer
# ============================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ============================================
# 4. إعداد مجلد العمل
# ============================================
WORKDIR /var/www/html

# ============================================
# 5. نسخ ملفات المشروع
# ============================================
COPY . .

# ============================================
# 6. تثبيت اعتماديات Composer
# ============================================
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ============================================
# 7. بناء أصول Vite
# ============================================
RUN npm install && npm run build

# ============================================
# 8. الصلاحيات
# ============================================
RUN chmod -R 777 storage bootstrap/cache

# ============================================
# 9. كشف المنفذ (Render يستخدم 10000 افتراضياً)
# ============================================
EXPOSE 10000

# ============================================
# 10. أمر التشغيل
# ============================================
# ✅ أمر واحد بسيط لا ينتهي (php artisan serve عملية مستمرة)
CMD php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan permission:cache-reset && \
    php artisan storage:link && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}