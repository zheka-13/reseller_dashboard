<?php
//application details
$apps[$x]['name'] = "Reseller Dashboard";
$apps[$x]['uuid'] = "ad1fb513-4910-4329-addf-763efdf11961";
$apps[$x]['category'] = "";
$apps[$x]['subcategory'] = "";
$apps[$x]['version'] = "";
$apps[$x]['license'] = "Mozilla Public License 1.1";
$apps[$x]['url'] = "http://www.fusionpbx.com";
$apps[$x]['description']['en-us'] = "";


//permission details
$y = 0;
$apps[$x]['permissions'][$y]['name'] = "reseller_dashboard_view";
$apps[$x]['permissions'][$y]['menu']['uuid'] = "7f7e8782-fb1e-4d1c-aaba-8c2a32a8001a";
$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
$apps[$x]['permissions'][$y]['groups'][] = "admin";
