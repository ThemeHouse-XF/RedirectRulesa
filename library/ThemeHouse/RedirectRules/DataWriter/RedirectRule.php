<?php

/**
 * Data writer for redirect rules.
 */
class ThemeHouse_RedirectRules_DataWriter_RedirectRule extends XenForo_DataWriter
{

    /**
     * Title of the phrase that will be created when a call to set the
     * existing data fails (when the data doesn't exist).
     *
     * @var string
     */
    protected $_existingDataErrorPhrase = 'th_requested_redirect_rule_not_found_redirectrules';

    /**
     * Gets the fields that are defined for the table.
     * See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'xf_redirect_rule_th' => array(
                'redirect_rule_id' => array(
                    'type' => self::TYPE_UINT,
                    'autoIncrement' => true
                ), /* END 'redirect_rule_id' */
                'title' => array(
                    'type' => self::TYPE_STRING,
                    'required' => true
                ), /* END 'title' */
                'reroute_domain' => array(
                    'type' => self::TYPE_STRING,
                    'required' => true
                ), /* END 'reroute_domain' */
                'active' => array(
                    'type' => self::TYPE_BOOLEAN,
                    'default' => 1
                ),
                'display_order' => array(
                    'type' => self::TYPE_UINT,
                    'default' => 1
                ),
                'user_criteria' => array(
                    'type' => self::TYPE_UNKNOWN,
                    'required' => true,
                    'verification' => array(
                        '$this',
                        '_verifyCriteria'
                    )
                ),
                'page_criteria' => array(
                    'type' => self::TYPE_UNKNOWN,
                    'required' => true,
                    'verification' => array(
                        '$this',
                        '_verifyCriteria'
                    )
                )
            )
            , /* END 'xf_redirect_rule_th' */
        );
    } /* END _getFields */

    /**
     * Gets the actual existing data out of data that was passed in.
     * See parent for explanation.
     *
     * @param mixed
     *
     * @return array false
     */
    protected function _getExistingData($data)
    {
        if (!$redirectRuleId = $this->_getExistingPrimaryKey($data, 'redirect_rule_id')) {
            return false;
        }

        $redirectRule = $this->_getRedirectRuleModel()->getRedirectRuleById($redirectRuleId);
        if (!$redirectRule) {
            return false;
        }

        return $this->getTablesDataFromArray($redirectRule);
    } /* END _getExistingData */

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'redirect_rule_id = ' . $this->_db->quote($this->getExisting('redirect_rule_id'));
    } /* END _getUpdateCondition */

    /**
     * Verifies that the criteria is valid and formats is correctly.
     * Expected input format: [] with children: [rule] => name, [data] => info
     *
     * @param array|string $criteria Criteria array or serialize string; see
     * above for format. Modified by ref.
     *
     * @return boolean
     */
    protected function _verifyCriteria(&$criteria)
    {
        $criteriaFiltered = XenForo_Helper_Criteria::prepareCriteriaForSave($criteria);
        $criteria = serialize($criteriaFiltered);
        return true;
    } /* END _verifyCriteria */


    /**
     * Post-save handling.
     */
    protected function _postSave()
    {
        $this->_rebuildRedirectRuleCache();
    } /* END _postSave */

    /**
     * Post-delete handling.
     */
    protected function _postDelete()
    {
        $this->_rebuildRedirectRuleCache();
    } /* END _postDelete */

    /**
     * Rebuilds the redirect rule cache.
     */
    protected function _rebuildRedirectRuleCache()
    {
        $this->_getRedirectRuleModel()->rebuildRedirectRuleCache();
    } /* END _rebuildRedirectRuleCache */

    /**
     * Get the redirect rules model.
     *
     * @return ThemeHouse_RedirectRules_Model_RedirectRule
     */
    protected function _getRedirectRuleModel()
    {
        return $this->getModelFromCache('ThemeHouse_RedirectRules_Model_RedirectRule');
    } /* END _getRedirectRuleModel */
}