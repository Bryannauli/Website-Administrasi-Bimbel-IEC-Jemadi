FROM php:8.2-apache

# Menginstall dependensi sistem dasar & Node.js untuk Tailwind
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm

# Membersihkan cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Menginstall ekstensi PHP yang dibutuhkan Laravel & MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Mengaktifkan mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# Mengarahkan domain ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set folder kerja
WORKDIR /var/www/html

# Copy semua kodingan kamu ke dalam server Render
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependensi PHP & Build Tailwind CSS
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Mengatur izin folder agar Laravel tidak error permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80