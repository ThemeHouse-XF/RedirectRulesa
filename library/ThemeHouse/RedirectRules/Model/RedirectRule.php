<?php

/**
 * Model for redirect rules.
 */
class ThemeHouse_RedirectRules_Model_RedirectRule extends XenForo_Model
{

    /**
     * Gets redirect rules that match the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions
     *
     * @return array [redirect rule id] => info.
     */
    public function getRedirectRules(array $conditions = array(), array $fetchOptions = array())
    {
        $whereClause = $this->prepareRedirectRuleConditions($conditions, $fetchOptions);

        $sqlClauses = $this->prepareRedirectRuleFetchOptions($fetchOptions);
        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        return $this->fetchAllKeyed(
            $this->limitQueryResults(
                '
                SELECT redirect_rule.*
                    ' . $sqlClauses['selectFields'] . '
                FROM xf_redirect_rule_th AS redirect_rule
                ' . $sqlClauses['joinTables'] . '
                WHERE ' . $whereClause . '
                ' . $sqlClauses['orderClause'] . '
            ', $limitOptions['limit'], $limitOptions['offset']),
            'redirect_rule_id');
    } /* END getRedirectRules */

    /**
     * Gets the redirect rule that matches the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions Options that affect what is fetched.
     *
     * @return array false
     */
    public function getRedirectRule(array $conditions = array(), array $fetchOptions = array())
    {
        $redirectRules = $this->getRedirectRules($conditions, $fetchOptions);

        return reset($redirectRules);
    } /* END getRedirectRule */

    /**
     * Gets a redirect rule by ID.
     *
     * @param integer $redirectRuleId
     * @param array $fetchOptions Options that affect what is fetched.
     *
     * @return array false
     */
    public function getRedirectRuleById($redirectRuleId, array $fetchOptions = array())
    {
        $conditions = array(
            'redirect_rule_id' => $redirectRuleId
        );

        return $this->getRedirectRule($conditions, $fetchOptions);
    } /* END getRedirectRuleById */

    /**
     * Gets the total number of a redirect rule that match the specified
     * criteria.
     *
     * @param array $conditions List of conditions.
     *
     * @return integer
     */
    public function countRedirectRules(array $conditions = array())
    {
        $fetchOptions = array();

        $whereClause = $this->prepareRedirectRuleConditions($conditions, $fetchOptions);
        $joinOptions = $this->prepareRedirectRuleFetchOptions($fetchOptions);

        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        return $this->_getDb()->fetchOne(
            '
            SELECT COUNT(*)
            FROM xf_redirect_rule_th AS redirect_rule
            ' . $joinOptions['joinTables'] . '
            WHERE ' . $whereClause . '
        ');
    } /* END countRedirectRules */

    /**
     * Gets all redirect rules titles.
     *
     * @return array [redirect rule id] => title.
     */
    public static function getRedirectRuleTitles()
    {
        $redirectRules = XenForo_Model::create(__CLASS__)->getRedirectRules();
        $titles = array();
        foreach ($redirectRules as $redirectRuleId => $redirectRule) {
            $titles[$redirectRuleId] = $redirectRule['title'];
        }
        return $titles;
    } /* END getRedirectRuleTitles */

    /**
     * Gets the default redirect rule record.
     *
     * @return array
     */
    public function getDefaultRedirectRule()
    {
        return array(
            'redirect_rule_id' => 0,

            'title' => '',

            'user_criteria' => '',
            'userCriteriaList' => array(),

            'page_criteria' => '',
            'pageCriteriaList' => array(),

            'active' => 1,
            'display_order' => 1
        );
    } /* END getDefaultRedirectRule */

    /**
     * Prepares a set of conditions to select redirect rules against.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May
     * be edited if criteria requires.
     *
     * @return string Criteria as SQL for where clause
     */
    public function prepareRedirectRuleConditions(array $conditions, array &$fetchOptions)
    {
        $db = $this->_getDb();
        $sqlConditions = array();

        if (isset($conditions['redirect_rule_ids']) && !empty($conditions['redirect_rule_ids'])) {
            $sqlConditions[] = 'redirect_rule.redirect_rule_id IN (' . $db->quote($conditions['redirect_rule_ids']) . ')';
        } else
            if (isset($conditions['redirect_rule_id'])) {
                $sqlConditions[] = 'redirect_rule.redirect_rule_id = ' . $db->quote($conditions['redirect_rule_id']);
            }

        $this->_prepareRedirectRuleConditions($conditions, $fetchOptions, $sqlConditions);

        return $this->getConditionsForClause($sqlConditions);
    } /* END prepareRedirectRuleConditions */

    /**
     * Method designed to be overridden by child classes to add to set of
     * conditions.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May
     * be edited if criteria requires.
     * @param array $sqlConditions List of conditions as SQL snippets. May be
     * edited if criteria requires.
     */
    protected function _prepareRedirectRuleConditions(array $conditions, array &$fetchOptions, array &$sqlConditions)
    {
    } /* END _prepareRedirectRuleConditions */

    /**
     * Checks the 'join' key of the incoming array for the presence of the
     * FETCH_x bitfields in this class
     * and returns SQL snippets to join the specified tables if required.
     *
     * @param array $fetchOptions containing a 'join' integer key built from
     * this class's FETCH_x bitfields.
     *
     * @return string containing selectFields, joinTables, orderClause keys.
     * Example: selectFields = ', user.*, foo.title'; joinTables = ' INNER JOIN
     * foo ON (foo.id = other.id) '; orderClause = 'ORDER BY x.y'
     */
    public function prepareRedirectRuleFetchOptions(array &$fetchOptions)
    {
        $selectFields = '';
        $joinTables = '';
        $orderBy = '';

        $this->_prepareRedirectRuleFetchOptions($fetchOptions, $selectFields, $joinTables, $orderBy);

        return array(
            'selectFields' => $selectFields,
            'joinTables' => $joinTables,
            'orderClause' => ($orderBy ? "ORDER BY $orderBy" : 'ORDER BY display_order ASC')
        );
    } /* END prepareRedirectRuleFetchOptions */

    /**
     * Method designed to be overridden by child classes to add to SQL snippets.
     *
     * @param array $fetchOptions containing a 'join' integer key built from
     * this class's FETCH_x bitfields.
     * @param string $selectFields = ', user.*, foo.title'
     * @param string $joinTables = ' INNER JOIN foo ON (foo.id = other.id) '
     * @param string $orderBy = 'x.y ASC, x.z DESC'
     */
    protected function _prepareRedirectRuleFetchOptions(array &$fetchOptions, &$selectFields, &$joinTables, &$orderBy)
    {
    } /* END _prepareRedirectRuleFetchOptions */

    public function rebuildRedirectRuleCache()
    {
        $cache = array();

        foreach ($this->getRedirectRules() AS $redirectRuleId => $redirectRule)
        {
            if ($redirectRule['active'])
            {
                $cache[$redirectRuleId] = array(
                    'title' => $redirectRule['title'],
                    'reroute_domain' => $redirectRule['reroute_domain'],
                    'user_criteria' => XenForo_Helper_Criteria::unserializeCriteria($redirectRule['user_criteria']),
                    'page_criteria' => XenForo_Helper_Criteria::unserializeCriteria($redirectRule['page_criteria'])
                );
            }
        }

        XenForo_Application::setSimpleCacheData('th_redirectRules', $cache);

        return $cache;
    } /* END rebuildRedirectRuleCache */
}