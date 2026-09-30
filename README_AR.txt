مشروع PHP + MySQL محلي — نموذج تجريبي غير رسمي
=================================================

مكان المشروع الافتراضي المقترح:
C:\xampp\htdocs\seha_local_reference_style

التشغيل لأول مرة:
1) ثبّت XAMPP.
2) افتح XAMPP Control Panel وشغّل Apache و MySQL.
3) فك ضغط هذه الحزمة.
4) انقل مجلد seha_local_reference_style كاملًا إلى:
   C:\xampp\htdocs\
5) افتح المتصفح على:
   http://localhost/seha_local_reference_style/setup.php
6) أنشئ اسم مستخدم وكلمة مرور للمشرف.
7) بعد نجاح الإعداد:
   الموقع: http://localhost/seha_local_reference_style/
   الإدارة: http://localhost/seha_local_reference_style/admin/

إذا غيّرت اسم مجلد المشروع:
افتح config/config.php وعدّل base_path ليطابق اسم المجلد الجديد.
مثال لو كان اسم المجلد seha:
'base_path' => '/seha',

إعدادات MySQL الافتراضية لـ XAMPP:
Host: 127.0.0.1
Port: 3306
Database: seha_local
User: root
Password: فارغة

إذا كانت إعدادات MySQL لديك مختلفة، عدّل config/config.php.

ملاحظات:
- لا تفتح index.php بطريقة file:/// لأن PHP يحتاج Apache.
- يجب أن يكون Apache وMySQL في حالة Running.
- لا تستخدم بيانات حقيقية أو حساسة إلا إذا كان لديك أساس قانوني وصلاحية مناسبة لحفظها.
- هذا المشروع نموذج تجريبي غير تابع لأي جهة حكومية أو صحية.
