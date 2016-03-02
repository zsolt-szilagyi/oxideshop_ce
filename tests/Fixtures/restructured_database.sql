SET @@session.sql_mode = '';

#
# Table structure for table `oxarticles_multilang`
# for storing oxarticle multilanguage fields
#

DROP TABLE IF EXISTS `oxarticles_multilang`;

CREATE TABLE `oxarticles_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'oxarticle.oxid article id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXSHORTDESC` varchar(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXURLDESC` varchar(255) NOT NULL default '' COMMENT 'Text for external URL (multilanguage)',
  `OXSTOCKTEXT` varchar(255) NOT NULL default '' COMMENT 'Message, which is shown if the article is in stock (multilanguage)',
  `OXNOSTOCKTEXT` varchar(255) NOT NULL default '' COMMENT 'Message, which is shown if the article is off stock (multilanguage)',
  `OXSEARCHKEYS` varchar(255) NOT NULL default '' COMMENT 'Search terms (multilanguage)',
  `OXVARNAME` varchar(255) NOT NULL default '' COMMENT 'Name of variants selection lists (different lists are separated by | ) (multilanguage)',
  `OXVARSELECT` varchar(255) NOT NULL default '' COMMENT 'Variant article selections (separated by | ) (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`),
  KEY `OXVARNAME` (`OXVARNAME`)
)ENGINE=InnoDB COMMENT 'oxarticles multilanguage data';


INSERT INTO `oxarticles_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXSHORTDESC`, `OXURLDESC`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXSEARCHKEYS`, `OXVARNAME`, `OXVARSELECT`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXSHORTDESC_DE`, `OXURLDESC_DE`, `OXSTOCKTEXT_DE`, `OXNOSTOCKTEXT_DE`, `OXSEARCHKEYS_DE`, `OXVARNAME_DE`, `OXVARSELECT_DE`, `OXTIMESTAMP` FROM `oxarticles`;

INSERT INTO `oxarticles_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXSHORTDESC`, `OXURLDESC`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXSEARCHKEYS`, `OXVARNAME`, `OXVARSELECT`, `OXTIMESTAMP` )
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXSHORTDESC_EN`, `OXURLDESC_EN`, `OXSTOCKTEXT_EN`, `OXNOSTOCKTEXT_EN`, `OXSEARCHKEYS_EN`, `OXVARNAME_EN`, `OXVARSELECT_EN`, `OXTIMESTAMP` FROM `oxarticles`;


