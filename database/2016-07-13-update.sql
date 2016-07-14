ALTER TABLE `user`
ADD `hash_change_password_id` varchar(32) COLLATE 'utf8_general_ci' NOT NULL AFTER `password`,
ADD `hash_change_password_secred` varchar(32) COLLATE 'utf8_general_ci' NOT NULL AFTER `hash_change_password_id`,
ADD `hash_change_password_expiration_date` datetime NOT NULL AFTER `hash_change_password_secred`;