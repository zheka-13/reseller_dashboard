<?php
header("Content-type: text/csv; charset=utf-8; name=\"domain_statistic.csv\"");
header("Content-Disposition: attachment; filename=\"domain_statistic.csv\"");
header('Expires: Mon, 26 Nov 1962 00:00:00 GMT');
header("Last-Modified: ".gmdate("D,d M Y H:i:s")." GMT");
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

/*
	FusionPBX
	Version: MPL 1.1
	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/
	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.
	The Original Code is FusionPBX
	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2016
	the Initial Developer. All Rights Reserved.
	Contributor(s):
	KonradSC <konrd@yahoo.com>
*/

include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
require_once "resources/check_auth.php";

if (permission_exists('reseller_dashboard_view')) {
    //access granted
}
else {
    echo "access denied";
    exit;
}
$language = new text;
$text = $language->get();
$database = new database;

$domainStatService = new DomainStatService($database);


$domains = $domainStatService->getDomainsStat();
$total = [
    "users_count" => 0,
    "cc_count" => 0,
    "ext_count" => 0,
    "dev_count" => 0,
    "dest_count" => 0,
    "vmails_count" => 0,
];
echo f($text['table-domain']).f($text['table-extensions']).f($text['table-users']).f($text['table-devices']).f($text['table-destinations'])
    .f($text['table-queues']).f($text['table-voicemails'], true)."\n";

foreach ($domains as $domain){
    echo f($domain['domain_name']).f((int)$domain['ext_count']).f((int)$domain['users_count']).f((int)$domain['dev_count']).f((int)$domain['dest_count'])
        .f((int)$domain['cc_count']).f((int)$domain['vmails_count'], true)."\n";

    $total["users_count"] += (int)$domain['users_count'];
    $total["cc_count"] += (int)$domain['cc_count'];
    $total["dev_count"] += (int)$domain['dev_count'];
    $total["dest_count"] += (int)$domain['dest_count'];
    $total["ext_count"] += (int)$domain['ext_count'];
    $total["vmails_count"] += (int)$domain['vmails_count'];
}
echo f($text['table-total']).f((int)$total['ext_count']).f((int)$total['users_count']).f((int)$total['dev_count']).f((int)$total['dest_count'])
    .f((int)$total['cc_count']).f((int)$total['vmails_count'], true)."\n";

function f($f, $no_end_comma = false){
    if ($no_end_comma){
        return '"'.$f.'"';
    }
    return '"'.$f.'",';
}
?>
