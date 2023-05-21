CREATE DATABASE IF NOT EXISTS `sprs`;

USE `sprs`;


CREATE TABLE `users` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `role_id` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `neptuncode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `users` (`id`, `role_id`, `username`, `fullname`, `neptuncode`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 1, 'administrator', 'Administrator user', 'NONEPTUN', '', '$2y$10$67V7TOyY9UXXaZbL.Wplye5anOqnnqP.cnS49nKIydzIlaMJqV/iO', '2021-02-01 20:00:00', '2022-11-24 14:31:04'),
(2, 3, 'TokenHolder', 'Token holder user - privileged permissions by server', 'TH', 'no-email-required', '', '2022-11-24 14:44:32', '2022-11-24 14:44:32');


CREATE TABLE `roles` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES (1, 'Admin', 'Has authority of users, roles and permissions plus everything');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES (2, 'Guest', 'No permissions');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES (3, 'Student', 'Can register for topic');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES (4, 'Lecturer', 'Has full authority of own topics');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES (5, 'Coordinator', 'Has full authority over all topics');


CREATE TABLE `permissions` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'view-dashboard', 'can view dashboard'),
(2, 'view-profile', 'can view profile'),
(3, 'view-user-list', 'can view users'),
(4, 'create-user', 'can create users'),
(5, 'update-user', 'can update users'),
(6, 'delete-user', 'can delete users'),
(7, 'assign-user-role', 'can assign role to user'),
(8, 'view-role-list', 'can view roles'),
(9, 'create-role', 'can create roles'),
(10, 'update-role', 'can update roles'),
(11, 'delete-role', 'can delete roles'),
(12, 'assign-role-permission', 'can assign permissions to role'),
(13, 'view-opportunity-list', 'can view opportunities'),
(14, 'create-opportunity', 'can create opportunities'),
(15, 'update-opportunity', 'can update opportunities'),
(16, 'delete-opportunity', 'can delete opportunities'),
(17, 'generate-token', 'can generate QR code/hexadcimal number'),
(18, 'update-token', 'can update token'),
(19, 'view-point-types', 'can view the types of points'),
(20, 'create-point-type', 'can create new point type'),
(21, 'edit-point-type', 'can edit point type'),
(22, 'delete-point-type', 'can delete point type'),
(23, 'manage-excellence-list', 'can manage excellence lists'),
(24, 'create-excellence-list', 'can create excellence lists'),
(25, 'update-excellence-list', 'can edit excellence lists'),
(26, 'delete-excellence-list', 'can delete excellence lists');



-- ************************************************************************************************************************************************************************************* --
-- Roles permissions 

CREATE TABLE `permission_role` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (1, 1, 1);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (2, 1, 2);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (3, 1, 3);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (4, 1, 4);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (5, 1, 5);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (6, 1, 6);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (7, 1, 7);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (8, 1, 8);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (9, 1, 9);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (10, 1, 10);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (11, 1, 11);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (12, 1, 12);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (13, 1, 13);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (14, 1, 14);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (15, 1, 15);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (16, 1, 16);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (17, 1, 17);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (18, 1, 18);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (19, 1, 19);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (20, 1, 20);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (21, 1, 21);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (22, 1, 22);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (23, 1, 23);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (24, 1, 24);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (25, 1, 25);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (26, 1, 26);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (27, 3, 2);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (28, 3, 13);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (29, 4, 13);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (30, 4, 14);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (31, 4, 15);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (32, 4, 16);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (33, 4, 17);
INSERT INTO `permission_role` (`id`, `role_id`, `permission_id`) VALUES (34, 4, 18);



-- ************************************************************************************************************************************************************************************* --
-- OPPORTUNITIES

CREATE TABLE `opportunities` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `opportunity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int(11) NOT NULL,
  `points_type` int(255) NOT NULL,
  `expiration_date` date NOT NULL,
  `owner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `opportunity_points_type` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



INSERT INTO `opportunity_points_type` (`id`, `name`) VALUES (1, 'social');
INSERT INTO `opportunity_points_type` (`id`, `name`) VALUES (2, 'professional');



CREATE TABLE `tokens` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `token` varchar(255) NOT NULL,
  `opportunity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `expiration_date` text NOT NULL,
  `redeemed` text NOT NULL DEFAULT 'no',
  `login_required` varchar(255) NOT NULL DEFAULT 'true'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `excellence_lists` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `points_type` varchar(255) NOT NULL,
  `users` longtext DEFAULT '[ [ ] ]',
  `created_by` int(11) NOT NULL,
  `show_name` varchar(255) NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `excellence_lists` (`id`, `name`, `points_type`, `users`, `created_by`, `show_name`) VALUES (1, 'Global excellence list', 'all', 'all', 1, 'false');

CREATE TABLE `excellence_points` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `opportunity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` text NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `email` (
  `apikey` varchar(255) NOT NULL PRIMARY KEY,
  `email_from` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `user_id` INT(255) NOT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `account_verification` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `user_id` INT(11) NOT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `email` (`id`, `apikey`, `email`) VALUES (1, 'XXXXXXXXXXXXXXXXXXX', 'localhost@localhost.localhost');

ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
  ALTER TABLE `opportunities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
ALTER TABLE `opportunity_points_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
ALTER TABLE `permission_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
ALTER TABLE `excellence_lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
ALTER TABLE `excellence_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


ALTER TABLE `excellence_lists`
  ADD CONSTRAINT `excellence_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `excellence_points`
  ADD CONSTRAINT `points_ibfk_1` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `points_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `opportunities`
  ADD CONSTRAINT `opportunities_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `opportunities_ibfk_2` FOREIGN KEY (`points_type`) REFERENCES `opportunity_points_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_role_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`);


ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `tokens_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `tokens_ibfk_3` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `account_verification`
  ADD CONSTRAINT `account_verification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

COMMIT;