<?php

/**
 * Installer for Redirect Rules by ThemeHouse.
 */
class ThemeHouse_RedirectRules_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/redirect-rules-by-th.3847/';

    /**
     * Gets the tables (with fields) to be created for this add-on.
     * See parent for explanation.
     *
     * @return array Format: [table name] => fields
     */
    protected function _getTables()
    {
        return array(
            'xf_redirect_rule_th' => array(
                'redirect_rule_id' => 'int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'redirect_rule_id' */
                'title' => 'varchar(255) NOT NULL', /* END 'title' */
                'reroute_domain' => 'varchar(255) NOT NULL DEFAULT \'\'', /* END 'reroute_domain' */
                'active' => 'tinyint NOT NULL DEFAULT 1', /* END 'active' */
                'display_order' => 'int NOT NULL DEFAULT 1', /* END 'display_order' */
                'user_criteria' => 'MEDIUMBLOB', /* END 'user_criteria' */
                'page_criteria' => 'MEDIUMBLOB', /* END 'page_criteria' */
            ), /* END 'xf_redirect_rule_th' */
        );
    } /* END _getTables */


    protected function _postInstall()
    {
        $addOn = $this->getModelFromCache('XenForo_Model_AddOn')->getAddOnById('YoYo_');
        if ($addOn) {
            $db->query("
                INSERT INTO xf_redirect_rule_th (redirect_rule_id, title, reroute_domain, active, display_order, user_criteria, page_criteria)
                    SELECT redirect_rule_id, title, reroute_domain, active, display_order, user_criteria, page_criteria
                        FROM xf_redirect_rule_waindigo"); 
        }
    }
}