<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings used by the tiles course format
 *
 * @package format_supertiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/format/supertiles/lib.php');

if ($ADMIN->fulltree) {
    $settings = null; // We add our own settings pages and do not want the standard settings link.

    $settingscategory = new \format_supertiles\admin_settingspage_tabs('formatsettingtiles', get_string('pluginname', 'format_supertiles'));


    // Colour settings.
    $page = new admin_settingpage('format_supertiles/tab-colours', get_string('colours', 'format_supertiles'));

    $page->add(
        new admin_setting_heading('followthemecolour', get_string('followthemecolour', 'format_supertiles'),
            get_string('followthemecolour_desc', 'format_supertiles'))
    );

    $name = 'format_supertiles/followthemecolour';
    $title = get_string('followthemecolour', 'format_supertiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, '', $default));

    $brandcolourdefaults = array(
        '#1670CC' => get_string('colourblue', 'format_supertiles'),
        '#00A9CE' => get_string('colourlightblue', 'format_supertiles'),
        '#7A9A01' => get_string('colourgreen', 'format_supertiles'),
        '#009681' => get_string('colourdarkgreen', 'format_supertiles'),
        '#D13C3C' => get_string('colourred', 'format_supertiles'),
        '#772583' => get_string('colourpurple', 'format_supertiles'),
    );
    $colournumber = 1;
    foreach ($brandcolourdefaults as $hex => $displayname) {
        $title = get_string('brandcolour', 'format_supertiles') . ' ' . $colournumber;
        if ($colournumber === 1) {
            $title .= " - " . get_string('defaulttilecolour', 'format_supertiles');
        }
        $page->add(
            new admin_setting_heading(
                'brand' . $colournumber,
                $title,
                ''
            )
        );
        // Colour picker for this brand.

        if ($colournumber === 1) {
            $visiblename = get_string('defaulttilecolour', 'format_supertiles');
        } else {
            $visiblename = get_string('tilecolourgeneral', 'format_supertiles') . ' ' . $colournumber;
        }
        $setting = new admin_setting_configcolourpicker(
            'format_supertiles/tilecolour' . $colournumber,
            $visiblename,
            '',
            $hex
        );
        $page->add($setting);

        // Display name for this brand.
        $setting = new admin_setting_configtext(
            'format_supertiles/colourname' . $colournumber,
            get_string('colournamegeneral', 'format_supertiles') . ' ' . $colournumber,
            get_string('colourname_descr', 'format_supertiles'),
            $displayname,
            PARAM_RAW,
            30
        );
        $page->add($setting);
        $colournumber++;
    }

    $page->add(new admin_setting_heading('hovercolourheading', get_string('hovercolour', 'format_supertiles'), ''));
    // Hover colour for all tiles (in hexadecimal RGB with preceding '#').
    $name = 'format_supertiles/hovercolour';
    $title = get_string('hovercolour', 'format_supertiles');
    $description = get_string('hovercolour_descr', 'format_supertiles');
    $default = '#ED8B00';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);
    $settingscategory->add($page);

    // Modal activities / resources.
    $page = new admin_settingpage('format_supertiles/tab-modalwindows', get_string('modalwindows', 'format_supertiles'));

    // Modal windows for course modules.
    $allowedmodtypes = ['page' => 1]; // Number is default to on or off.
    $allmodtypes = get_module_types_names();
    $options = [];
    foreach (array_keys($allowedmodtypes) as $modtype) {
        if (isset($allmodtypes[$modtype])) {
            $options[$modtype] = $allmodtypes[$modtype];
        }
    }
    $name = 'format_supertiles/modalmodules';
    $title = get_string('modalmodules', 'format_supertiles');
    $description = get_string('modalmodules_desc', 'format_supertiles');
    $setting = new admin_setting_configmulticheckbox(
        $name,
        $title,
        $description,
        $allowedmodtypes,
        $options
    );
    $page->add($setting);

    // Modal windows for resources.
    $displayembed = get_string('display', 'form') . ': ' . get_string('resourcedisplayembed');
    $link = html_writer::link(
        "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options",
        "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options"
    );
    $allowedresourcetypes = array(
        'pdf' => get_string('displaytitle_mod_pdf', 'format_supertiles') . " (pdf)",
        'url' => get_string('url') . ' (' . $displayembed . ')',
        'html' => get_string('displaytitle_mod_html', 'format_supertiles') . " (HTML " . get_string('file') . ")"
    );
    $name = 'format_supertiles/modalresources';
    $title = get_string('modalresources', 'format_supertiles');
    $description = get_string('modalresources_desc', 'format_supertiles', array('displayembed' => $displayembed, 'link' => $link));
    $setting = new admin_setting_configmulticheckbox(
        $name,
        $title,
        $description,
        array('pdf' => 1, 'url' => 1, 'html' => 1),
        $allowedresourcetypes
    );
    $page->add($setting);
    $settingscategory->add($page);

    // Photo tile settings.
    $page = new admin_settingpage('format_supertiles/tab-phototilesettings', get_string('phototilesettings', 'format_supertiles'));

    $name = 'format_supertiles/allowphototiles';
    $title = get_string('allowphototiles', 'format_supertiles');
    $description = get_string('allowphototiles_desc', 'format_supertiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_supertiles/phototilesaltstyle';
    $title = get_string('phototilesaltstyle', 'format_supertiles');
    $description = get_string('phototilesaltstyle_desc', 'format_supertiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    // Tile title CSS adjustments.
    $page->add(
        new admin_setting_heading('transparenttitleadjustments', get_string('transparenttitleadjustments', 'format_supertiles'),
            get_string('transparenttitleadjustments_desc', 'format_supertiles'))
    );

    $opacities = [0.3, 0.2, 0.1, 0];
    $choices = [];
    foreach ($opacities as $op) {
        $choices[(string)$op] = (string)($op * 100) . "%";
    }
    $setting = new admin_setting_configselect(
        'format_supertiles/phototiletitletransarency',
        get_string('phototiletitletransarency', 'format_supertiles'),
        get_string('phototiletitletransarency_desc', 'format_supertiles'),
        "0",
        $choices);
    $page->add($setting);

    // Tile title line height.
    $choices = [];
    for ($x = 30.0; $x <= 33.0; $x += 0.1) {
        $choices[$x * 10] = $x;
    }
    $setting = new admin_setting_configselect(
        'format_supertiles/phototitletitlelineheight',
        get_string('phototitletitlelineheight', 'format_supertiles'),
        '',
        305,
        $choices);
    $page->add($setting);

    // Tile title line line padding.
    $choices = [];
    for ($x = 0.0; $x <= 6.0; $x += 0.5) {
        $choices[$x * 10] = $x;
    }
    $setting = new admin_setting_configselect(
        'format_supertiles/phototitletitlepadding',
        get_string('phototitletitlepadding', 'format_supertiles'),
        '',
        40,
        $choices);
    $page->add($setting);
    $settingscategory->add($page);

    // Browser Session Storage (storing course content).
    $page = new admin_settingpage('format_supertiles/tab-browserstorage', get_string('browserstorage', 'format_supertiles'));
    $choices = [];
    for ($x = 0; $x <= 20; $x++) {
        $choices[$x] = $x;
    }

    $name = 'format_supertiles/assumedatastoreconsent';
    $title = get_string('assumedatastoreconsent', 'format_supertiles');
    $description = get_string('assumedatastoreconsent_desc', 'format_supertiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $setting = new admin_setting_configselect(
        'format_supertiles/jsmaxstoreditems',
        get_string('jsmaxstoreditems', 'format_supertiles'),
        get_string('jsmaxstoreditems_desc', 'format_supertiles'),
        8,
        $choices);
    $page->add($setting);

    $choices = [];
    for ($x = 30; $x <= 300; $x += 30) {
        $choices[$x] = $x;
    }
    $setting = new admin_setting_configselect(
        'format_supertiles/jsstoredcontentexpirysecs',
        get_string('jsstoredcontentexpirysecs', 'format_supertiles'),
        get_string('jsstoredcontentexpirysecs_desc', 'format_supertiles'),
        120,
        $choices);
    $page->add($setting);

    $choices = [];
    for ($x = 2; $x <= 30; $x += 2) {
        $choices[$x] = $x;
    }
    $setting = new admin_setting_configselect(
        'format_supertiles/jsstoredcontentdeletemins',
        get_string('jsstoredcontentdeletemins', 'format_supertiles'),
        get_string('jsstoredcontentdeletemins_desc', 'format_supertiles'),
        10,
        $choices);
    $page->add($setting);

    $settingscategory->add($page);

    // Javascript navigation settings.
    $page = new admin_settingpage('format_supertiles/tab-jsnav', get_string('jsnavsettings', 'format_supertiles'));

    $name = 'format_supertiles/usejavascriptnav';
    $title = get_string('usejavascriptnav', 'format_supertiles');
    $description = get_string('usejavascriptnav_desc', 'format_supertiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_supertiles/reopenlastsection';
    $title = get_string('reopenlastsection', 'format_supertiles');
    $description = get_string('reopenlastsection_desc', 'format_supertiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_supertiles/usejsnavforsinglesection';
    $title = get_string('usejsnavforsinglesection', 'format_supertiles');
    $description = get_string('usejsnavforsinglesection_desc', 'format_supertiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_supertiles/fittilestowidth';
    $title = get_string('fittilestowidth', 'format_supertiles')
        . ' ' . get_string('experimentalsetting', 'format_supertiles');
    $description = get_string('fittilestowidth_desc', 'format_supertiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $settingscategory->add($page);

    // Other settings.
    $page = new admin_settingpage('format_supertiles/tab-other', get_string('other', 'format_supertiles'));

    $name = 'format_supertiles/allowsubtilesview';
    $title = get_string('allowsubtilesview', 'format_supertiles');
    $description = get_string('allowsubtilesview_desc', 'format_supertiles');
    $default = 1;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_supertiles/showseczerocoursewide';
    $title = get_string('showseczerocoursewide', 'format_supertiles');
    $description = get_string('showseczerocoursewide_desc', 'format_supertiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $name = 'format_supertiles/allowlabelconversion';
    $title = get_string('allowlabelconversion', 'format_supertiles')
        . ' ' . get_string('experimentalsetting', 'format_supertiles');
    $description = get_string('allowlabelconversion_desc', 'format_supertiles');
    $default = 0;
    $page->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    $setting = new admin_setting_configtext(
        'format_supertiles/documentationurl',
        get_string('documentationurl', 'format_supertiles'),
        get_string('documentationurl_descr', 'format_supertiles'),
        'http://evolutioncode.uk/tiles',
        PARAM_RAW,
        50
    );
    $page->add($setting);

    // Custom css.
    $name = 'format_supertiles/customcss';
    $title = get_string('customcss', 'format_supertiles');
    $description = get_string('customcssdesc', 'format_supertiles');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $page->add($setting);

    $settingscategory->add($page);

    $ADMIN->add('formatsettings', $settingscategory);
}
