<?php 
require_once('../../config.php');
require_once('payment_config.php'); 

global $DB, $USER, $PAGE;
$PAGE->requires->jquery();

require_login();
$current_logged_in_user = $USER->id;
$PAGE->set_title('Assignment Subscription');
$PAGE->set_heading('Assignment Subscription');

$query_setting = 'SELECT * FROM {assign_subs_settings}';
$row_setting = $DB->get_record_sql($query_setting);

$recurring_cost = $row_setting->recurring_cost;
$one_off_cost = $row_setting->one_off_cost;
$recurring_duration = $row_setting->recurring_duration;
$stripe_currency = $row_setting->stripe_currency;



$sql_chk = "SELECT * FROM {assign_subs_users} WHERE userid=?";
$allforums_chk = $DB->get_record_sql($sql_chk, array($USER->id));
$haveactivesubs = false;



if($allforums_chk->end_date>time() && ($allforums_chk->status==1 || $allforums_chk->status==0))
{
	
	$end_date = date("d/m/Y", $allforums_chk->end_date);
    $subscription_type = $allforums_chk->subscription_type;
    $stripe_canceled_status = $allforums_chk->stripe_canceled_status;
	$haveactivesubs = true;
	
}


echo $OUTPUT->header();
if(!empty($_SERVER['HTTP_REFERER'])){
	$_SESSION['redirectedtopayfrom'] = $_SERVER['HTTP_REFERER'];
}
?>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>

<style type="text/css">
.container{
font-family: Georgia;
}
.payButton_recurring{
border-radius: 15px;
background-color: #DB3132;
padding: 12px;
text-decoration: none; 
color: #fff;
}
.payButton_oneoff{
border-radius: 15px;
background-color: #DB3132;
padding: 12px;
text-decoration: none; 
color: #fff;
}
</style>


<div class="container">
<div id="paymentResponse" class="hidden"></div>

<?php
if($haveactivesubs === true){
?>
	<div class="row">
		<div class="col-md-12" style="background-color: #DB3132; color: white; border: 4px solid black; border-radius: 30px;" align="center">
			<?php if($subscription_type==0 OR $subscription_type==1){ ?>
					<span>
						<h2  style="padding: 50px;">You have an active subscription which is due to expire on <span style="color: black;"><?php echo $end_date; ?></span>.You can renew your subscription when it expires.</h2>
					</span>
			<?php }if($subscription_type==2){ ?>

				<?php if($stripe_canceled_status==0){ ?>
					<span>
						<h2  style="padding: 50px;">You have an active recurrent priority subscription which is due for automatic renewal on <span style="color: black;"><?php echo $end_date; ?></span>.</h2>
					</span>
				<?php }else{ ?>
					<span>
						<h2  style="padding: 50px;">You have an active subscription which is due to expire on <span style="color: black;"><?php echo $end_date; ?></span>.You can renew your subscription when it expires.</h2>
					</span>
			 	<?php } } ?>
		</div>
	</div>

	<div class="row" style="margin-top: 30px;">
		<div class="col-md-12" align="center">
			<p><h2><b><?php $confirm='Are you sure to cancel your subscription?'; if($subscription_type==0 OR $subscription_type==1){ echo "You can renew your subscription when it is expired."; }else{ if($stripe_canceled_status==0){ echo 'If you wish to cancel your subscription, please click <a href="cancel_subscription.php" onclick="return confirm('."'".$confirm."'".')">HERE</a>'; }else{ echo 'You have cancelled your recurrent subscription plan.'; }} ?> </b></h2></p>
		</div>
	</div>
<?php
} else {
?>
	<div class="row">
	<div class="col-md-12" style="background-color: #DB3132; color: white; border: 4px solid black; border-radius: 30px;" align="center">
		<span>
			<h2  style="padding: 50px;">Upgrade your account from general to a priority account and receive your feedback within <span style="color: black;">24 hours</span> Mon-Fri!</h2>
		</span>
	</div>
	</div>


	<div class="row" style="margin-top: 30px;">
		<div class="col-md-5">
			<div style="margin: 10px; padding: 10px; border: 2px solid black; border-radius: 30px;" align="center">
				<p><?php echo strtoupper($stripe_currency); ?> <?php echo $recurring_cost; ?> <?php echo $recurring_duration; ?> Subscription</p>
				
				<input type="hidden" id="subscr_plan" value="2">
				<p><button  class="payButton_recurring"><div class="spinner_recurring hidden" class="spinner_recurring">Stripe Subscription</div><span class="buttonText_recurring">Stripe Subscription</span></button></p>
				<p>Pay through a <?php echo $recurring_duration; ?> Subscription</p>
			</div>
		</div>

		<div class="col-md-2" align="center" style="transform: translate(0px,20px);">
			<p ><h2><b>OR</b></h2></p>
		</div>

		<div class="col-md-5">
			<div style="margin: 10px; padding: 10px; border: 2px solid black; border-radius: 30px;" align="center">
				<p><?php echo strtoupper($stripe_currency); ?> <?php echo $one_off_cost; ?> Annual One-off</p>
				<input type="hidden" id="subscr_plan" value="1">
				<p><button class="payButton_oneoff"><div class="spinner_oneoff hidden"  class="spinner_oneoff">Stripe</div><span class="buttonText_oneoff">Stripe</span></button></p>
				<p>Pay One-off</p>
			</div>
		</div>
	</div>
<?php
}
?>

