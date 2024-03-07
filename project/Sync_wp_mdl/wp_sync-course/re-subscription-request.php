<?php
global $wpdb,$woocommerce; 
get_header();
echo '<script type="text/javascript" src="'.plugins_url().'/sync-course/custom.js"></script>';
 ?>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
		 $uid = $_GET['uid'];
		 $price = $_GET['pr'];
		 $uc = $_GET['nos'];
		 $user_details = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."family_form WHERE user_id ='".$uid."'");
		 $user_count = $user_details[0]->member_count;
		//print_r($user_details);
		
		if($userid[0]->type==0){
							
							$child = "Month";
						}else{
							$child = "Year";  
						} 
		
        ?>

        <div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<article class="post-content">
 
<div class="row thank-main">
					<div class="col-sm-12 thank-section">						
					</div>
					<!--div class="col-sm-6">
						<div class="col-sm-12 thank-block">
							<h4>Content Here</h4>
							<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
						</div>
					</div-->
					<div class="col-sm-10 col-sm-offset-1 thank-block">
						<div class="col-sm-6  thank-block1">
 						<h3 class="text-center">Details</h3><br>
						<div><strong>Name:</strong><?= $user_details[0]->first_name .'&nbsp;'.$user_details[0]->last_name ?></div>
						<div><strong>Email:</strong><?= $user_details[0]->email ?></div>
						<div><strong>Subscribtion Type:</strong><?= $child ?> </div>
						<div><strong>Subscribtion Charge:</strong><?= $price ?>£ </div>
						<div><strong>How Many Child Add:</strong><?= $uc ?> </div>
						</div>
						
							<div class="col-sm-6 thank-block2">
					 	
		<form action="/trail-responce/?uc=<?=$uc?>&price=<?= $price ?>&id=<?= $uid ?>&int=<?= $_GET['int']?>" method="post" class="frmStripePayment">
  <div class="text-center"><h3>Want to Join Studyif ?</h3><br><br>
  <script  src="https://checkout.stripe.com/checkout.js" class="stripe-button"
          data-key="<?php echo $stripe['publishable_key']; ?>"
          data-name="GOALSTART"
          data-description="ABONNEMENT 1 MOIS"
          data-panel-label="Subscribe"
          data-label="Subscribe Now"
          data-locale="auto">></script>
		  </div>
</form>
					 </div>
						</div>
					</div>
					</div>
					
					 
				</div>
	</article>
</div>
</div>
</div>
 
    </main><!-- .site-main -->
 
   
 
</div><!-- .content-area -->
 

<?php get_footer(); ?>