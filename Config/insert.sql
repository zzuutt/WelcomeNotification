# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Mail templates for welcome_notification
-- ---------------------------------------------------------------------
-- First, delete existing entries
SET @var := 0;
SELECT @var := `id` FROM `message` WHERE name="welcome_notification";
DELETE FROM `message` WHERE `id`=@var;
-- Try if ON DELETE constraint isn't set
DELETE FROM `message_i18n` WHERE `id`=@var;

-- Then add new entries
SELECT @max := MAX(`id`) FROM `message`;
SET @max := @max+1;
-- insert message
INSERT INTO `message` (`id`, `name`, `secured`,`text_template_file_name`,`html_template_file_name`,`created_at`) VALUES
  (@max,
   'welcome_notification',
   '0',
   'welcome_notification.txt',
   'welcome_notification.html',
   NOW()
  );

-- and template fr_FR
INSERT INTO `message_i18n` (`id`, `locale`, `title`, `subject`, `text_message`, `html_message`) VALUES
  (@max, 'fr_FR', 'Welcome notification', 'Confirmation d\'inscription sur SHOP', '{loop type="customer" name="customer.order" current="false" id="$customer_id" backend_context="1"}\r\nBonjour {$FIRSTNAME} {$LASTNAME} ,\r\n\r\nBienvenue sur {config key="store_name"}\r\n\r\nVous pouvez maintenant vous connecter au site :\r\nIdentifant : {$EMAIL}\r\n\r\nCordialement{/loop}', '{loop type="customer" name="customer.order" current="false" id="$customer_id" backend_context="1"}\r\nBonjour {$FIRSTNAME} {$LASTNAME} ,\r\n\r\nBienvenue sur <a href="{navigate to="index"}">{config key="store_name"}</a>\r\n\r\nVous pouvez maintenant vous connecter au site :\r\nIdentifant : {$EMAIL}\r\n\r\nCordialement{/loop}');

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;