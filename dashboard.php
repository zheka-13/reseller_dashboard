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
echo "<td style='width:98%; padding:10px;vertical-align: top'>";
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
echo  "</tr>";
echo "</table>";

echo "<table class='main_table'>";
echo "<tr>";
echo "<td style='width:98%; padding:10px;vertical-align: top'>";
echo "<div class='portlet_header' style='margin-bottom: 10px'><span style='margin-left:20px'>".$text['title-graph']."</span></div>";

echo "<div ><canvas height='100px' id='HourChart'></canvas></div>";
echo "<div ><canvas height='100px' id='DayChart'></canvas></div>";
echo "</td>";

echo "</tr></table>";
//------------------- hourly data

$query = "select date_trunc('hour', dtime)::timestamp(0) as dtime from (select * from generate_series(now()-'1 day'::interval, now(), '1 hour') as dtime) d
order by dtime asc";
$data = $database->select($query);
$hours = [];
foreach ($data as $row){
    $hours[] = $row['dtime'];
}

$query = "SELECT direction, count(*) as cnt, date_trunc('hour', start_stamp)::timestamp(0) as dtime  FROM public.v_xml_cdr
    where direction in ('inbound', 'outbound') and start_stamp > now()-'1 day'::interval
    group by direction,  date_trunc('hour', start_stamp)
    ORDER BY dtime ASC";

$data = $database->select($query);
$call_data = [];
foreach ($data as $row){
    if (!isset($call_data[$row['dtime']])){
        $call_data[$row['dtime']] = [];
    }
    if (!isset($call_data[$row['dtime']][$row['direction']])){
        $call_data[$row['dtime']][$row['direction']] = $row['cnt'];
    }
}
$hour_inbound = [];
$hour_outbound = [];
foreach ($hours as $hour){
    if (isset($call_data[$hour]['inbound'])){
        $hour_inbound[] = $call_data[$hour]['inbound'];
    }
    else{
        $hour_inbound[] = 0;
    }
    if (isset($call_data[$hour]['outbound'])){
        $hour_outbound[] = $call_data[$hour]['outbound'];
    }
    else{
        $hour_outbound[] = 0;
    }
}
//-----------------------------------------------------
//----------------------dayly calls
$query = "select dtime::date as dtime from (select * from generate_series(now()-'10 days'::interval, now(), '1 day') as dtime) d
order by dtime asc";
$data = $database->select($query);
$days = [];
foreach ($data as $row){
    $days[] = $row['dtime'];
}

$query = "SELECT direction, count(*) as cnt, start_stamp::date as dtime  FROM public.v_xml_cdr
    where direction in ('inbound', 'outbound') and start_stamp > now()-'10 days'::interval
    group by direction,  start_stamp::date
    ORDER BY dtime ASC";

$data = $database->select($query);
$call_data = [];
foreach ($data as $row){
    if (!isset($call_data[$row['dtime']])){
        $call_data[$row['dtime']] = [];
    }
    if (!isset($call_data[$row['dtime']][$row['direction']])){
        $call_data[$row['dtime']][$row['direction']] = $row['cnt'];
    }
}
$day_inbound = [];
$day_outbound = [];
foreach ($days as $day){
    if (isset($call_data[$day]['inbound'])){
        $day_inbound[] = $call_data[$day]['inbound'];
    }
    else{
        $day_inbound[] = 0;
    }
    if (isset($call_data[$day]['outbound'])){
        $day_outbound[] = $call_data[$day]['outbound'];
    }
    else{
        $day_outbound[] = 0;
    }
}

require_once "resources/footer.php";
?>
<script language='JavaScript' type='text/javascript' src='/resources/chartjs/chart.min.js'></script>
<script language='JavaScript' type='text/javascript' src='/resources/chartjs/chartjs-adapter-date-fns.bundle.min.js'></script>

<script>
    const hour_labels = <?php echo json_encode($hours); ?>;
    const hour_data = {
        labels: hour_labels,
        datasets: [
            {
                label: 'Inbound calls',
                data: <?php echo json_encode($hour_inbound); ?>,
                backgroundColor: 'rgb(255, 99, 132)',
            },
            {
                label: 'Outbound calls',
                data: <?php echo json_encode($hour_outbound); ?>,
                backgroundColor: 'rgb(54, 162, 235)',
            },

        ]
    };

    const hour_config = {
        type: 'bar',
        data: hour_data,
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Calls per hour (last 24 hours)'
                }
            }
        }
    };
    const hourChart = new Chart(
        document.getElementById('HourChart'),
        hour_config
    );

    const day_labels = <?php echo json_encode($days); ?>;
    const day_data = {
        labels: day_labels,
        datasets: [
            {
                label: 'Inbound calls',
                data: <?php echo json_encode($day_inbound); ?>,
                backgroundColor: 'rgb(255, 99, 132)',
            },
            {
                label: 'Outbound calls',
                data: <?php echo json_encode($day_outbound); ?>,
                backgroundColor: 'rgb(54, 162, 235)',
            },

        ]
    };

    const day_config = {
        type: 'bar',
        data: day_data,
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Calls per day (last 10 days)'
                }
            }
        }
    };
    const dayChart = new Chart(
        document.getElementById('DayChart'),
        day_config
    );
</script>
