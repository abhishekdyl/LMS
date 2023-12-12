            foreach ($course as $key) {

                $handler = core_course\customfield\course_handler::create();
                $customfields = $handler->get_instance_data($key->id);
                $customfieldsdata = $handler->display_custom_fields_data($customfields);
                $customfieldscontent = \html_writer::tag('div', $customfieldsdata, ['class' => 'customfields-container']);
                $rating = (!empty($customfieldsdata))? $customfieldscontent:'<div class="customfield customfield_textarea customfield_tool_courserating">
                    <span class="customfieldname">Course rating</span><span class="customfieldseparator">: </span><span class="customfieldvalue"><span class="tool_courserating-cfield"><a class="tool_courserating-ratings tool_courserating-ratings-courseid-7" title="Course ratings" href="#"><span class="tool_courserating-stars">  <span class="tool_courserating-stars"><i class="icon fa fa-star-o fa-fw " aria-hidden="true"></i><i class="icon fa fa-star-o fa-fw " aria-hidden="true"></i><i class="icon fa fa-star-o fa-fw " aria-hidden="true"></i><i class="icon fa fa-star-o fa-fw " aria-hidden="true"></i><i class="icon fa fa-star-o fa-fw " aria-hidden="true"></i></span>
                    </div>
                    ';
                }



------------------------ Get data moodle custom field in course fuction ------------------------------------


private static function get_course_metadata($courseid) {
        $handler = \core_customfield\handler::get_handler('core_course', 'course');
        $datas = $handler->get_instance_data($courseid);
        $metadata = [];
        foreach ($datas as $data) {
            //echo 'data: '.$data->get_value();
            if (empty($data->get_value())) {
                continue;
            }
            $cat = $data->get_field()->get_category()->get('name');
            $metadata[$data->get_field()->get('shortname')] = $data->get_value();
        }
        return $metadata;
    }

------------------------ Get data moodle custom field in course fuction ------------------------------------

  function get_course_metadata($id) {
        $handler = \core_customfield\handler::get_handler('core_course', 'course');
        $datas = $handler->get_instance_data($id, true);
        $metadata = [];
        foreach ($datas as $data) {
            //echo 'data: '.$data->get_value();
            if (empty($data->get_value())) {
                continue;
            }
            $metadata[$data->get_field()->get('shortname')] = $data->get_value();
        }
        return $metadata;
    }