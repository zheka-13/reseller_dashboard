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

$graph_types = [
    'extension', 'user', 'device', 'destination', 'queue', 'voicemail'
];
$periods = [7, 30, 60];

if (!empty($_GET['graph_type']) && !in_array($_GET['graph_type'], $graph_types)){
    unset($_SESSION['graph_type']);
}
if (!empty($_GET['period']) && !in_array($_GET['period'], $periods)){
    unset($_SESSION['period']);
}

if (empty($_SESSION['period'])){
    $_SESSION['period'] = 7;
}
if (empty($_SESSION['graph_type'])){
    $_SESSION['graph_type'] = 'extension';
}

if (!empty($_GET['graph_type']) && in_array($_GET['graph_type'], $graph_types)){
    $_SESSION['graph_type'] = $_GET['graph_type'];
}
if (!empty($_GET['period']) && in_array($_GET['period'], $periods)){
    $_SESSION['period'] = $_GET['period'];
}


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
foreach ($domains as $domain){
    $total["users_count"] += (int)$domain['users_count'];
    $total["cc_count"] += (int)$domain['cc_count'];
    $total["dev_count"] += (int)$domain['dev_count'];
    $total["dest_count"] += (int)$domain['dest_count'];
    $total["ext_count"] += (int)$domain['ext_count'];
    $total["vmails_count"] += (int)$domain['vmails_count'];
}

require_once "resources/header.php";
?>
    <style>
        .main_table{
            width:90%;
            font-family: Monaco, monospace;
        }
        .portlet_header{
            line-height:70px;
            height:70px;
            color:white;
            width:100%;
            background-color:#2c3858;
            font-family: Monaco, monospace;
            border-radius: 10px 10px 0px 0px;
        }
        .portlet_table_cell{
            border-bottom: 1px solid lightgrey;
            text-transform: uppercase;
            padding: 10px;
            color: gray;
            font-family: Monaco, monospace;
        }
        button.btn-reseller {
            height: 28px;
            padding: 5px 5px;
            border: 1px solid #242424;
            -moz-border-radius: 3px 3px 3px 3px;
            -webkit-border-radius: 3px 3px 3px 3px;
            -khtml-border-radius: 3px 3px 3px 3px;
            border-radius: 3px 3px 3px 3px;
            background: white;
            font-family: Candara, Calibri, Segoe, "Segoe UI", Optima, Arial, sans-serif;
            text-align: center;
            text-transform: uppercase;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            vertical-align: middle;
            white-space: nowrap;
        }
        button.btn-reseller_active {
            height: 28px;
            padding: 5px 5px;
            border: 1px solid #242424;
            -moz-border-radius: 3px 3px 3px 3px;
            -webkit-border-radius: 3px 3px 3px 3px;
            -khtml-border-radius: 3px 3px 3px 3px;
            border-radius: 3px 3px 3px 3px;
            background: lightgrey;
            font-family: Candara, Calibri, Segoe, "Segoe UI", Optima, Arial, sans-serif;
            text-align: center;
            text-transform: uppercase;
            color: #000000;
            font-weight: bold;
            font-size: 11px;
            vertical-align: middle;
            white-space: nowrap;
        }
    </style>
<?php
$document['title'] = $text['title-reseller_dashboard'];

echo "<script src='/resources/chartjs/chart.min.js'></script>";

echo "<h3>".$text['title-reseller_dashboard']."</h3>";
echo "<table class='main_table'>";
echo "<tr>";
echo "<td style='width:49%; padding:10px;vertical-align: top'>";
echo "<div class='portlet_header'><span style='margin-left:20px'>".$text['title-domain_statistics']."</span>";
echo "<span style='float:right;padding-right: 10px'>";
echo button::create([
        'type'=>'button',
        'label'=>$text['button-export'],
        'icon'=>$_SESSION['theme']['button_icon_download'],
        'id'=>'btn_export',
        'link'=>'export.php',
        'class' => 'reseller']);
