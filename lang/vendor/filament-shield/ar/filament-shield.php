<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'العنوان',
    'column.guard_name' => 'اسم الحارس',
    'column.roles' => 'الأدوار',
    'column.permissions' => 'الصلاحيات',
    'column.updated_at' => 'تاريخ التحديث',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'العنوان',
    'field.guard_name' => 'اسم الحارس',
    'field.permissions' => 'الصلاحيات',
    'field.select_all.name' => 'تحديد الكل',
    'field.select_all.message' => 'تفعيل كافة الصلاحيات لهذا الدور',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'إدارة الوصول',
    'nav.role.label' => 'الأدوار',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'دور',
    'resource.label.roles' => 'الأدوار',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'الأقسام',
    'resources' => 'المصادر',
    'widgets' => 'الأجزاء',
    'pages' => 'الصفحات',
    'custom' => 'صلاحيات مخصصة',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'أنت غير مخول، لديك صلاحية للوصول',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'عرض',
        'view_any' => 'عرض الكل',
        'create' => 'إضافة',
        'update' => 'تعديل',
        'delete' => 'حذف',
        'delete_any' => 'حذف الكل',
        'force_delete' => 'إجبار الحذف',
        'force_delete_any' => ' إجبار حذف أي',
        'reorder' => 'إعادة ترتيب',
        'restore' => 'استرجاع',
        'restore_any' => 'استرجاع الكل',
        'replicate' => 'استنساخ',
        'approve_registeration' => 'قبول / رفض الطالب',
        'termination' => 'انهاء القيد',
        'approve_employee_registeration' => 'اعتماد اضافة موظف',
        'finish_employee_duration' => 'انهاء فترة عمل',
        'print' => 'طباعة',
        'view_in_menu' => 'اظهار في القائمة',
        'add_expense' => 'اضافة مصروف',
        'terminate_student_registeration' => 'انهاء قيد',
        'upgrade_student' => 'ترفيع',
        'print_fees_invoice' => 'طباعة فاتورة الرسوم',
        'add_receipt_voucher' => 'سداد جديد',
        'show_payments' => 'عرض مدفوعات',
        'add_payment' => 'اضافة مدفوعات',
        'update_payment' => 'تعديل مدفوعات',
        'delete_payment' => 'حذف مدفوعات',
        'create_transport_registeration' => 'اضافة الى المواصلات',
        'create_transport_registeration' => 'اضافة مواصلات',
        'terminate_transport_registeration' => 'انهاء قيد المواصلات',
        'ban_user' => 'حظر مستخدم',
        'approve_fee_payment_request' => 'قبول طلب سداد',
        'reject_fee_payment_request' => 'رفض طلب سداد',
    ],
];
