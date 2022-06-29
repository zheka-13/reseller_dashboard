<?php
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
    "cr_count" => 0,
    "gates_count" => 0,
    "rooms_count" => 0,
    "vmails_count" => 0,
];
require_once "resources/header.php";
$document['title'] = $text['title-reseller_dashboard'];

echo "<h3>".$text['title-reseller_dashboard']."</h3>";

echo "<div class='action_bar' id='action_bar'>";
echo "<div class='heading'><b>".$text['title-domain_statistics']."</b></div>";
echo "<div class='actions'>";
echo button::create(['type'=>'button','label'=>$text['button-export'],'icon'=>$_SESSION['theme']['button_icon_download'],'id'=>'btn_export','link'=>'export.php']);
echo "</div>";
echo "<div style='clear: both;'></div>";
echo "</div>";

echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "<tr>\n";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-domain']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-pbx_users']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-call_centers']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-call_recordings']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-sip_trunks']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-meeting_rooms']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-voicemail_trancriptions']."</th>";
echo "</tr>\n";
foreach ($domains as $domain){
    echo "<tr>";
    echo "<td>".$domain['domain_name']."</td>";
    echo "<td>".(int)$domain['users_count']."</td>";
    echo "<td>".(int)$domain['cc_count']."</td>";
    echo "<td>".(int)$domain['cr_count']."</td>";
    echo "<td>".(int)$domain['gates_count']."</td>";
    echo "<td>".(int)$domain['rooms_count']."</td>";
    echo "<td>".(int)$domain['vmails_count']."</td>";
    echo "</tr>";
    $total["users_count"] += (int)$domain['users_count'];
    $total["cc_count"] += (int)$domain['cc_count'];
    $total["cr_count"] += (int)$domain['cr_count'];
    $total["gates_count"] += (int)$domain['gates_count'];
    $total["rooms_count"] += (int)$domain['rooms_count'];
    $total["vmails_count"] += (int)$domain['vmails_count'];
}
echo "<tr>";
echo "<td>".$text['table-total']."</td>";
echo "<td>".$total['users_count']."</td>";
echo "<td>".$total['cc_count']."</td>";
echo "<td>".$total['cr_count']."</td>";
echo "<td>".$total['gates_count']."</td>";
echo "<td>".$total['rooms_count']."</td>";
echo "<td>".$total['vmails_count']."</td>";
echo "</tr>";
echo "</table>";
require_once "resources/footer.php";
