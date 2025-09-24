-- ==================================================
-- نظام إدارة المشاريع المبسط - 6 جداول أساسية
-- ==================================================

-- 1. جدول مساحات العمل
CREATE TABLE `workspaces` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'اسم مساحة العمل',
  `description` text DEFAULT NULL COMMENT 'وصف مساحة العمل',
  `admin_id` bigint(20) UNSIGNED NOT NULL COMMENT 'المدير المسؤول',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'هل هي المساحة الرئيسية',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `workspaces_admin_id_foreign` (`admin_id`),
  CONSTRAINT `workspaces_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='مساحات العمل - تقسيم النظام لأقسام منفصلة';

-- 2. جدول المشاريع
CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint(20) UNSIGNED NOT NULL COMMENT 'مساحة العمل التابعة لها',
  `title` varchar(255) NOT NULL COMMENT 'اسم المشروع',
  `description` longtext DEFAULT NULL COMMENT 'وصف المشروع',
  `status` enum('new','in_progress','completed','on_hold') NOT NULL DEFAULT 'new' COMMENT 'حالة المشروع',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium' COMMENT 'أولوية المشروع',
  `budget` decimal(10,2) DEFAULT NULL COMMENT 'ميزانية المشروع',
  `start_date` date NOT NULL COMMENT 'تاريخ البداية',
  `end_date` date NOT NULL COMMENT 'تاريخ النهاية المتوقعة',
  `actual_end_date` date DEFAULT NULL COMMENT 'تاريخ النهاية الفعلية',
  `progress_percentage` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'نسبة الإنجاز 0-100',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT 'منشئ المشروع',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `projects_workspace_id_foreign` (`workspace_id`),
  KEY `projects_created_by_foreign` (`created_by`),
  KEY `projects_status_index` (`status`),
  KEY `projects_priority_index` (`priority`),
  CONSTRAINT `projects_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='المشاريع - تفاصيل جميع المشاريع';

-- 3. جدول المهام
CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) UNSIGNED NOT NULL COMMENT 'المشروع التابعة له',
  `title` varchar(255) NOT NULL COMMENT 'اسم المهمة',
  `description` longtext DEFAULT NULL COMMENT 'وصف المهمة',
  `status` enum('not_started','in_progress','completed','overdue') NOT NULL DEFAULT 'not_started' COMMENT 'حالة المهمة',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium' COMMENT 'أولوية المهمة',
  `start_date` date NOT NULL COMMENT 'تاريخ البداية',
  `due_date` date NOT NULL COMMENT 'تاريخ الاستحقاق',
  `budget` decimal(10,2) DEFAULT NULL COMMENT 'ميزانية المهمة',
  `completed_date` date DEFAULT NULL COMMENT 'تاريخ الإنجاز الفعلي',
  `estimated_hours` decimal(5,2) DEFAULT NULL COMMENT 'الساعات المقدرة',
  `actual_hours` decimal(5,2) DEFAULT NULL COMMENT 'الساعات الفعلية',
  `completion_percentage` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'نسبة الإنجاز 0-100',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT 'منشئ المهمة',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tasks_project_id_foreign` (`project_id`),
  KEY `tasks_created_by_foreign` (`created_by`),
  KEY `tasks_status_index` (`status`),
  KEY `tasks_priority_index` (`priority`),
  KEY `tasks_due_date_index` (`due_date`),
  CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='المهام - مهام المشاريع المختلفة';

-- 4. جدول ربط المستخدمين بالمشاريع
CREATE TABLE `project_user` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) UNSIGNED NOT NULL COMMENT 'معرف المشروع',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'معرف المستخدم',
  `role` enum('manager','member','viewer') NOT NULL DEFAULT 'member' COMMENT 'دور المستخدم في المشروع',
  `joined_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'تاريخ الانضمام للمشروع',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_user_unique` (`project_id`,`user_id`),
  KEY `project_user_project_id_foreign` (`project_id`),
  KEY `project_user_user_id_foreign` (`user_id`),
  CONSTRAINT `project_user_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ربط المستخدمين بالمشاريع';

-- 5. جدول ربط المستخدمين بالمهام
CREATE TABLE `task_user` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) UNSIGNED NOT NULL COMMENT 'معرف المهمة',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'معرف المستخدم',
  `assigned_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'تاريخ التعيين',
  `assigned_by` bigint(20) UNSIGNED NOT NULL COMMENT 'من قام بالتعيين',
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_user_unique` (`task_id`,`user_id`),
  KEY `task_user_task_id_foreign` (`task_id`),
  KEY `task_user_user_id_foreign` (`user_id`),
  KEY `task_user_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `task_user_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_user_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ربط المستخدمين بالمهام';

-- 6. جدول التعليقات
CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'كاتب التعليق',
  `commentable_type` varchar(255) NOT NULL COMMENT 'نوع العنصر (project, task)',
  `commentable_id` bigint(20) UNSIGNED NOT NULL COMMENT 'معرف العنصر',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'التعليق الأصلي (للردود)',
  `content` longtext NOT NULL COMMENT 'محتوى التعليق',
  `is_edited` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'هل تم تعديل التعليق',
  `edited_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ آخر تعديل',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ الحذف (Soft Delete)',
  PRIMARY KEY (`id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_commentable_index` (`commentable_type`,`commentable_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  KEY `comments_created_at_index` (`created_at`),
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='التعليقات - نقاش حول المشاريع والمهام';
