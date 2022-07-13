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
    "ext_count" => 0,
    "dev_count" => 0,
    "dest_count" => 0,
    "vmails_count" => 0,
];


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
echo "<script src='/resources/chartjs/chartjs-adapter-date-fns.bundle.min.js'></script>";

echo "<h3>".$text['title-reseller_dashboard']."</h3>";

echo "<div class='heading'><b>".$text['title-graph-hours']."</b></div>";
echo "<div ><canvas height='100px' id='HourChart'></canvas></div>";
echo "<br><hr><br>";
echo "<div class='heading'><b>".$text['title-graph-days']."</b></div>";
echo "<div ><canvas height='100px' id='DayChart'></canvas></div>";

//------------------- hourly data

$hourly_data = $domainStatService->getHourlyChartData();
//-----------------------------------------------------
//----------------------daily calls
$daily_data = $domainStatService->getDailyChartData();

echo "<br><hr><br>";
echo "<div class='action_bar' id='action_bar'>";
echo "<div class='heading'><b>".$text['title-domain_statistics']."</b></div>";
echo "<div class='actions'>";
echo button::create(
    ['type'=>'button','label'=>$text['button-export'],'icon'=>$_SESSION['theme']['button_icon_download'],
        'id'=>'btn_export','link'=>'export.php']);
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
    echo "<td>".(int)$domain['ext_count']."</td>";
    echo "<td>".(int)$domain['users_count']."</td>";
    echo "<td>".(int)$domain['dev_count']."</td>";
    echo "<td>".(int)$domain['dest_count']."</td>";
    echo "<td>".(int)$domain['cc_count']."</td>";
    echo "<td>".(int)$domain['vmails_count']."</td>";
    echo "</tr>";
    $total["users_count"] += (int)$domain['users_count'];
    $total["cc_count"] += (int)$domain['cc_count'];
    $total["dev_count"] += (int)$domain['dev_count'];
    $total["dest_count"] += (int)$domain['dest_count'];
    $total["ext_count"] += (int)$domain['ext_count'];
    $total["vmails_count"] += (int)$domain['vmails_count'];
}
echo "<tr class='list-row'>";
echo "<td><b>".$text['table-total']."</b></td>";
echo "<td><b>".$total['ext_count']."</b></td>";
echo "<td><b>".$total['users_count']."</b></td>";
echo "<td><b>".$total['dev_count']."</b></td>";
echo "<td><b>".$total['dest_count']."</b></td>";
echo "<td><b>".$total['cc_count']."</b></td>";
echo "<td><b>".$total['vmails_count']."</b></td>";
echo "</tr>";
echo "</table>";

require_once "resources/footer.php";
?>

<script>
    const hour_labels = <?php echo json_encode($hourly_data['labels']); ?>;
    const hour_data = {
        labels: hour_labels,
        datasets: [
            {
                label: '<?php echo $text['type-graph-inbound'];?>',
                data: <?php echo json_encode($hourly_data['inbound']); ?>,
                backgroundColor: 'rgb(255, 99, 132)',
            },
            {
                label: '<?php echo $text['type-graph-outbound'];?>',
                data: <?php echo json_encode($hourly_data['outbound']); ?>,
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
                    text: '<?php echo $text['title-graph-hours']; ?>'
                }
            }
        }
    };
    const hourChart = new Chart(
        document.getElementById('HourChart'),
        hour_config
    );

    const day_labels = <?php echo json_encode($daily_data['labels']); ?>;
    const day_data = {
        labels: day_labels,
        datasets: [
            {
                label: '<?php echo $text['type-graph-inbound'];?>',
                data: <?php echo json_encode($daily_data['inbound']); ?>,
                backgroundColor: 'rgb(255, 99, 132)',
            },
            {
                label: '<?php echo $text['type-graph-outbound'];?>',
                data: <?php echo json_encode($daily_data['outbound']); ?>,
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
                    text: '<?php echo $text['title-graph-days'];?>'
                }
            }
        }
    };
    const dayChart = new Chart(
        document.getElementById('DayChart'),
        day_config
    );
</script>
