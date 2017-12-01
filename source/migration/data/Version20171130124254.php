<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add multilingual external links for Category
 */
class Version20171130124254 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER table `oxcategories` ADD `OXEXTLINK_1` varchar(255) COLLATE utf8_general_ci NOT NULL default '' AFTER `OXEXTLINK`;");
        $this->addSql("ALTER table `oxcategories` ADD `OXEXTLINK_2` varchar(255) COLLATE utf8_general_ci NOT NULL default '' AFTER `OXEXTLINK_1`;");
        $this->addSql("ALTER table `oxcategories` ADD `OXEXTLINK_3` varchar(255) COLLATE utf8_general_ci NOT NULL default '' AFTER `OXEXTLINK_2`;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
