CREATE TABLE IF NOT EXISTS `post_category` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user` INTEGER NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `parent` INTEGER DEFAULT 0,
    `about` TEXT,
    
    `meta_schema` VARCHAR(20),
    `meta_title` VARCHAR(100),
    `meta_description` TEXT,
    `meta_keywords` VARCHAR(200),
    
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `post_category_chain` (
    `id` INTEGER NOT NULL AUTO_INCREMENT KEY,
    `user` INTEGER NOT NULL,
    `post` INTEGER NOT NULL,
    `post_category` INTEGER NOT NULL,
    
    `post_status` TINYINT DEFAULT 1,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO `site_param` ( `name`, `type`, `group`, `value` ) VALUES
    ( 'post_category_index_enable', 4, 'Post Category', '0' ),
    ( 'post_category_index_meta_title', 1, 'Post Category', 'Post Categories' ),
    ( 'post_category_index_meta_description',  5, 'Post Category', 'List of post categories' ),
    ( 'post_category_index_meta_keywords', 1, 'Post Category', '' );