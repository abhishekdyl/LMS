<?php
global $wpdb,$woocommerce; 
get_header();
/*$user22 = wp_get_current_user();
echo "<pre>";
print_r($user22);*/
$user_count =1;
$mnthlyprice = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix ."options  WHERE option_name='monthly_package'" );
$monthly_package = $mnthlyprice->option_value; 
echo '<script type="text/javascript" src="'.plugins_url().'/sync-course/custom.js"></script>';
$uid = 0;
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">     
		<div class="container">
		    <div class="row">
		        <div class="col-xs-12 col-sm-12 col-md-12">
		            <article class="post-content">        
			 
					 	<div class="member-form">
					        <div class="col9 member-form-main">
					            <form  method="post">
					                <h2>Become a member</h2>
					                <div class="member-header">
					                  <input type="hidden" id="uid" name="uid" value="<?php echo $uid; ?>" >
					                    <div class="divider"></div>
					                    <div class="row row2">
					                        <div class="col12 flexlay">
					                            <h5>Choose a plan</h5>
					                        </div>
					                        <div class="col6 flexlay">
					                            <div class="planbtn">
					                                <label for="planBtn1"><input type="radio" id="planBtn1" name="planBtn" value="0" checked><p>Monthly</p></label>
					                            </div>
					                        </div>
					                        <div class="col6 flexlay">
					                            <div class="planbtn">
					                                <label for="planBtn2"><input type="radio" id="planBtn2" name="planBtn" value="1" ><p>Annual</p></label>
					                            </div>
					                        </div>
					    
					                    </div>
					                </div>
					                <div class="divider"></div>
					    
					                <div class="one-row">
					                <div class="row row3">
					                    <div class="col12 flexlay">
					                        <h5>Choose number of children</h5>
					                    </div>
					                    <div class="flex-div-center">
						                    <div class="col12 flexlay">
						                        <div class="incre-decre">
											      <div class="value-button" id="decrease" onclick="decreaseValue()" value="Decrease Value">-</div>
											      <input type="number" id="number"  value="1" />
												  <input type="hidden" id="number2"  value="1" />
											      <input type="hidden" id="member_count"  value="<?php echo $user_count; ?>" />
											      <div class="value-button" id="increase" onclick="increaseValue()" value="Increase Value">+</div>
											    </div>
											    <p id='each_id'>Each additional child is only £2 !</p>
						                    </div>
					                	</div>
					              	</div>
					                <div class="row row4">
					                    <div class="col12 flexlay">
					                        <h5>Desired subjects</h5>
					                    </div>
					                    <div class="flex-div-center">
						                    <div class="col12 flexlay">
						                        <button type="button" class="select-option-active box-monthly">
						                            <div class="productOption-name">Maths & English</div>
						                            <div class="productOption-tag">Reception–Year 13</div>
						                            <div class="productOption-price">£<span id="pmonth"><?php echo $monthly_package; ?></span></div>                     
						                            <div class="productOption-term">per year</div>
						                            <input type="hidden" name="month" value="<?php echo $monthly_package; ?>">
						                            <input type="hidden" name="monthd" value="<?php echo $monthly_package; ?>">
						                        </button>
						                        <button type="button" class="select-option-active box-annual">
						                            <div class="productOption-name">Maths</div>
						                            <div class="productOption-tag">Reception–Year 5</div>
						                            <div class="productOption-price">£<span id="pyear"></span></div>                        
						                            <div class="productOption-term">per year</div>
						                            <input type="hidden" name="year" value="">
						                            <input type="hidden" name="yeard" value="">
						                        </button>
						                        <p>Your membership will be renewed automatically. You can cancel online anytime.</p>
						                    </div>
					                	</div>
					                </div>
					            </div>
					    
					                <div class="row row5">
					                        <div class="divider"></div>
					                    <div class="col12 flexlay">
					                        <h2>Enter contact information</h2>
					                    </div>
					                    <div class="col12 flexlay my-member-cust-form">

					                        <div class="row">
					                            <div class="col-sm-6">
					                                <label for="uname">Username </label>
					                                <input type="text" class="form-control" id="uname" name="uname" required >
					                            </div>
					                            <div class="col-sm-6">
					                                <label for="email">E-mail address </label>
					                                <input type="email" class="form-control" id="email" name="email" >
					                            </div>
					                            
					                        </div>
					                        <div class="row">				                            
					                            <div class="col-sm-6">
					                                <label for="fname">First Name</label>
					                                <input type="text" class="form-control" id="fname" name="fname" required >
					                            </div>
					                            <div class="col-sm-6">
					                                <label for="lname">Last Name</label>
					                                <input type="text" class="form-control" id="lname" name="lname" required >
					                            </div>
					                        </div>
					                        <div class="row">				                            
					                            <div class="col-sm-6">
					                                <label for="password">Password</label>
					                                <input type="password" class="form-control" id="password" name="password" required >
					                            </div>
					                            <div class="col-sm-6">
					                                <label for="cpassword">Confirm Password</label>
					                                <input type="password" class="form-control" id="cpassword" name="cpassword" required >
					                            </div>
					                        </div>
					                    </div>
					                </div> 
					                <div class="row row6">
					                	<div class="col12 text-center">
					                        <span class="error_message"></span>
					                    </div>
					                    <div class="col12 text-center">
					                        <button class="sbmt-btn" type="button" onclick="trailfunction()">Join Studyif</button>
					                    </div>
					                </div>
					            </form>
					        </div>				        
					        <div class="col3 flexlay" id="side-bar">
					            <div class="last-content">
						            <h3 class="side-heading">Purchase summary</h3>
						            <div class="sidebars-para">
							            <p id="ftext">Family monthly membership</p>
							            <p> <span id="child">1 </span> <span id="cstring"> child</span></p>
							            <p>Maths & English</p>
							            <h1 id="hprize">£11.99</h1>
						            </div>
						            <div class="sidebars-para">
						                <p>Our guarantee</p>
					                    <p> If you're not satisfied within 30 days, </p>
					                    <p>   we'll gladly provide a full refund.</p>
						            </div>
						                <h3 class="side-heading">Benefits of joining</h3>
						                <ul class="reg-side-arrow">
							                <li><i class="fa fa-angle-right"></i> Thousands of skills</li>
							                <li><i class="fa fa-angle-right"></i> Fun practice environment</li>
							                <li> <i class="fa fa-angle-right"></i> Fun practice environment</li>
							                <li><i class="fa fa-angle-right"></i> Research based</li>
							                <li><i class="fa fa-angle-right"></i> Detailed reporting</li>
							                <li><i class="fa fa-angle-right"></i> Used by millions worldwide!</li>
						                </ul>
						                <ul class="purchase-sidebar-support">
						                    <li><h4>Questions?</h4></li>
							                <li><a href="mailto:info@studyif.com">info@studyif.com</a></li>
							                <li><a href="/help-center/topic#552370">Membership FAQ</a></li>
							            </ul>
					        	</div>
					    	</div>
					    </div>   
					</article>
			    </div>
			</div>
		</div> 
	</main><!-- .site-main -->
