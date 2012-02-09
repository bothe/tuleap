<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
 * 
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once 'Visitor.class.php';

/**
 * Can visit a FormElement and provides the corresponding administration element 
 */
class Tracker_FormElement_View_Admin_UpdateVisitor implements Tracker_FormElement_View_Admin_Visitor {

    /**
     * Return html corresponding to FormElement update
     * 
     * @return String
     */
    public function fetchForm() {
        if ($this->element->isModifiable()) {
            return $this->fetchAdminForUpdate();
        } else {
            return $this->fetchAdminForShared();
        }
    }
    

    public function fetchAdminForUpdate() {
        $html = '';
        $html .= $this->adminElement->fetchTypeForUpdate();
        $html .= $this->adminElement->fetchNameForUpdate();
        $html .= $this->adminElement->fetchLabelForUpdate();
        $html .= $this->adminElement->fetchDescriptionForUpdate();
        $html .= $this->adminElement->fetchRanking();
        $html .= $this->adminElement->fetchAdminSpecificProperties();
        $html .= $this->adminElement->fetchAfterAdminEditForm();
        $html .= $this->adminElement->fetchAdminButton(self::SUBMIT_UPDATE);
        $html .= $this->adminElement->fetchAdminFormPermissionLink();
        return $html;
    }

    public function fetchAdminForShared() {
        $html = '';
        $html .= $this->adminElement->fetchTypeNotModifiable();
        $html .= $this->adminElement->fetchCustomHelpForShared();
        $html .= $this->adminElement->fetchNameForShared();
        $html .= $this->adminElement->fetchLabelForShared();
        $html .= $this->adminElement->fetchDescriptionForShared();
        $html .= $this->adminElement->fetchRanking();
        $html .= $this->adminElement->fetchAdminSpecificProperties();
        $html .= $this->adminElement->fetchAfterAdminEditForm();
        $html .= $this->adminElement->fetchAdminButton(self::SUBMIT_UPDATE);
        $html .= $this->adminElement->fetchAdminFormPermissionLink();
        return $html;
    }

    /**
     * Display the form to administrate the element
     * 
     * @param TrackerManager  $tracker_manager The tracker manager
     * @param HTTPRequest     $request         The data coming from the user
     * 
     * @return void
     */
    public function display(TrackerManager $tracker_manager, HTTPRequest $request) {
        $label            = $this->element->getLabel();
        $title            = $GLOBALS['Language']->getText('plugin_tracker_include_type', 'upd_label', $label);
        $url              = $this->element->getAdminEditUrl();
        $breadcrumbsLabel = $label;
        echo $this->displayForm($tracker_manager, $request, $breadcrumbsLabel, $url, $title, $this->fetchForm());
    }
}

?>
