<?php
defined('MOODLE_INTERNAL') || die();                                                                          
$THEME->name = 'customboost';                                                                                                             
$THEME->sheets = [];                                                                                                             
$THEME->editor_sheets = [];                                                                                                         
$THEME->parents = ['boost'];                                                                                                        
$THEME->enable_dock = false;                                                                                                        
$THEME->yuicssmodules = array();                                                                                                    
$THEME->rendererfactory = 'theme_overridden_renderer_factory';                                                                      
$THEME->requiredblocks = '';   
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->haseditswitch = true;

$THEME->layouts = [
    // Most backwards compatible layout without the blocks.
    'base' => array(
        'file' => 'drawers.php',
        'regions' => array(),
    ),
    // Standard layout with blocks.
    'standard' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Main course page.
    'course' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
    'coursecategory' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // The site home page.
    'frontpage' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true),
    ),
    // Server administration scripts.
    'admin' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // My courses page.
    'mycourses' => array(
        // 'file' => 'drawers.php',
        'file' => 'cccourse.php',
        // 'file' => 'columns1.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true),
    ),
    // My dashboard page.
    'mydashboard' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true, 'langmenu' => true),
    ),
    // My public page.
    'mypublic' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    'login' => array(
        'file' => 'login.php',
        'regions' => array(),
        'options' => array('langmenu' => true),
    )
];

// $THEME->prescsscallback = 'theme_customboost_get_pre_scss';
// $THEME->extrascsscallback = 'theme_customboost_get_extra_scss';
$THEME->scss = function($theme) {                                                                                                   
    return theme_customboost_get_main_scss_content($theme);                                                                               
};