echo "</span>";
echo "</div>";
echo  "<table border='0' cellpadding='0' cellspacing='0' style='width:100%;'>";
echo "<tr>";
echo "<td class='portlet_table_cell'>".$text['table-domain']."</td>";
echo "<td class='portlet_table_cell'>".$text['table-extensions']."</td>";
echo "<td class='portlet_table_cell'>".$text['table-users']."</td>";
echo "<td class='portlet_table_cell'>".$text['table-devices']."</td>";
echo "<td class='portlet_table_cell'>".$text['table-destinations']."</td>";
echo "<td class='portlet_table_cell'>".$text['table-queues']."</td>";
echo "<td class='portlet_table_cell'>".$text['table-voicemails']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='portlet_table_cell'>".$text['table-total']."</td>";
echo "<td class='portlet_table_cell'>".$total['ext_count']."</td>";
echo "<td class='portlet_table_cell'>".$total['users_count']."</td>";
echo "<td class='portlet_table_cell'>".$total['dev_count']."</td>";
echo "<td class='portlet_table_cell'>".$total['dest_count']."</td>";
echo "<td class='portlet_table_cell'>".$total['cc_count']."</td>";
echo "<td class='portlet_table_cell'>".$total['vmails_count']."</td>";
echo "</tr>";
foreach ($domains as $domain){
    echo "<tr>";
    echo "<td class='portlet_table_cell'>".$domain['domain_name']."</td>";
    echo "<td class='portlet_table_cell'>".(!empty($domain['ext_count']) ? "<strong>".$domain['ext_count']."</strong>" : 0)."</td>";
    echo "<td class='portlet_table_cell'>".(!empty($domain['users_count']) ? "<strong>".$domain['users_count']."</strong>" : 0)."</td>";
    echo "<td class='portlet_table_cell'>".(!empty($domain['dev_count']) ? "<strong>".$domain['dev_count']."</strong>" : 0)."</td>";
    echo "<td class='portlet_table_cell'>".(!empty($domain['dest_count']) ? "<strong>".$domain['dest_count']."</strong>" : 0)."</td>";
    echo "<td class='portlet_table_cell'>".(!empty($domain['cc_count']) ? "<strong>".$domain['cc_count']."</strong>" : 0)."</td>";
    echo "<td class='portlet_table_cell'>".(!empty($domain['vmails_count']) ? "<strong>".$domain['vmails_count']."</strong>" : 0)."</td>";
    echo "</tr>";

}

echo "</table>";
echo  "</td>";
echo "<td style='width:49%; padding:10px;vertical-align: top'>";
echo "<div class='portlet_header'><span style='margin-left:20px'>".$text['title-graph']."</span></div>";
echo "</td><td>";
echo "<span>";
foreach  ($periods as $period){
    echo button::create([
        'type' => 'button',
        'label' => $text['button-'.$period.'days'],
        'class' => ($_SESSION['period'] == $period ? "reseller_active" : "reseller"),
        'id' => 'btn_'.$period.'days','link'=>'dashboard.php?period='.$period
    ]);
}
echo "</span>";
echo "</td></tr></table>";



/*
echo "<div class='action_bar' id='action_bar'>";
echo "<div class='heading'><b>".."</b></div>";
echo "<div class='actions'>";
echo button::create(['type'=>'button','label'=>$text['button-export'],'icon'=>$_SESSION['theme']['button_icon_download'],'id'=>'btn_export','link'=>'export.php']);
echo "</div>";
echo "<div style='clear: both;'></div>";
echo "</div>";

echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "<tr>\n";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-domain']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-extensions']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-users']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-devices']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-destinations']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-queues']."</th>";
echo "<th nowrap='nowrap'><a href='#'>".$text['table-voicemails']."</th>";
echo "</tr>\n";
foreach ($domains as $domain){
    echo "<tr class='list-row'>";
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
echo "<tr class='list-row'>";
echo "<td><b>".$text['table-total']."</b></td>";
echo "<td><b>".$total['users_count']."</b></td>";
echo "<td><b>".$total['cc_count']."</b></td>";
echo "<td><b>".$total['cr_count']."</b></td>";
echo "<td><b>".$total['gates_count']."</b></td>";
echo "<td><b>".$total['rooms_count']."</b></td>";
echo "<td><b>".$total['vmails_count']."</b></td>";
echo "</tr>";
echo "</table>";

echo "<br><hr><br>";

echo "<div class='action_bar' id='action_bar'>";
echo "<div class='heading'><b>".."</b></div>";
echo "<div class='actions'>";

echo "<form id='form_graph_type' class='inline' method='get'>";
echo "<select class='formfld' name='graph_type' id='graph_type' style='width: auto; margin-left: 15px;' 
        onchange='this.form.submit()'>";
    foreach ($graph_types as $type){
        echo "<option ".($_SESSION['graph_type'] == $type ? "selected" : "")." value='".$type."'>".$text['graph-'.$type]."</option>";
    }
echo "</select></form>";
echo "</div>";
echo "<div style='clear: both;'></div>";
echo "</div>";
*/
require_once "resources/footer.php";
