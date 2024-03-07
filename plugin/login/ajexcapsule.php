<?php
require_once('../config.php');
global $DB;
$match_company = $DB->get_record_sql("select * from mdl_company_list where CONCAT(name, ' - ' ,address)like ?", array(htmlspecialchars_decode(urldecode($_GET['company']))));

$custom_branding_company = $DB->get_record_sql("select cb.* from mdl_company_list as cl inner join {custom_branding} cb on FIND_IN_SET(cl.id,cb.company_id) where CONCAT(cl.name, ' - ' ,cl.address)like ? and cb.consent_text != ''", array(urldecode($_GET['company'])));

if(!empty($match_company)){
	$match_company->company_group=$custom_branding_company;
    echo json_encode($match_company);
} else {
	$fakecompany = new stdClass();
	$fakecompany->agency=0;
	$fakecompany->company_group=$custom_branding_company;
	$fakecompany->passcode="";
    echo json_encode($fakecompany);
}
