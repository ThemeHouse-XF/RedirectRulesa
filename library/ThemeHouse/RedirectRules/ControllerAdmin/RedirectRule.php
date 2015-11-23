<?php

/**
 * Admin controller for handling actions on redirect rules.
 */
class ThemeHouse_RedirectRules_ControllerAdmin_RedirectRule extends XenForo_ControllerAdmin_Abstract
{

    /**
     * Shows a list of redirect rules.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionIndex()
    {
        $redirectRuleModel = $this->_getRedirectRuleModel();
        $redirectRules = $redirectRuleModel->getRedirectRules();
        $viewParams = array(
            'redirectRules' => $redirectRules
        );
        return $this->responseView('ThemeHouse_RedirectRules_ViewAdmin_RedirectRule_List',
            'th_redirect_rule_list_redirectrules', $viewParams);
    } /* END actionIndex */

    /**
     * Helper to get the redirect rule add/edit form controller response.
     *
     * @param array $redirectRule
     *
     * @return XenForo_ControllerResponse_View
     */
    protected function _getRedirectRuleAddEditResponse(array $redirectRule)
    {
        $viewParams = array(
            'redirectRule' => $redirectRule,

            'userCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection($redirectRule['user_criteria']),
            'userCriteriaData' => XenForo_Helper_Criteria::getDataForUserCriteriaSelection(),

            'pageCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection($redirectRule['page_criteria']),
            'pageCriteriaData' => XenForo_Helper_Criteria::getDataForPageCriteriaSelection(),

            'showInactiveCriteria' => true
        );

        return $this->responseView('ThemeHouse_RedirectRules_ViewAdmin_RedirectRule_Edit',
            'th_redirect_rule_edit_redirectrules', $viewParams);
    } /* END _getRedirectRuleAddEditResponse */

    /**
     * Displays a form to add a new redirect rule.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionAdd()
    {
        $redirectRule = $this->_getRedirectRuleModel()->getDefaultRedirectRule();

        return $this->_getRedirectRuleAddEditResponse($redirectRule);
    } /* END actionAdd */

    /**
     * Displays a form to edit an existing redirect rule.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionEdit()
    {
        $redirectRuleId = $this->_input->filterSingle('redirect_rule_id', XenForo_Input::STRING);

        if (!$redirectRuleId) {
            return $this->responseReroute('ThemeHouse_RedirectRules_ControllerAdmin_RedirectRule', 'add');
        }

        $redirectRule = $this->_getRedirectRuleOrError($redirectRuleId);

        return $this->_getRedirectRuleAddEditResponse($redirectRule);
    } /* END actionEdit */

    /**
     * Inserts a new redirect rule or updates an existing one.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionSave()
    {
        $this->_assertPostOnly();

        $redirectRuleId = $this->_input->filterSingle('redirect_rule_id', XenForo_Input::STRING);

        $input = $this->_input->filter(
            array(
                'title' => XenForo_Input::STRING,
                'reroute_domain' => XenForo_Input::STRING,
                'active' => XenForo_Input::UINT,
                'display_order' => XenForo_Input::UINT,
                'user_criteria' => XenForo_Input::ARRAY_SIMPLE,
                'page_criteria' => XenForo_Input::ARRAY_SIMPLE
            ));

        $writer = XenForo_DataWriter::create('ThemeHouse_RedirectRules_DataWriter_RedirectRule');
        if ($redirectRuleId) {
            $writer->setExistingData($redirectRuleId);
        }
        $writer->bulkSet($input);
        $writer->save();

        if ($this->_input->filterSingle('reload', XenForo_Input::STRING)) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
                XenForo_Link::buildAdminLink('redirect-rules/edit', $writer->getMergedData()));
        } else {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildAdminLink('redirect-rules') . $this->getLastHash($writer->get('redirect_rule_id')));
        }
    } /* END actionSave */

    /**
     * Deletes a redirect rule.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionDelete()
    {
        $redirectRuleId = $this->_input->filterSingle('redirect_rule_id', XenForo_Input::STRING);
        $redirectRule = $this->_getRedirectRuleOrError($redirectRuleId);

        $writer = XenForo_DataWriter::create('ThemeHouse_RedirectRules_DataWriter_RedirectRule');
        $writer->setExistingData($redirectRule);

        if ($this->isConfirmedPost()) { // delete redirect rule
            $writer->delete();

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildAdminLink('redirect-rules'));
        } else { // show delete confirmation prompt
            $writer->preDelete();
            $errors = $writer->getErrors();
            if ($errors) {
                return $this->responseError($errors);
            }

            $viewParams = array(
                'redirectRule' => $redirectRule
            );

            return $this->responseView('ThemeHouse_RedirectRules_ViewAdmin_RedirectRule_Delete',
                'th_redirect_rule_delete_redirectrules', $viewParams);
        }
    } /* END actionDelete */

    /**
     * Gets a valid redirect rule or throws an exception.
     *
     * @param string $redirectRuleId
     *
     * @return array
     */
    protected function _getRedirectRuleOrError($redirectRuleId)
    {
        $redirectRule = $this->_getRedirectRuleModel()->getRedirectRuleById($redirectRuleId);
        if (!$redirectRule) {
            throw $this->responseException(
                $this->responseError(new XenForo_Phrase('th_requested_redirect_rule_not_found_redirectrules'),
                    404));
        }

        return $redirectRule;
    } /* END _getRedirectRuleOrError */

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