</div>





<script>

	$(document).ready(function() {


	// Select payment button payBtn
	const btn = document.querySelectorAll(".payButton_recurring, .payButton_oneoff");
	const subscr_plan_id = document.getElementById("subscr_plan").value;

	[].forEach.call(btn, function(payBtn) {
	  payBtn.addEventListener('click', function(event) { 
		// Set Stripe publishable key to initialize Stripe.js
		const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
        if(this.innerText=='Stripe Subscription'){  


      			// Show a spinner on payment processing
				function setLoading(isLoading) {
				    if (isLoading) {
				        // Disable the button and show a spinner
				        payBtn.disabled = false;
				        document.querySelector(".spinner_recurring").classList.remove("hidden");
				        document.querySelector(".buttonText_recurring").classList.add("hidden");
				    } else {
				        // Enable the button and hide spinner
				        payBtn.disabled = false;
				        document.querySelector(".spinner_recurring").classList.add("hidden");
				        document.querySelector(".buttonText_recurring").classList.remove("hidden");
				    }
				}


				// Display message
				function showMessage(messageText) {
				    const messageContainer = document.querySelector("#paymentResponse");
					
				    messageContainer.classList.remove("hidden");
				    messageContainer.textContent = messageText;
					
				    setTimeout(function () {
				        messageContainer.classList.add("hidden");
				        messageText.textContent = "";
				    }, 5000);
				}


      			// Create a Checkout Session with the selected product
				const createCheckoutSession = function (stripe) {

				    return fetch("payment_init.php?ptype=recurring", {
				        method: "POST",
				        headers: {
				            "Content-Type": "application/json",
				        },
				        body: JSON.stringify({
				            request_type:'create_customer_subscription', createCheckoutSession: 1,
				        }),
				    }).then(function (result) {
				        return result.json();
				    });
				};

	      		setLoading(true);
			    createCheckoutSession().then(function (data) {
			    	// window.location = 'payment_success.php';
			        if(data.sessionId){
			            stripe.redirectToCheckout({
			                sessionId: data.sessionId,
			            }).then(handleResult);
			        }else{
			            handleResult(data);
			        }
			    });


		    

				// Handle any errors returned from Checkout
				const handleResult = function (result) {
				    if (result.error) {
				        showMessage(result.error.message);
				    }
				    
				    setLoading(false);
				};
      	}
    	




    	if(this.innerText=='Stripe'){  

    			// Show a spinner on payment processing
				function setLoading(isLoading) {
				    if (isLoading) {
				        // Disable the button and show a spinner
				        payBtn.disabled = false;
				        document.querySelector(".spinner_oneoff").classList.remove("hidden");
				        document.querySelector(".buttonText_oneoff").classList.add("hidden");
				    } else {
				        // Enable the button and hide spinner
				        payBtn.disabled = false;
				        document.querySelector(".spinner_oneoff").classList.add("hidden");
				        document.querySelector(".buttonText_oneoff").classList.remove("hidden");
				    }
				}


				// Display message
				function showMessage(messageText) {
				    const messageContainer = document.querySelector("#paymentResponse");
					
				    messageContainer.classList.remove("hidden");
				    messageContainer.textContent = messageText;
					
				    setTimeout(function () {
				        messageContainer.classList.add("hidden");
				        messageText.textContent = "";
				    }, 5000);
				}



    			// Create a Checkout Session with the selected product
				const createCheckoutSession = function (stripe) {
				    return fetch("payment_init.php?ptype=oneoff", {
				        method: "POST",
				        headers: {
				            "Content-Type": "application/json",
				        },
				        body: JSON.stringify({
				            request_type:'create_customer_subscription', createCheckoutSession: 1,
				        }),
				    }).then(function (result) {
				        return result.json();
				    });
				};



				// Handle any errors returned from Checkout
				const handleResult = function (result) {
				    if (result.error) {
				        showMessage(result.error.message);
				    }
				    
				    setLoading(false);
				};



	    		setLoading(true);
			    createCheckoutSession().then(function (data) {
			        if(data.sessionId){
			            stripe.redirectToCheckout({
			                sessionId: data.sessionId,
			            }).then(handleResult);
			        }else{
			            handleResult(data);
			        }
			    });


    	 }


    });		



	})

});
</script>


<?php echo $OUTPUT->footer(); ?>
