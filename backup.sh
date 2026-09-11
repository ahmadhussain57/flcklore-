#!/bin/bash

# ═══════════════════════════════════════════
# سكربت النسخ الاحتياطي لقاعدة بيانات folklore
# ═══════════════════════════════════════════

DB_NAME="flcklore"
BACKUP_DIR="./backups"
DATE=$(date +%Y-%m-%d_%H-%M-%S)

# إنشاء مجلد النسخ الاحتياطي
mkdir -p "$BACKUP_DIR"

BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_${DATE}.sql"

echo "🔄 بدء النسخ الاحتياطي..."

# ✅ استخدام sudo -u postgres لتجاوز مصادقة peer
if sudo -u postgres pg_dump -d "$DB_NAME" -f "$BACKUP_FILE"; then
    # ضغط الملف
    gzip "$BACKUP_FILE"
    echo "✅ تم النسخ: ${BACKUP_FILE}.gz"
    echo "📊 الحجم: $(du -h ${BACKUP_FILE}.gz | cut -f1)"

    # حذف النسخ الأقدم من 7 أيام
    find "$BACKUP_DIR" -name "*.sql.gz" -mtime +7 -delete
    echo "🗑️  تم حذف النسخ الأقدم من 7 أيام"
else
    echo "❌ فشل النسخ الاحتياطي!"
    exit 1
fi