<?php
/**
 * Copyright (c) Enalean, 2016. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\SvnCore\ViewVC;

require_once('viewvc_utils.php');
require_once('www/svn/svn_utils.php');

use ForgeConfig;
use HTTPRequest;
use Project;

class EPELViewVCProxy implements ViewVCProxy
{
    private function displayViewVcHeader(HTTPRequest $request)
    {
        $request_uri = $request->getFromServer('REQUEST_URI');

        if (strpos($request_uri, "annotate=") !== false) {
            return true;
        }

        if (strpos($request_uri, "view=patch") !== false ||
            strpos($request_uri, "view=graphimg") !== false ||
            strpos($request_uri, "view=redirect_path") !== false ||
            // ViewVC will redirect URLs with "&rev=" to "&revision=". This is needed by Hudson.
            strpos($request_uri, "&rev=") !== false ) {
            return false;
        }

        if (strpos($request_uri, "/?") === false &&
            strpos($request_uri, "&r1=") === false &&
            strpos($request_uri, "&r2=") === false &&
            (strpos($request_uri, "view=") === false ||
                strpos($request_uri, "view=co") !== false )
        ) {
            return false;
        }

        return true;
    }

    private function buildQueryString(HTTPRequest $request)
    {
        parse_str($request->getFromServer('QUERY_STRING'), $query_string_parts);
        unset($query_string_parts['roottype']);
        return http_build_query($query_string_parts);
    }

    private function escapeStringFromServer(HTTPRequest $request, $key)
    {
        $string = $request->getFromServer($key);

        return escapeshellarg($string);
    }

    private function setLocaleOnFileName($path)
    {
        $current_locales = setlocale(LC_ALL, "0");
        // to allow $path filenames with French characters
        setlocale(LC_CTYPE, "en_US.UTF-8");

        $encoded_path = escapeshellarg($path);
        setlocale(LC_ALL, $current_locales);

        return $encoded_path;
    }

    private function setLocaleOnCommand($command)
    {
        ob_start();
        putenv("LC_CTYPE=en_US.UTF-8");
        passthru($command);

        return ob_get_clean();
    }

    private function getViewVcLocationHeader($location_line)
    {
        // Now look for 'Location:' header line (e.g. generated by 'view=redirect_pathrev'
        // parameter, used when browsing a directory at a certain revision number)
        $location_found = false;

        while ($location_line && !$location_found && strlen($location_line) > 1) {
            $matches = array();

            if (preg_match('/^Location:(.*)$/', $location_line, $matches)) {
                return $matches[1];
            }

            $location_line = strtok("\n\t\r\0\x0B");
        }

        return false;
    }

    public function displayContent(Project $project, HTTPRequest $request)
    {
        $user = $request->getCurrentUser();
        if ($user->isAnonymous()) {
            exit_error(
                $GLOBALS['Language']->getText('svn_viewvc', 'access_denied'),
                $GLOBALS['Language']->getText(
                    'svn_viewvc',
                    'acc_den_comment',
                    session_make_url("/project/memberlist.php?group_id=" . urlencode($project->getID()))
                )
            );
        }

        viewvc_utils_track_browsing($project->getID(), 'svn');

        //this is very important. default path must be /
        $path = "/";

        if ($request->getFromServer('PATH_INFO') != "") {
            $path = $request->getFromServer('PATH_INFO');

            // hack: path must always end with /
            if (strrpos($path, "/") != (strlen($path) - 1)) {
                $path .= "/";
            }
        }

        $command = 'REMOTE_USER=' . escapeshellarg($user->getUserName()) . ' '.
            'PATH_INFO='.$this->setLocaleOnFileName($path).' '.
            'QUERY_STRING='.escapeshellarg($this->buildQueryString($request)).' '.
            'SCRIPT_NAME='.$this->escapeStringFromServer($request, 'SCRIPT_NAME').' '.
            'HTTP_ACCEPT_ENCODING='.$this->escapeStringFromServer($request, 'HTTP_ACCEPT_ENCODING').' '.
            'HTTP_ACCEPT_LANGUAGE='.$this->escapeStringFromServer($request, 'HTTP_ACCEPT_LANGUAGE').' '.
            'TULEAP_REPO_NAME='.escapeshellarg($project->getUnixNameMixedCase()).' '.
            'TULEAP_REPO_PATH='.escapeshellarg(ForgeConfig::get('svn_prefix') . '/' . $project->getUnixName()).' '.
            ForgeConfig::get('tuleap_dir').'/src/common/svn/viewvc/viewvc-epel.cgi 2>&1';

        $content = $this->setLocaleOnCommand($command);

        list($headers, $body) = http_split_header_body($content);

        $content_type_line   = strtok($content, "\n\t\r\0\x0B");

        $content = substr($content, strpos($content, $content_type_line));

        $location_line   = strtok($content, "\n\t\r\0\x0B");
        $viewvc_location = $this->getViewVcLocationHeader($location_line);

        if ($viewvc_location) {
            $GLOBALS['Response']->redirect($viewvc_location);
        }

        $parse = $this->displayViewVcHeader($request);
        if ($parse) {
            //parse the html doc that we get from viewvc.
            //remove the http header part as well as the html header and
            //html body tags
            $begin_body = stripos($content, "<body");
            $begin_doc  = strpos($content, ">", $begin_body) + 1;
            $length     = strpos($content, "</body>\n</html>") - $begin_doc;

            // Now insert references, and display
            svn_header(array(
                'title' => basename($path) . ' - ' . $GLOBALS['Language']->getText('svn_utils', 'browse_tree'),
                'path' => urlencode($path)
            ));
            echo util_make_reference_links(
                substr($content, $begin_doc, $length),
                $project->getID()
            );
            site_footer(array());
        } else {
            echo $body;
            exit();
        }
    }
}
