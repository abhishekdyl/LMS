<?php
require_once('../../../../wp-config.php');
$post_id=$_POST['post_id'];
$url=wp_get_attachment_image_url($post_id,'full');
echo $url;