</div><!-- .content-area -->

<script type="text/javascript">
var m_count = jQuery('#member_count').val();
jQuery('#planBtn2').click(function (e) {
	jQuery.ajax({
		url:'<?php echo admin_url('admin-ajax.php')?>',
		type: 'POST',
		data:{'action':'get_yearly_price'},
		success: function ( data )
		{
			var annobj1 = JSON.parse(data);
			console.log(annobj1.price);
			if (annobj1.price) 
			{
				if(m_count==undefined){
					jQuery('.member-form').addClass('annual');
					jQuery('#number').val(1);
					jQuery('#pyear').text(annobj1.price);
					jQuery('[name=year]').val(annobj1.price);
					jQuery('#hprize').text('£'+annobj1.price);
					jQuery('[name=year]').val(annobj1.price);
					jQuery('[name=yeard]').val(annobj1.price);
					jQuery('#ftext').text('Family annual membership');
					jQuery('#each_id').text("Each additional child is only £20 !");
				}
				else{
					jQuery('.member-form').addClass('annual');
					jQuery('#number').val(m_count);
					jQuery('#pyear').text(annobj1.price*m_count);
					jQuery('[name=year]').val(annobj1.price*m_count);
					jQuery('#hprize').text('£'+annobj1.price*m_count);
					jQuery('[name=year]').val(annobj1.price*m_count);
					jQuery('[name=yeard]').val(annobj1.price*m_count);
					jQuery('#ftext').text('Family annual membership');
					jQuery('#each_id').text("Each additional child is only £20 !");
				}
			}
		}
	});
}); 

