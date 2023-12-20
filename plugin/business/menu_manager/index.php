<?php
require_once('../../../config.php');
global $CFG, $DB, $PAGE, $USER;
$html = '';
require_login();
$loguser = $DB->get_record("custom_branding_users", array("userid"=>$USER->id,"status"=>1,"isadmin"=>1));
if(empty($loguser)){
    redirect($CFG->wwwroot);
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
<div class="company_menumanager">
<a href="'.$CFG->wwwroot.'/local/business/"><button>Back</button></a>
<form method="POST">
<div class="p-10 my-50">
    <h3>Menu Settings</h3>
    <div>
        <label class="span2" for="main_menu" >Main Menu : </label> 
        <input type="text" id="main_menu" max="15" class="w-50 span10" name="main_menu" required value="'.$business_menu->title.'" class="p-3" >
        <div><span>Recommend keeping this short. Aim for 15 characters or less</span></div>
    </div>
    <div>
        <div>
        <h5>
            Dropdown Menu : 
        </h5>
        <p>
            You can customise up to 10 menu items for your private eLearning section in Animal Health Academy. This dropdown menu will appear in the order shown 
            below. Each menu item requires a name and a destination URL (the page link). You may leave menu items blank/unused.
        </p> 
        <div class="row">
            <div class="span2">
                <h6>Menu</h6>
            </div>
            <div class="span10">
                <h6>Destination URL</h6>
            </div>
        </div>
        
        </div> 
    </div>
';
for ($i=0; $i < $business_menu->submenulimit; $i++) { 
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