ALTER TABLE oxarticles
DROP COLUMN OXTITLE,
DROP COLUMN OXSHORTDESC,
DROP COLUMN OXURLDESC,
DROP COLUMN OXSTOCKTEXT,
DROP COLUMN OXNOSTOCKTEXT,
DROP COLUMN OXSEARCHKEYS,
DROP COLUMN OXVARNAME,
DROP COLUMN OXVARSELECT,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXSHORTDESC_DE,
DROP COLUMN OXURLDESC_DE,
DROP COLUMN OXSTOCKTEXT_DE,
DROP COLUMN OXNOSTOCKTEXT_DE,
DROP COLUMN OXSEARCHKEYS_DE,
DROP COLUMN OXVARNAME_DE,
DROP COLUMN OXVARSELECT_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXSHORTDESC_EN,
DROP COLUMN OXURLDESC_EN,
DROP COLUMN OXSTOCKTEXT_EN,
DROP COLUMN OXNOSTOCKTEXT_EN,
DROP COLUMN OXSEARCHKEYS_EN,
DROP COLUMN OXVARNAME_EN,
DROP COLUMN OXVARSELECT_EN,
DROP COLUMN OXTITLE_FR,
DROP COLUMN OXSHORTDESC_FR,
DROP COLUMN OXURLDESC_FR,
DROP COLUMN OXSTOCKTEXT_FR,
DROP COLUMN OXNOSTOCKTEXT_FR,
DROP COLUMN OXSEARCHKEYS_FR,
DROP COLUMN OXVARNAME_FR,
DROP COLUMN OXVARSELECT_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles AS SELECT
oxarticles.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_de.OXSHORTDESC as OXSHORTDESC_DE,
mlang_de.OXURLDESC as OXURLDESC_DE,
mlang_de.OXSTOCKTEXT as OXSTOCKTEXT_DE,
mlang_de.OXNOSTOCKTEXT as OXNOSTOCKTEXT_DE,
mlang_de.OXSEARCHKEYS as OXSEARCHKEYS_DE,
mlang_de.OXVARNAME as OXVARNAME_DE,
mlang_de.OXVARSELECT as OXVARSELECT_DE,
mlang_en.OXTITLE as OXTITLE_EN,
mlang_en.OXSHORTDESC as OXSHORTDESC_EN,
mlang_en.OXURLDESC as OXURLDESC_EN,
mlang_en.OXSTOCKTEXT as OXSTOCKTEXT_EN,
mlang_en.OXNOSTOCKTEXT as OXNOSTOCKTEXT_EN,
mlang_en.OXSEARCHKEYS as OXSEARCHKEYS_EN,
mlang_en.OXVARNAME as OXVARNAME_EN,
mlang_en.OXVARSELECT as OXVARSELECT_EN
from oxarticles
LEFT JOIN oxarticles_multilang as mlang_de ON (oxarticles.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxarticles_multilang as mlang_en ON (oxarticles.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_de AS SELECT
oxarticles.*,
mlang.OXTITLE as OXTITLE,
mlang.OXSHORTDESC as OXSHORTDESC,
mlang.OXURLDESC as OXURLDESC,
mlang.OXSTOCKTEXT as OXSTOCKTEXT,
mlang.OXNOSTOCKTEXT as OXNOSTOCKTEXT,
mlang.OXSEARCHKEYS as OXSEARCHKEYS,
mlang.OXVARNAME as OXVARNAME,
mlang.OXVARSELECT as OXVARSELECT
from oxarticles
LEFT JOIN oxarticles_multilang as mlang ON (oxarticles.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_en AS SELECT
oxarticles.*,
mlang.OXTITLE as OXTITLE,
mlang.OXSHORTDESC as OXSHORTDESC,
mlang.OXURLDESC as OXURLDESC,
mlang.OXSTOCKTEXT as OXSTOCKTEXT,
mlang.OXNOSTOCKTEXT as OXNOSTOCKTEXT,
mlang.OXSEARCHKEYS as OXSEARCHKEYS,
mlang.OXVARNAME as OXVARNAME,
mlang.OXVARSELECT as OXVARSELECT
from oxarticles
LEFT JOIN oxarticles_multilang as mlang ON (oxarticles.OXID = mlang.OXID AND mlang.oxlang = 'en');

#
# Table structure for table `oxactions_multilang`
#

DROP TABLE IF EXISTS `oxactions_multilang`;

CREATE TABLE `oxactions_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Action id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` char(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXLONGDESC` text NOT NULL COMMENT 'Long description, used for promotion (multilanguage)',
  `OXPIC`   VARCHAR(128) NOT NULL DEFAULT '' COMMENT 'Picture filename, used for banner (multilanguage)',
  `OXLINK`   VARCHAR(128) NOT NULL DEFAULT '' COMMENT 'Link, used on banner (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'oxactions multilanguage multilanguage data';

INSERT INTO `oxactions_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXLONGDESC`, `OXPIC`, `OXLINK`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXLONGDESC_DE`, `OXPIC_DE`, `OXLINK_DE`, `OXTIMESTAMP` FROM `oxactions`;

INSERT INTO `oxactions_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXLONGDESC`, `OXPIC`, `OXLINK`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXLONGDESC_EN`, `OXPIC_EN`, `OXLINK_EN`, `OXTIMESTAMP` FROM `oxactions`;

ALTER TABLE oxactions
DROP COLUMN OXTITLE,
DROP COLUMN OXLONGDESC,
DROP COLUMN OXPIC,
DROP COLUMN OXLINK,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXLONGDESC_DE,
DROP COLUMN OXPIC_DE,
DROP COLUMN OXLINK_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXLONGDESC_EN,
DROP COLUMN OXPIC_EN,
DROP COLUMN OXLINK_EN,
DROP COLUMN OXTITLE_FR,
DROP COLUMN OXLONGDESC_FR,
DROP COLUMN OXPIC_FR,
DROP COLUMN OXLINK_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxactions AS SELECT
oxactions.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_de.OXLONGDESC as OXLONGDESC_DE,
mlang_de.OXPIC as OXPIC_DE,
mlang_de.OXLINK as OXLINK_DE,
mlang_en.OXTITLE as OXTITLE_EN,
mlang_en.OXLONGDESC as OXLONGDESC_EN,
mlang_en.OXPIC as OXPIC_EN,
mlang_en.OXLINK as OXLINK_EN
from oxactions
LEFT JOIN oxactions_multilang as mlang_de ON (oxactions.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxactions_multilang as mlang_en ON (oxactions.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxactions_de AS SELECT
oxactions.*,
mlang.OXTITLE as OXTITLE,
mlang.OXLONGDESC as OXLONGDESC,
mlang.OXPIC as OXPIC,
mlang.OXLINK as OXLINK
from oxactions
LEFT JOIN oxactions_multilang as mlang ON (oxactions.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxactions_en AS SELECT
oxactions.*,
mlang.OXTITLE as OXTITLE,
mlang.OXLONGDESC as OXLONGDESC,
mlang.OXPIC as OXPIC,
mlang.OXLINK as OXLINK
from oxactions
LEFT JOIN oxactions_multilang as mlang ON (oxactions.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxartextends_multilang`
#

DROP TABLE IF EXISTS `oxartextends_multilang`;

CREATE TABLE `oxartextends_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Article id (extends oxarticles article with this id)',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXLONGDESC` text NOT NULL COMMENT 'Long description (multilanguage)',
  `OXTAGS` varchar(255) NOT NULL COMMENT 'Tags (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`),
  FULLTEXT KEY `OXTAGS`   (`OXTAGS`)
) ENGINE=MyISAM COMMENT 'Additional information for articles';

INSERT INTO `oxartextends_multilang` (`OXID`, `OXLANG`, `OXLONGDESC`, `OXTAGS`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXLONGDESC_DE`, `OXTAGS_DE`, `OXTIMESTAMP` FROM `oxartextends`;
INSERT INTO `oxartextends_multilang` (`OXID`, `OXLANG`, `OXLONGDESC`, `OXTAGS`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXLONGDESC_EN`, `OXTAGS_EN`, `OXTIMESTAMP` FROM `oxartextends`;

ALTER TABLE oxartextends
DROP COLUMN OXLONGDESC,
DROP COLUMN OXTAGS,
DROP COLUMN OXLONGDESC_DE,
DROP COLUMN OXTAGS_DE,
DROP COLUMN OXLONGDESC_EN,
DROP COLUMN OXTAGS_EN,
DROP COLUMN OXLONGDESC_FR,
DROP COLUMN OXTAGS_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends AS SELECT
oxartextends.*,
mlang_de.OXLONGDESC as OXLONGDESC_DE,
mlang_de.OXTAGS as OXTAGS_DE,
mlang_en.OXLONGDESC as OXLONGDESC_EN,
mlang_en.OXTAGS as OXTAGS_EN
from oxartextends
LEFT JOIN oxartextends_multilang as mlang_de ON (oxartextends.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxartextends_multilang as mlang_en ON (oxartextends.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends_de AS SELECT
oxartextends.*,
mlang.OXLONGDESC as OXLONGDESC,
mlang.OXTAGS as OXTAGS
from oxartextends
LEFT JOIN oxartextends_multilang as mlang ON (oxartextends.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends_en AS SELECT
oxartextends.*,
mlang.OXLONGDESC as OXLONGDESC,
mlang.OXTAGS as OXTAGS
from oxartextends
LEFT JOIN oxartextends_multilang as mlang ON (oxartextends.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxattribute_multilang`
#

DROP TABLE IF EXISTS `oxattribute_multilang`;

CREATE TABLE `oxattribute_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Attribute id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` char(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Article attributes multilanguage data';

INSERT INTO `oxattribute_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXTIMESTAMP` FROM `oxattribute`;
INSERT INTO `oxattribute_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXTIMESTAMP` FROM `oxattribute`;

ALTER TABLE oxattribute
DROP COLUMN OXTITLE,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXTITLE_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxattribute AS SELECT
oxattribute.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_en.OXTITLE as OXTITLE_EN
from oxattribute
LEFT JOIN oxattribute_multilang as mlang_de ON (oxattribute.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxattribute_multilang as mlang_en ON (oxattribute.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxattribute_de AS SELECT
oxattribute.*,
mlang.OXTITLE as OXTITLE
from oxattribute
LEFT JOIN oxattribute_multilang as mlang ON (oxattribute.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxattribute_en AS SELECT
oxattribute.*,
mlang.OXTITLE as OXTITLE
from oxattribute
LEFT JOIN oxattribute_multilang as mlang ON (oxattribute.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxcategories_multilang`
#

CREATE TABLE `oxcategories_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Category id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active (multilanguage)',
  `OXTITLE` varchar(254) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXDESC` varchar(255) NOT NULL default '' COMMENT 'Description (multilanguage)',
  `OXLONGDESC` text NOT NULL COMMENT 'Long description (multilanguage)',
  `OXTHUMB` varchar(128) NOT NULL default '' COMMENT 'Thumbnail filename (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
   UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Article categories multilanguage data';

INSERT INTO `oxcategories_multilang` (`OXID`, `OXLANG`, `OXACTIVE`, `OXTITLE`, `OXDESC`, `OXLONGDESC`, `OXTHUMB`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXACTIVE_DE`, `OXTITLE_DE`, `OXDESC_DE`, `OXLONGDESC_DE`, `OXTHUMB_DE`, `OXTIMESTAMP` FROM `oxcategories`;
INSERT INTO `oxcategories_multilang` (`OXID`, `OXLANG`, `OXACTIVE`, `OXTITLE`, `OXDESC`, `OXLONGDESC`, `OXTHUMB`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXACTIVE_EN`, `OXTITLE_EN`, `OXDESC_EN`, `OXLONGDESC_EN`, `OXTHUMB_EN`, `OXTIMESTAMP` FROM `oxcategories`;

ALTER TABLE oxcategories
DROP COLUMN OXACTIVE,
DROP COLUMN OXTITLE,
DROP COLUMN OXDESC,
DROP COLUMN OXLONGDESC,
DROP COLUMN OXTHUMB,
DROP COLUMN OXACTIVE_DE,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXDESC_DE,
DROP COLUMN OXLONGDESC_DE,
DROP COLUMN OXTHUMB_DE,
DROP COLUMN OXACTIVE_EN,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXDESC_EN,
DROP COLUMN OXLONGDESC_EN,
DROP COLUMN OXTHUMB_EN,
DROP COLUMN OXACTIVE_FR,
DROP COLUMN OXTITLE_FR,
DROP COLUMN OXDESC_FR,
DROP COLUMN OXLONGDESC_FR,
DROP COLUMN OXTHUMB_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcategories AS SELECT
oxcategories.*,
mlang_de.OXACTIVE as OXACTIVE_DE,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_de.OXDESC as OXDESC_DE,
mlang_de.OXLONGDESC as OXLONGDESC_DE,
mlang_de.OXTHUMB as OXTHUMB_DE,
mlang_en.OXACTIVE as OXACTIVE_EN,
mlang_en.OXTITLE as OXTITLE_EN,
mlang_en.OXDESC as OXDESC_EN,
mlang_en.OXLONGDESC as OXLONGDESC_EN,
mlang_en.OXTHUMB as OXTHUMB_EN
from oxcategories
LEFT JOIN oxcategories_multilang as mlang_de ON (oxcategories.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxcategories_multilang as mlang_en ON (oxcategories.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcategories_de AS SELECT
oxcategories.*,
mlang.OXACTIVE as OXACTIVE,
mlang.OXTITLE as OXTITLE,
mlang.OXDESC as OXDESC,
mlang.OXLONGDESC as OXLONGDESC,
mlang.OXTHUMB as OXTHUMB
from oxcategories
LEFT JOIN oxcategories_multilang as mlang ON (oxcategories.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcategories_en AS SELECT
oxcategories.*,
mlang.OXACTIVE as OXACTIVE,
mlang.OXTITLE as OXTITLE,
mlang.OXDESC as OXDESC,
mlang.OXLONGDESC as OXLONGDESC,
mlang.OXTHUMB as OXTHUMB
from oxcategories
LEFT JOIN oxcategories_multilang as mlang ON (oxcategories.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxcontents_multilang`
#

CREATE TABLE `oxcontents_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Content id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active (multilanguage)',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXCONTENT` text NOT NULL COMMENT 'Content (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Content pages (Snippets, Menu, Categories, Manual) multilanguage information';

INSERT INTO `oxcontents_multilang` (`OXID`, `OXLANG`, `OXACTIVE`, `OXTITLE`, `OXCONTENT`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXACTIVE_DE`, `OXTITLE_DE`, `OXCONTENT_DE`, `OXTIMESTAMP` FROM `oxcontents`;
INSERT INTO `oxcontents_multilang` (`OXID`, `OXLANG`, `OXACTIVE`, `OXTITLE`, `OXCONTENT`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXACTIVE_EN`, `OXTITLE_EN`, `OXCONTENT_EN`, `OXTIMESTAMP` FROM `oxcontents`;

ALTER TABLE oxcontents
DROP COLUMN OXACTIVE,
DROP COLUMN OXTITLE,
DROP COLUMN OXCONTENT,
DROP COLUMN OXACTIVE_DE,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXCONTENT_DE,
DROP COLUMN OXACTIVE_EN,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXCONTENT_EN,
DROP COLUMN OXACTIVE_FR,
DROP COLUMN OXTITLE_FR,
DROP COLUMN OXCONTENT_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcontents AS SELECT
oxcontents.*,
mlang_de.OXACTIVE as OXACTIVE_DE,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_de.OXCONTENT as OXCONTENT_DE,
mlang_en.OXACTIVE as OXACTIVE_EN,
mlang_en.OXTITLE as OXTITLE_EN,
mlang_en.OXCONTENT as OXCONTENT_EN
from oxcontents
LEFT JOIN oxcontents_multilang as mlang_de ON (oxcontents.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxcontents_multilang as mlang_en ON (oxcontents.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcontents_de AS SELECT
oxcontents.*,
mlang.OXACTIVE as OXACTIVE,
mlang.OXTITLE as OXTITLE,
mlang.OXCONTENT as OXCONTENT
from oxcontents
LEFT JOIN oxcontents_multilang as mlang ON (oxcontents.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcontents_en AS SELECT
oxcontents.*,
mlang.OXACTIVE as OXACTIVE,
mlang.OXTITLE as OXTITLE,
mlang.OXCONTENT as OXCONTENT
from oxcontents
LEFT JOIN oxcontents_multilang as mlang ON (oxcontents.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxcountry_multilang`
#

DROP TABLE IF EXISTS `oxcountry_multilang`;

CREATE TABLE `oxcountry_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Country id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` char(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXSHORTDESC` char(128) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXLONGDESC` char(255) NOT NULL default '' COMMENT 'Long description (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Countries list';


INSERT INTO `oxcountry_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXSHORTDESC`, `OXLONGDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXSHORTDESC_DE`, `OXLONGDESC_DE`, `OXTIMESTAMP` FROM `oxcountry`;
INSERT INTO `oxcountry_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXSHORTDESC`, `OXLONGDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXSHORTDESC_EN`, `OXLONGDESC_EN`, `OXTIMESTAMP` FROM `oxcountry`;

ALTER TABLE oxcountry
DROP COLUMN OXTITLE,
DROP COLUMN OXSHORTDESC,
DROP COLUMN OXLONGDESC,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXSHORTDESC_DE,
DROP COLUMN OXLONGDESC_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXSHORTDESC_EN,
DROP COLUMN OXLONGDESC_EN,
DROP COLUMN OXTITLE_FR,
DROP COLUMN OXSHORTDESC_FR,
DROP COLUMN OXLONGDESC_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcountry AS SELECT
oxcountry.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_de.OXSHORTDESC as OXSHORTDESC_DE,
mlang_de.OXLONGDESC as OXLONGDESC_DE,
mlang_en.OXTITLE as OXTITLE_EN,
mlang_en.OXSHORTDESC as OXSHORTDESC_EN,
mlang_en.OXLONGDESC as OXLONGDESC_EN
from oxcountry
LEFT JOIN oxcountry_multilang as mlang_de ON (oxcountry.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxcountry_multilang as mlang_en ON (oxcountry.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcountry_de AS SELECT
oxcountry.*,
mlang.OXTITLE as OXTITLE,
mlang.OXSHORTDESC as OXSHORTDESC,
mlang.OXLONGDESC as OXLONGDESC
from oxcountry
LEFT JOIN oxcountry_multilang as mlang ON (oxcountry.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcountry_en AS SELECT
oxcountry.*,
mlang.OXTITLE as OXTITLE,
mlang.OXSHORTDESC as OXSHORTDESC,
mlang.OXLONGDESC as OXLONGDESC
from oxcountry
LEFT JOIN oxcountry_multilang as mlang ON (oxcountry.OXID = mlang.OXID AND mlang.oxlang = 'en');



#
# Table structure for table `oxdelivery_multilang`
#

DROP TABLE IF EXISTS `oxdelivery_multilang`;

CREATE TABLE `oxdelivery_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Delivery shipping cost rule id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
)  ENGINE=MyISAM COMMENT 'Delivery shipping cost rules';

INSERT INTO `oxdelivery_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXTIMESTAMP` FROM `oxdelivery`;
INSERT INTO `oxdelivery_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXTIMESTAMP` FROM `oxdelivery`;

ALTER TABLE oxdelivery
DROP COLUMN OXTITLE,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXTITLE_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery AS SELECT
oxdelivery.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_en.OXTITLE as OXTITLE_EN
from oxdelivery
LEFT JOIN oxdelivery_multilang as mlang_de ON (oxdelivery.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxdelivery_multilang as mlang_en ON (oxdelivery.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery_de AS SELECT
oxdelivery.*,
mlang.OXTITLE as OXTITLE_DE
from oxdelivery
LEFT JOIN oxdelivery_multilang as mlang ON (oxdelivery.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery_en AS SELECT
oxdelivery.*,
mlang.OXTITLE as OXTITLE_DE
from oxdelivery
LEFT JOIN oxdelivery_multilang as mlang ON (oxdelivery.OXID = mlang.OXID AND mlang.oxlang = 'en');

#
# Table structure for table `oxdeliveryset_multilang`
#

DROP TABLE IF EXISTS `oxdeliveryset_multilang`;

CREATE TABLE `oxdeliveryset_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Delivery method id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Creation time',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Delivery (shipping) methods';

INSERT INTO `oxdeliveryset_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXTIMESTAMP` FROM `oxdeliveryset`;
INSERT INTO `oxdeliveryset_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXTIMESTAMP` FROM `oxdeliveryset`;

ALTER TABLE oxdeliveryset
DROP COLUMN OXTITLE,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXTITLE_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdeliveryset AS SELECT
oxdeliveryset.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_en.OXTITLE as OXTITLE_EN
from oxdeliveryset
LEFT JOIN oxdeliveryset_multilang as mlang_de ON (oxdeliveryset.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxdeliveryset_multilang as mlang_en ON (oxdeliveryset.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdeliveryset_de AS SELECT
oxdeliveryset.*,
mlang.OXTITLE as OXTITLE
from oxdeliveryset
LEFT JOIN oxdeliveryset_multilang as mlang ON (oxdeliveryset.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdeliveryset_en AS SELECT
oxdeliveryset.*,
mlang.OXTITLE as OXTITLE
from oxdeliveryset
LEFT JOIN oxdeliveryset_multilang as mlang ON (oxdeliveryset.OXID = mlang.OXID AND mlang.oxlang = 'en');



#
# Table structure for table `oxdiscount_multilang`
#

DROP TABLE IF EXISTS `oxdiscount_multilang`;

CREATE TABLE `oxdiscount_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Discount id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Article discounts';

INSERT INTO `oxdiscount_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXTIMESTAMP` FROM `oxdiscount`;
INSERT INTO `oxdiscount_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXTIMESTAMP` FROM `oxdiscount`;

ALTER TABLE oxdiscount
DROP COLUMN OXTITLE,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXTITLE_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount AS SELECT
oxdiscount.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_en.OXTITLE as OXTITLE_EN
from oxdiscount
LEFT JOIN oxdiscount_multilang as mlang_de ON (oxdiscount.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxdiscount_multilang as mlang_en ON (oxdiscount.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount_de AS SELECT
oxdiscount.*,
mlang.OXTITLE as OXTITLE
from oxdiscount
LEFT JOIN oxdiscount_multilang as mlang ON (oxdiscount.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount_en AS SELECT
oxdiscount.*,
mlang.OXTITLE as OXTITLE
from oxdiscount
LEFT JOIN oxdiscount_multilang as mlang ON (oxdiscount.OXID = mlang.OXID AND mlang.oxlang = 'en');

#
# Table structure for table `oxgroups_multilang`
#

DROP TABLE IF EXISTS `oxgroups_multilang`;

CREATE TABLE `oxgroups_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Group id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'User groups';

INSERT INTO `oxgroups_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXTIMESTAMP` FROM `oxgroups`;
INSERT INTO `oxgroups_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXTIMESTAMP` FROM `oxgroups`;

ALTER TABLE oxgroups
DROP COLUMN OXTITLE,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXTITLE_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxgroups AS SELECT
oxgroups.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_en.OXTITLE as OXTITLE_EN
from oxgroups
LEFT JOIN oxgroups_multilang as mlang_de ON (oxgroups.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxgroups_multilang as mlang_en ON (oxgroups.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxgroups_de AS SELECT
oxgroups.*,
mlang.OXTITLE as OXTITLE
from oxgroups
LEFT JOIN oxgroups_multilang as mlang ON (oxgroups.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxgroups_en AS SELECT
oxgroups.*,
mlang.OXTITLE as OXTITLE
from oxgroups
LEFT JOIN oxgroups_multilang as mlang ON (oxgroups.OXID = mlang.OXID AND mlang.oxlang = 'en');



#
# Table structure for table `oxlinks_multilang`
#

DROP TABLE IF EXISTS `oxlinks_multilang`;

CREATE TABLE `oxlinks_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Link id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXURLDESC` text NOT NULL COMMENT 'Description (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Links';


INSERT INTO `oxlinks_multilang` (`OXID`, `OXLANG`, `OXURLDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXURLDESC_DE`, `OXTIMESTAMP` FROM `oxlinks`;
INSERT INTO `oxlinks_multilang` (`OXID`, `OXLANG`, `OXURLDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXURLDESC_EN`, `OXTIMESTAMP` FROM `oxlinks`;

ALTER TABLE oxlinks
DROP COLUMN OXURLDESC,
DROP COLUMN OXURLDESC_DE,
DROP COLUMN OXURLDESC_EN,
DROP COLUMN OXURLDESC_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxlinks AS SELECT
oxlinks.*,
mlang_de.OXURLDESC as OXURLDESC_DE,
mlang_en.OXURLDESC as OXURLDESC_EN
from oxlinks
LEFT JOIN oxlinks_multilang as mlang_de ON (oxlinks.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxlinks_multilang as mlang_en ON (oxlinks.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxlinks_de AS SELECT
oxlinks.*,
mlang.OXURLDESC as OXURLDESC
from oxlinks
LEFT JOIN oxlinks_multilang as mlang ON (oxlinks.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxlinks_en AS SELECT
oxlinks.*,
mlang.OXURLDESC as OXURLDESC
from oxlinks
LEFT JOIN oxlinks_multilang as mlang ON (oxlinks.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxmanufacturers_multilang`
#

DROP TABLE IF EXISTS `oxmanufacturers_multilang`;


CREATE TABLE `oxmanufacturers_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Manufacturer id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` char(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXSHORTDESC` char(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Shop manufacturers';

INSERT INTO `oxmanufacturers_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXSHORTDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXSHORTDESC_DE`, `OXTIMESTAMP` FROM `oxmanufacturers`;
INSERT INTO `oxmanufacturers_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXSHORTDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXSHORTDESC_EN`, `OXTIMESTAMP` FROM `oxmanufacturers`;

ALTER TABLE oxmanufacturers
DROP COLUMN OXTITLE,
DROP COLUMN OXSHORTDESC,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXSHORTDESC_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXSHORTDESC_EN,
DROP COLUMN OXTITLE_FR,
DROP COLUMN OXSHORTDESC_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmanufacturers AS SELECT
oxmanufacturers.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_de.OXSHORTDESC as OXSHORTDESC_DE,
mlang_en.OXTITLE as OXTITLE_EN,
mlang_en.OXSHORTDESC as OXSHORTDESC_EN
from oxmanufacturers
LEFT JOIN oxmanufacturers_multilang as mlang_de ON (oxmanufacturers.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxmanufacturers_multilang as mlang_en ON (oxmanufacturers.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmanufacturers_de AS SELECT
oxmanufacturers.*,
mlang.OXTITLE as OXTITLE,
mlang.OXSHORTDESC as OXSHORTDESC
from oxmanufacturers
LEFT JOIN oxmanufacturers_multilang as mlang ON (oxmanufacturers.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmanufacturers_en AS SELECT
oxmanufacturers.*,
mlang.OXTITLE as OXTITLE,
mlang.OXSHORTDESC as OXSHORTDESC
from oxmanufacturers
LEFT JOIN oxmanufacturers_multilang as mlang ON (oxmanufacturers.OXID = mlang.OXID AND mlang.oxlang = 'en');

#
# Table structure for table `oxmediaurls_multilang`
#

DROP TABLE IF EXISTS `oxmediaurls_multilang`;

CREATE TABLE `oxmediaurls_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Media id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXDESC` varchar(255) NOT NULL COMMENT 'Description (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE = MYISAM COMMENT 'Stores objects media';


INSERT INTO `oxmediaurls_multilang` (`OXID`, `OXLANG`, `OXDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXDESC_DE`, `OXTIMESTAMP` FROM `oxmediaurls`;
INSERT INTO `oxmediaurls_multilang` (`OXID`, `OXLANG`, `OXDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXDESC_EN`, `OXTIMESTAMP` FROM `oxmediaurls`;

ALTER TABLE oxmediaurls
DROP COLUMN OXDESC,
DROP COLUMN OXDESC_DE,
DROP COLUMN OXDESC_EN,
DROP COLUMN OXDESC_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmediaurls AS SELECT
oxmediaurls.*,
mlang_de.OXDESC as OXDESC_DE,
mlang_en.OXDESC as OXDESC_EN
from oxmediaurls
LEFT JOIN oxmediaurls_multilang as mlang_de ON (oxmediaurls.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxmediaurls_multilang as mlang_en ON (oxmediaurls.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmediaurls_de AS SELECT
oxmediaurls.*,
mlang.OXDESC as OXDESC
from oxmediaurls
LEFT JOIN oxmediaurls_multilang as mlang ON (oxmediaurls.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmediaurls_en AS SELECT
oxmediaurls.*,
mlang.OXDESC as OXDESC
from oxmediaurls
LEFT JOIN oxmediaurls_multilang as mlang ON (oxmediaurls.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxnews_multilang`
#

CREATE TABLE `oxnews_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'News id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Is active',
  `OXSHORTDESC` varchar(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXLONGDESC` text NOT NULL COMMENT 'Long description (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Shop news';

INSERT INTO `oxnews_multilang` (`OXID`, `OXLANG`, `OXACTIVE`, `OXSHORTDESC`, `OXLONGDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXACTIVE_DE`, `OXSHORTDESC_DE`, `OXLONGDESC_DE`, `OXTIMESTAMP` FROM `oxnews`;
INSERT INTO `oxnews_multilang` (`OXID`, `OXLANG`, `OXACTIVE`, `OXSHORTDESC`, `OXLONGDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXACTIVE_DE`, `OXSHORTDESC_EN`, `OXLONGDESC_EN`, `OXTIMESTAMP` FROM `oxnews`;

ALTER TABLE oxnews
DROP COLUMN OXACTIVE,
DROP COLUMN OXSHORTDESC,
DROP COLUMN OXLONGDESC,
DROP COLUMN OXACTIVE_DE,
DROP COLUMN OXSHORTDESC_DE,
DROP COLUMN OXLONGDESC_DE,
DROP COLUMN OXACTIVE_EN,
DROP COLUMN OXSHORTDESC_EN,
DROP COLUMN OXLONGDESC_EN,
DROP COLUMN OXACTIVE_FR,
DROP COLUMN OXSHORTDESC_FR,
DROP COLUMN OXLONGDESC_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxnews AS SELECT
oxnews.*,
mlang_de.OXACTIVE    as OXACTIVE_DE,
mlang_de.OXSHORTDESC as OXSHORTDESC_DE,
mlang_de.OXLONGDESC  as OXLONGDESC_DE,
mlang_en.OXACTIVE    as OXACTIVE_EN,
mlang_en.OXSHORTDESC as OXSHORTDESC_EN,
mlang_en.OXLONGDESC  as OXLONGDESC_EN
from oxnews
LEFT JOIN oxnews_multilang as mlang_de ON (oxnews.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxnews_multilang as mlang_en ON (oxnews.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxnews_de AS SELECT
oxnews.*,
mlang.OXACTIVE as OXACTIVE,
mlang.OXSHORTDESC as OXSHORTDESC,
mlang.OXLONGDESC as OXLONGDESC
from oxnews
LEFT JOIN oxnews_multilang as mlang ON (oxnews.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxnews_en AS SELECT
oxnews.*,
mlang.OXACTIVE as OXACTIVE,
mlang.OXSHORTDESC as OXSHORTDESC,
mlang.OXLONGDESC as OXLONGDESC
from oxnews
LEFT JOIN oxnews_multilang as mlang ON (oxnews.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxobject2attribute_multilang`
#

DROP TABLE IF EXISTS `oxobject2attribute_multilang`;

CREATE TABLE `oxobject2attribute_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXVALUE` char(255) NOT NULL default '' COMMENT 'Attribute value (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between articles and attributes';


INSERT INTO `oxobject2attribute_multilang` (`OXID`, `OXLANG`, `OXVALUE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXVALUE_DE`, `OXTIMESTAMP` FROM `oxobject2attribute`;
INSERT INTO `oxobject2attribute_multilang` (`OXID`, `OXLANG`, `OXVALUE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXVALUE_EN`, `OXTIMESTAMP` FROM `oxobject2attribute`;

ALTER TABLE oxobject2attribute
DROP COLUMN OXVALUE,
DROP COLUMN OXVALUE_DE,
DROP COLUMN OXVALUE_EN,
DROP COLUMN OXVALUE_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxobject2attribute AS SELECT
oxobject2attribute.*,
mlang_de.OXVALUE as OXVALUE_DE,
mlang_en.OXVALUE as OXVALUE_EN
from oxobject2attribute
LEFT JOIN oxobject2attribute_multilang as mlang_de ON (oxobject2attribute.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxobject2attribute_multilang as mlang_en ON (oxobject2attribute.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxobject2attribute_de AS SELECT
oxobject2attribute.*,
mlang.OXVALUE as OXVALUE
from oxobject2attribute
LEFT JOIN oxobject2attribute_multilang as mlang ON (oxobject2attribute.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxobject2attribute_en AS SELECT
oxobject2attribute.*,
mlang.OXVALUE as OXVALUE
from oxobject2attribute
LEFT JOIN oxobject2attribute_multilang as mlang ON (oxobject2attribute.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxpayments_multilang`
#

DROP TABLE IF EXISTS `oxpayments_multilang`;

CREATE TABLE `oxpayments_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Payment id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXDESC` varchar(128) NOT NULL default '' COMMENT 'Description (multilanguage)',
  `OXVALDESC` text NOT NULL COMMENT 'Payment additional fields, separated by "field1__@@field2" (multilanguage)',
  `OXLONGDESC` text NOT NULL default '' COMMENT 'Long description (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Payment methods';

INSERT INTO `oxpayments_multilang` (`OXID`, `OXLANG`, `OXDESC`, `OXVALDESC`, `OXLONGDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXDESC_DE`, `OXVALDESC_DE`, `OXLONGDESC_DE`, `OXTIMESTAMP` FROM `oxpayments`;
INSERT INTO `oxpayments_multilang` (`OXID`, `OXLANG`, `OXDESC`, `OXVALDESC`, `OXLONGDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXDESC_EN`, `OXVALDESC_EN`, `OXLONGDESC_EN`, `OXTIMESTAMP` FROM `oxpayments`;

ALTER TABLE oxpayments
DROP COLUMN OXDESC,
DROP COLUMN OXVALDESC,
DROP COLUMN OXLONGDESC,
DROP COLUMN OXDESC_DE,
DROP COLUMN OXVALDESC_DE,
DROP COLUMN OXLONGDESC_DE,
DROP COLUMN OXDESC_EN,
DROP COLUMN OXVALDESC_EN,
DROP COLUMN OXLONGDESC_EN,
DROP COLUMN OXDESC_FR,
DROP COLUMN OXVALDESC_FR,
DROP COLUMN OXLONGDESC_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxpayments AS SELECT
oxpayments.*,
mlang_de.OXDESC as OXDESC_DE,
mlang_de.OXDESC as OXVALDESC_DE,
mlang_de.OXDESC as OXLONGDESC_DE,
mlang_en.OXDESC as OXDESC_EN,
mlang_de.OXDESC as OXVALDESC_EN,
mlang_de.OXDESC as OXLONGDESC_EN
from oxpayments
LEFT JOIN oxpayments_multilang as mlang_de ON (oxpayments.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxpayments_multilang as mlang_en ON (oxpayments.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxpayments_de AS SELECT
oxpayments.*,
mlang.OXDESC as OXDESC,
mlang.OXDESC as OXVALDESC,
mlang.OXDESC as OXLONGDESC
from oxpayments
LEFT JOIN oxpayments_multilang as mlang ON (oxpayments.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxpayments_en AS SELECT
oxpayments.*,
mlang.OXDESC as OXDESC,
mlang.OXDESC as OXVALDESC,
mlang.OXDESC as OXLONGDESC
from oxpayments
LEFT JOIN oxpayments_multilang as mlang ON (oxpayments.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxselectlist_multilang`
#

DROP TABLE IF EXISTS `oxselectlist_multilang`;

CREATE TABLE `oxselectlist_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Selection list id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` varchar(254) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXVALDESC` text NOT NULL COMMENT 'List fields, separated by "[field_name]!P![price]__@@[field_name]__@@" (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Selection lists';


INSERT INTO `oxselectlist_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXVALDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXVALDESC_DE`, `OXTIMESTAMP` FROM `oxselectlist`;
INSERT INTO `oxselectlist_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXVALDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXVALDESC_EN`, `OXTIMESTAMP` FROM `oxselectlist`;


ALTER TABLE oxselectlist
DROP COLUMN OXTITLE,
DROP COLUMN OXVALDESC,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXVALDESC_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXVALDESC_EN,
DROP COLUMN OXTITLE_FR,
DROP COLUMN OXVALDESC_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist AS SELECT
oxselectlist.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_de.OXVALDESC as OXVALDESC_DE,
mlang_en.OXTITLE as OXTITLE_EN,
mlang_en.OXVALDESC as OXVALDESC_EN
from oxselectlist
LEFT JOIN oxselectlist_multilang as mlang_de ON (oxselectlist.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxselectlist_multilang as mlang_en ON (oxselectlist.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist_de AS SELECT
oxselectlist.*,
mlang.OXTITLE as OXTITLE,
mlang.OXVALDESC as OXVALDESC
from oxselectlist
LEFT JOIN oxselectlist_multilang as mlang ON (oxselectlist.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist_en AS SELECT
oxselectlist.*,
mlang.OXTITLE as OXTITLE,
mlang.OXVALDESC as OXVALDESC
from oxselectlist
LEFT JOIN oxselectlist_multilang as mlang ON (oxselectlist.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxshops_multilang`
#

DROP TABLE IF EXISTS `oxshops_multilang`;

CREATE TABLE `oxshops_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Shop id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLEPREFIX` varchar(255) NOT NULL default '' COMMENT 'Seo title prefix (multilanguage)',
  `OXTITLESUFFIX` varchar(255) NOT NULL default '' COMMENT 'Seo title suffix (multilanguage)',
  `OXSTARTTITLE` varchar(255) NOT NULL default '' COMMENT 'Start page title (multilanguage)',
  `OXORDERSUBJECT` varchar(255) NOT NULL default '' COMMENT 'Order email subject (multilanguage)',
  `OXREGISTERSUBJECT` varchar(255) NOT NULL default '' COMMENT 'Registration email subject (multilanguage)',
  `OXFORGOTPWDSUBJECT` varchar(255) NOT NULL default '' COMMENT 'Forgot password email subject (multilanguage)',
  `OXSENDEDNOWSUBJECT` varchar(255) NOT NULL default '' COMMENT 'Order sent email subject (multilanguage)',
  `OXSEOACTIVE` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Seo active (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Shop config';

INSERT INTO `oxshops_multilang` (`OXID`, `OXLANG`, `OXTITLEPREFIX`, `OXTITLESUFFIX`, `OXSTARTTITLE`, `OXORDERSUBJECT`, `OXREGISTERSUBJECT`, `OXFORGOTPWDSUBJECT`, `OXSENDEDNOWSUBJECT`, `OXSEOACTIVE`,`OXTIMESTAMP`)
        SELECT `OXID`, 'de',  `OXTITLEPREFIX_DE`, `OXTITLESUFFIX_DE`, `OXSTARTTITLE_DE`, `OXORDERSUBJECT_DE`, `OXREGISTERSUBJECT_DE`, `OXFORGOTPWDSUBJECT_DE`, `OXSENDEDNOWSUBJECT_DE`, `OXSEOACTIVE_DE`, `OXTIMESTAMP` FROM `oxshops`;
INSERT INTO `oxshops_multilang` (`OXID`, `OXLANG`, `OXTITLEPREFIX`, `OXTITLESUFFIX`, `OXSTARTTITLE`, `OXORDERSUBJECT`, `OXREGISTERSUBJECT`, `OXFORGOTPWDSUBJECT`, `OXSENDEDNOWSUBJECT`, `OXSEOACTIVE`,`OXTIMESTAMP`)
        SELECT `OXID`, 'en',  `OXTITLEPREFIX_EN`, `OXTITLESUFFIX_EN`, `OXSTARTTITLE_EN`, `OXORDERSUBJECT_EN`, `OXREGISTERSUBJECT_EN`, `OXFORGOTPWDSUBJECT_EN`, `OXSENDEDNOWSUBJECT_EN`, `OXSEOACTIVE_EN`, `OXTIMESTAMP` FROM `oxshops`;

ALTER TABLE oxshops
DROP COLUMN OXTITLEPREFIX,
DROP COLUMN OXTITLESUFFIX,
DROP COLUMN OXSTARTTITLE,
DROP COLUMN OXORDERSUBJECT,
DROP COLUMN OXREGISTERSUBJECT,
DROP COLUMN OXFORGOTPWDSUBJECT,
DROP COLUMN OXSENDEDNOWSUBJECT,
DROP COLUMN OXSEOACTIVE,
DROP COLUMN OXTITLEPREFIX_DE,
DROP COLUMN OXTITLESUFFIX_DE,
DROP COLUMN OXSTARTTITLE_DE,
DROP COLUMN OXORDERSUBJECT_DE,
DROP COLUMN OXREGISTERSUBJECT_DE,
DROP COLUMN OXFORGOTPWDSUBJECT_DE,
DROP COLUMN OXSENDEDNOWSUBJECT_DE,
DROP COLUMN OXSEOACTIVE_DE,
DROP COLUMN OXTITLEPREFIX_EN,
DROP COLUMN OXTITLESUFFIX_EN,
DROP COLUMN OXSTARTTITLE_EN,
DROP COLUMN OXORDERSUBJECT_EN,
DROP COLUMN OXREGISTERSUBJECT_EN,
DROP COLUMN OXFORGOTPWDSUBJECT_EN,
DROP COLUMN OXSENDEDNOWSUBJECT_EN,
DROP COLUMN OXSEOACTIVE_EN,
DROP COLUMN OXTITLEPREFIX_FR,
DROP COLUMN OXTITLESUFFIX_FR,
DROP COLUMN OXSTARTTITLE_FR,
DROP COLUMN OXORDERSUBJECT_FR,
DROP COLUMN OXREGISTERSUBJECT_FR,
DROP COLUMN OXFORGOTPWDSUBJECT_FR,
DROP COLUMN OXSENDEDNOWSUBJECT_FR,
DROP COLUMN OXSEOACTIVE_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops AS SELECT
oxshops.*,
mlang_de.OXTITLEPREFIX AS OXTITLEPREFIX_DE,
mlang_de.OXTITLESUFFIX AS OXTITLESUFFIX_DE,
mlang_de.OXSTARTTITLE AS OXSTARTTITLE_DE,
mlang_de.OXORDERSUBJECT AS OXORDERSUBJECT_DE,
mlang_de.OXREGISTERSUBJECT AS OXREGISTERSUBJECT_DE,
mlang_de.OXFORGOTPWDSUBJECT AS OXFORGOTPWDSUBJECT_DE,
mlang_de.OXSENDEDNOWSUBJECT AS OXSENDEDNOWSUBJECT_DE,
mlang_de.OXSEOACTIVE AS OXSEOACTIVE_DE,
mlang_en.OXTITLEPREFIX AS OXTITLEPREFIX_EN,
mlang_en.OXTITLESUFFIX AS OXTITLESUFFIX_EN,
mlang_en.OXSTARTTITLE AS OXSTARTTITLE_EN,
mlang_en.OXORDERSUBJECT AS OXORDERSUBJECT_EN,
mlang_en.OXREGISTERSUBJECT AS OXREGISTERSUBJECT_EN,
mlang_en.OXFORGOTPWDSUBJECT AS OXFORGOTPWDSUBJECT_EN,
mlang_en.OXSENDEDNOWSUBJECT AS OXSENDEDNOWSUBJECT_EN,
mlang_en.OXSEOACTIVE AS OXSEOACTIVE_EN
from oxshops
LEFT JOIN oxshops_multilang as mlang_de ON (oxshops.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxshops_multilang as mlang_en ON (oxshops.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops_de AS SELECT
oxshops.*,
mlang.OXTITLEPREFIX AS OXTITLEPREFIX,
mlang.OXTITLESUFFIX AS OXTITLESUFFIX,
mlang.OXSTARTTITLE AS OXSTARTTITLE,
mlang.OXORDERSUBJECT AS OXORDERSUBJECT,
mlang.OXREGISTERSUBJECT AS OXREGISTERSUBJECT,
mlang.OXFORGOTPWDSUBJECT AS OXFORGOTPWDSUBJECT,
mlang.OXSENDEDNOWSUBJECT AS OXSENDEDNOWSUBJECT,
mlang.OXSEOACTIVE AS OXSEOACTIVE
from oxshops
LEFT JOIN oxshops_multilang as mlang ON (oxshops.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops_en AS SELECT
oxshops.*,
mlang.OXTITLEPREFIX AS OXTITLEPREFIX,
mlang.OXTITLESUFFIX AS OXTITLESUFFIX,
mlang.OXSTARTTITLE AS OXSTARTTITLE,
mlang.OXORDERSUBJECT AS OXORDERSUBJECT,
mlang.OXREGISTERSUBJECT AS OXREGISTERSUBJECT,
mlang.OXFORGOTPWDSUBJECT AS OXFORGOTPWDSUBJECT,
mlang.OXSENDEDNOWSUBJECT AS OXSENDEDNOWSUBJECT,
mlang.OXSEOACTIVE AS OXSEOACTIVE
from oxshops
LEFT JOIN oxshops_multilang as mlang ON (oxshops.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxstates_multilang`
#

DROP TABLE IF EXISTS `oxstates_multilang`;

CREATE TABLE `oxstates_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'State id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` char(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE = MYISAM COMMENT 'US States list';

INSERT INTO `oxstates_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXTIMESTAMP` FROM `oxstates`;
INSERT INTO `oxstates_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXTIMESTAMP` FROM `oxstates`;

ALTER TABLE oxstates
DROP COLUMN OXTITLE,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXTITLE_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxstates AS SELECT
oxstates.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_en.OXTITLE as OXTITLE_EN
from oxstates
LEFT JOIN oxstates_multilang as mlang_de ON (oxstates.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxstates_multilang as mlang_en ON (oxstates.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxstates_de AS SELECT
oxstates.*,
mlang.OXTITLE as OXTITLE
from oxstates
LEFT JOIN oxstates_multilang as mlang ON (oxstates.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxstates_en AS SELECT
oxstates.*,
mlang.OXTITLE as OXTITLE
from oxstates
LEFT JOIN oxstates_multilang as mlang ON (oxstates.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxvendor_multilang`
#

DROP TABLE IF EXISTS `oxvendor_multilang`;

CREATE TABLE `oxvendor_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Vendor id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXTITLE` char(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXSHORTDESC` char(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Distributors list';

INSERT INTO `oxvendor_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXSHORTDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXTITLE_DE`, `OXSHORTDESC_DE`, `OXTIMESTAMP` FROM `oxvendor`;
INSERT INTO `oxvendor_multilang` (`OXID`, `OXLANG`, `OXTITLE`, `OXSHORTDESC`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXTITLE_EN`, `OXSHORTDESC_EN`, `OXTIMESTAMP` FROM `oxvendor`;

ALTER TABLE oxvendor
DROP COLUMN OXTITLE,
DROP COLUMN OXSHORTDESC,
DROP COLUMN OXTITLE_DE,
DROP COLUMN OXSHORTDESC_DE,
DROP COLUMN OXTITLE_EN,
DROP COLUMN OXSHORTDESC_EN,
DROP COLUMN OXTITLE_FR,
DROP COLUMN OXSHORTDESC_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxvendor AS SELECT
oxvendor.*,
mlang_de.OXTITLE as OXTITLE_DE,
mlang_de.OXSHORTDESC as OXSHORTDESC_DE,
mlang_en.OXTITLE as OXTITLE_EN,
mlang_en.OXSHORTDESC as OXSHORTDESC_EN
from oxvendor
LEFT JOIN oxvendor_multilang as mlang_de ON (oxvendor.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxvendor_multilang as mlang_en ON (oxvendor.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxvendor_de AS SELECT
oxvendor.*,
mlang.OXTITLE as OXTITLE,
mlang.OXSHORTDESC as OXSHORTDESC
from oxvendor
LEFT JOIN oxvendor_multilang as mlang ON (oxvendor.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxvendor_en AS SELECT
oxvendor.*,
mlang.OXTITLE as OXTITLE,
mlang.OXSHORTDESC as OXSHORTDESC
from oxvendor
LEFT JOIN oxvendor_multilang as mlang ON (oxvendor.OXID = mlang.OXID AND mlang.oxlang = 'en');


#
# Table structure for table `oxwrapping_multilang`
#

DROP TABLE IF EXISTS `oxwrapping_multilang`;

CREATE TABLE `oxwrapping_multilang` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Wrapping id',
  `OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active (multilanguage)',
  `OXNAME` varchar(128) NOT NULL default '' COMMENT 'Name (multilanguage)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)
) ENGINE=MyISAM COMMENT 'Wrappings';

INSERT INTO `oxwrapping_multilang` (`OXID`, `OXLANG`, `OXNAME`, `OXTIMESTAMP`)
        SELECT `OXID`, 'de', `OXNAME_DE`, `OXTIMESTAMP` FROM `oxwrapping`;
INSERT INTO `oxwrapping_multilang` (`OXID`, `OXLANG`, `OXNAME`, `OXTIMESTAMP`)
        SELECT `OXID`, 'en', `OXNAME_EN`, `OXTIMESTAMP` FROM `oxwrapping`;

ALTER TABLE oxwrapping
DROP COLUMN OXNAME,
DROP COLUMN OXNAME_DE,
DROP COLUMN OXNAME_EN,
DROP COLUMN OXNAME_FR;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxwrapping AS SELECT
oxwrapping.*,
mlang_de.OXNAME as OXNAME_DE,
mlang_en.OXNAME as OXNAME_EN
from oxwrapping
LEFT JOIN oxwrapping_multilang as mlang_de ON (oxwrapping.OXID = mlang_de.OXID AND mlang_de.oxlang = 'de')
LEFT JOIN oxwrapping_multilang as mlang_en ON (oxwrapping.OXID = mlang_en.OXID AND mlang_en.oxlang = 'en');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxwrapping_de AS SELECT
oxwrapping.*,
mlang.OXNAME as OXNAME
from oxwrapping
LEFT JOIN oxwrapping_multilang as mlang ON (oxwrapping.OXID = mlang.OXID AND mlang.oxlang = 'de');

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxwrapping_en AS SELECT
oxwrapping.*,
mlang.OXNAME as OXNAME
from oxwrapping
LEFT JOIN oxwrapping_multilang as mlang ON (oxwrapping.OXID = mlang.OXID AND mlang.oxlang = 'de');