jQuery('#planBtn1').click(function (e) 
{
	jQuery.ajax({
		url:'<?php echo admin_url('admin-ajax.php')?>',
		type: 'POST',
		data:{'action':'get_monthly_price'},
		success: function ( data ) 
		{
			//console.log(data);
			var mnthlyobj = JSON.parse(data);
			//console.log(mnthlyobj);
			//alert(m_count);
			if (mnthlyobj.price) 
			{
				if(m_count==undefined){
					jQuery('.member-form').removeClass('annual');
					jQuery('#number').val(1);					
					jQuery('#pmonth').text(mnthlyobj.price);
					jQuery('[name=month]').val(mnthlyobj.price);
					jQuery('[name=monthd]').val(mnthlyobj.price);
					jQuery('#hprize').text('£'+mnthlyobj.price);
					jQuery('#ftext').text('Family monthly membership');
					jQuery('#each_id').text("Each additional child is only £2 !")
				}else{			
					jQuery('.member-form').removeClass('annual');
					jQuery('#number').val(m_count);					
					jQuery('#pmonth').text(mnthlyobj.price*m_count);
					jQuery('[name=month]').val(mnthlyobj.price*m_count);
					jQuery('[name=monthd]').val(mnthlyobj.price*m_count);
					jQuery('#hprize').text('£'+mnthlyobj.price*m_count);
					jQuery('#ftext').text('Family monthly membership');
					jQuery('#each_id').text("Each additional child is only £2 !")
				}
			}
		}
	});
});

function trailfunction(){ 
	var uid = jQuery('[name="uid"]').val();
	var get_type =jQuery('[name="planBtn"]:checked').val();
	var num_of_student = jQuery('#number').val();
	var month_price = parseFloat(jQuery('#pmonth').text());
	var year_price = parseFloat(jQuery('#pyear').text());
	var username = document.getElementById("uname").value;
	var email = document.getElementById("email").value;
	var fname = document.getElementById("fname").value;
	var lname = document.getElementById("lname").value;
	var password = document.getElementById("password").value;
	var cpassword = document.getElementById("cpassword").value;
	if (username == '') {
		jQuery('.error_message').text("Please enter username");
	} else if (email == '') {
		jQuery('.error_message').text("Please enter email id");
	}else if(password ==''){
		jQuery('.error_message').text("Please enter password");
	}else{
		jQuery.ajax({
			url:'<?php echo admin_url('admin-ajax.php')?>',
			type: 'POST',
			data:{'action':'trail_buy',
					'uid':uid,
					'get_type':get_type,
					'num_of_student':num_of_student,
					'month_price':month_price,
					'year_price':year_price,
					'username':username,
					'email':email,
					'fname':fname,
					'lname':lname,
					'password':password,
					'cpassword':cpassword,
				},
			success: function (result) 
			{
				var annobj1 = JSON.parse(result);
				var error_message = annobj1.success;
				console.log('aaa',annobj1.success);

				if(annobj1.success==false){
					jQuery('.error_message').text(annobj1.message.message); 
				}else{
					console.log(result);
					window.location.href="<?php echo get_site_url();?>/?page_id=6973/?&uid="+annobj1.uid+"&mid="+annobj1.mid;
				}

				

			}
		});	
	}
}

</script>
	<?php get_footer(); ?>