<?php
require_once('../../../config.php');
global $CFG, $DB, $PAGE, $USER;
$html = '';
require_login();
$loguser = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
if(empty($loguser)){
    redirect($CFG->wwwroot);
}
$branding = $DB->get_record('custom_branding', array("id"=>$loguser->cbid));
if(empty($branding)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
require_once($CFG->libdir.'/accesslib.php');
$brandingcontext = context_coursecat::instance($branding->brand_category);
$roleid = $DB->get_field_sql("select id from {role} where shortname=?", array("companyadmin"));
if(empty($roleid)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}
if(!user_has_role_assignment($USER->id, $roleid, $brandingcontext->id)){
    redirect($CFG->wwwroot, 'You don\'t have access to this page', null, \core\output\notification::NOTIFY_WARNING);
}


$business_menu = $DB->get_record('business_menu', array("cbid"=>$loguser->cbid));
if(empty($business_menu)){
    $business_menu = new stdClass();
    $business_menu->cbid = $loguser->cbid;
    $business_menu->title = "";
    $data->createdby = $USER->id;
    $data->createddate = time();
    $business_menu->id = $DB->insert_record('business_menu', $business_menu);
    $business_menu = $DB->get_record('business_menu', array("cbid"=>$loguser->cbid));
}
if(isset($_POST["cancel"])){
    redirect($CFG->wwwroot.'/local/business/'); 
}

if(isset($_POST["submit"])) {
    $business_menu->title = $_POST["main_menu"];
    $business_menu->modifiedby = $USER->id;
    $business_menu->modifieddate = time();
    print_r($business_menu); //////////////////
    $DB->update_record('business_menu',$business_menu); 
    // $DB->delete_records('business_menu_items',array("cbid"=>$business_menu->cbid)); 
    echo "<pre>";
    $menuitemdata = $_POST["menuitem"];
    $menuitemdata[0] = array(
        "title"=>"Assigned Learning",
        "link"=>"/calendar/view.php?view=upcoming"
    );
        foreach($menuitemdata as $sortorder=> $row){
            if(!empty($row['title']) && !empty($row['link'])){
                if($oldrec=$DB->get_record("business_menu_items",array("cbid"=>$business_menu->cbid, "menuid"=>$business_menu->id, "sortorder"=>$sortorder))){
                    $oldrec->title=$row['title'];
                    $oldrec->link=$row['link']; 
                    $DB->update_record("business_menu_items",$oldrec); 
                } else {
                    $oldrec = new stdclass();
                    $oldrec->cbid = $business_menu->cbid;
                    $oldrec->menuid = $business_menu->id;
                    $oldrec->sortorder = $sortorder;
                    $oldrec->title=$row['title'];
                    $oldrec->link=$row['link']; 
                    $DB->insert_record("business_menu_items",$oldrec); 
                }
            } else {
                $DB->delete_records('business_menu_items',array("cbid"=>$business_menu->cbid, "menuid"=>$business_menu->id, "sortorder"=>$sortorder)); 
            }
        }
    redirect($CFG->wwwroot.'/local/business/menu_manager/');
}
$menuitemdata = $DB->get_records_sql("SELECT sortorder, title, link FROM {business_menu_items} where cbid=? and menuid=?", array($business_menu->cbid, $business_menu->id));

$html .= '
<div class="company_menumanager" style="padding:20px 40px;">
<a href="'.$CFG->wwwroot.'/local/business/" ><button style="color:#fff;background-color:#666;">Back</button></a>
<form method="POST">
<div class="p-10 my-50">
    <h3>Menu Settings</h3>
    <div>
        <label class="span2" for="main_menu" style="font-weight:bold;">Main Menu : </label> 
        <div class="span10" style="padding:0;margin:0;"><input type="text" id="main_menu" max="15" class="w-100 " name="main_menu" required value="'.$business_menu->title.'" class="p-3" ></div>
        <label class="span2" style="padding:0;margin:0;"></label> 
		<div class="span10" style="padding:0;margin:0;"><span>Recommend keeping this short. Aim for 15 characters or less</span></div>
    </div>
    <div style="clear:both;">
        <div>
        <label  style="font-weight:bold;">Dropdown Menu : </label>
        <p>
            You can customise up to 10 menu items for your private eLearning section in Animal Health Academy. This dropdown menu will appear in the order shown 
            below. Each menu item requires a name and a destination URL (the page link). You may leave menu items blank/unused.
        </p> 
        <div class="row">
            <div class="span2">
                <h6>Menu</h6>
            </div>
            <div class="span10" style="padding:0;margin:0;">
                <h6>Destination URL</h6>
            </div>
        </div>
        
        </div> 
    </div>
';
$html .= ' <div class="row">
    <input type="text" max="15" name="menuitem[0][title]" class="span2 w-100" readonly disabled value="Assigned Learning" />
    <input type="text" name="menuitem[0][link]" class="span10 w-100" readonly disabled value="/calendar/view.php?view=month" />
        </div>';
for ($i=1; $i < $business_menu->submenulimit; $i++) { 
    $html .= ' <div class="row">
        <input type="text" max="15" name="menuitem['.$i.'][title]" class="span2 w-100" value="'.(isset($menuitemdata[$i])?$menuitemdata[$i]->title:'').'" />
        <input type="text" name="menuitem['.$i.'][link]" class="span10 w-100" value="'.(isset($menuitemdata[$i])?$menuitemdata[$i]->link:'').'" />
            </div>';
}
$html .= '<div>
            <button type="submit" name="submit">Submit</button>
            <button type="submit" name="cancel">Cancel</button>
        </div>
</div>
</form>
</div>';

echo $OUTPUT->header();

echo $html;
echo $OUTPUT->footer();


?>