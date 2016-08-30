<?php 
add_action( 'wp_header', 'rushtix_popups_html' );

// This function output all the html for pop-ups
function rushtix_popups_html() {
	 global $current_user; ?>
<div style="display:none;">
	<div id="single-event-lightbox" class="single-event-lightbox mfp-hide">
	  <div id="buddypress">
	    <div class="full-evt-pg">
	    <a href="#" target="_blank" ><img src="https://rushtix.com/wp-content/uploads/2015/10/outer-link.png" /></a>
		<span class="descrip">Go to the full event page.</span>
	  </div>

	  <span class="close-single-evt"><span class="descrip">Go back to the feed. </span><svg width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path d="M6 4.586l4.24-4.24c.395-.395 1.026-.392 1.416-.002.393.393.39 1.024 0 1.415L7.413 6l4.24 4.24c.395.395.392 1.026.002 1.416-.393.393-1.024.39-1.415 0L6 7.413l-4.24 4.24c-.395.395-1.026.392-1.416.002-.393-.393-.39-1.024 0-1.415L4.587 6 .347 1.76C-.05 1.364-.048.733.342.343c.393-.393 1.024-.39 1.415 0L6 4.587z" fill-rule="evenodd"></path></svg>
	   </span>
	    <div class="item mainitem"></div>
	    <div class="evdesc"></div>
	  </div>
	</div>

	<div id="booking-msg" class="memberful-box mfp-hide">
		<div class="header">
		    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
		  	<h1>SUCCESS!</h1>
		</div>
		<div class="checkout">
			<p class="mesg">Thank you for your reservation. You will receive an email shortly. If you do not see your order confirmation in your inbox, make sure to check your junk/spam folder. If there are any issues please contact us at <a href="mailto:hello@rushtix.com?subject=Booking Question" target="_blank">hello@rushtix.com</a>. Enjoy!</p>
		</div>
	</div> <!--#booking-msg--> 

	<div id="cancel-subscrip-html" class="memberful-box">
	  <div style="text-align:center">
	  <img  src="<?php echo plugins_url('rushtix-stripe/assets/logo.png')?>" width="157" height="44"></div>
	  <br>
	  <div id="buddypress" style="margin:0; padding:0">
	  <h3 >Cancel options</h3>

	  <div class="cancgrp">
	  <input type="radio" name="cancel_mem_opts" class="rt_cancel_mem" id="extend_trial_14"  /><label for="extend_trial_14">Extend your trial for 14 days.</label><br>
	  </div>
	  <div class="cancgrp">
	  <input type="radio" name="cancel_mem_opts" class="rt_cancel_mem" id="month_3_discount" /><label for="month_3_discount">Get a discount for 3 months.</label>
	  <br>
	  </div>
	  <div class="cancgrp">
	  <input type="radio" name="cancel_mem_opts" class="rt_cancel_mem" id="pause_billing" /><label for="pause_billing">Pause your billing.</label>
	  <br>
	  </div>
	  <div class="cancgrp">
	  <input type="radio" name="cancel_mem_opts" class="rt_cancel_mem" id="re_demo_ceo" /><label for="re_demo_ceo">Request a demo with the CEO.</label>
	  <br>
	  </div>
	  <div class="cancgrp">
	  <input type="radio" name="cancel_mem_opts" class="rt_cancel_mem" id="cancel_account" /><label for="cancel_account">Cancel your account and leave us feedback.</label>
	  <br>
	  </div>
	  </div>
	</div>

	<div id="rt-login" class="memberful-box">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	    <h1>Sign in</h1>
	  </div>
	  <div class="checkout">
	    <form action="">
	     <div class="messages"></div>
	    <div class="fields">
	            <input name="user_login" id="login-user" placeholder="Email" type="text" >
	            <input name="password" id="login-pass" placeholder="Password" type="password" />
	       </div>
	     <div class="memberful-pay">
	          <button id="rt-sign-in">
	            <span class="default-message purchasing">
	              Sign me in!
	              </span>
	          </button>
	          <p class="password-reset">
	    Forgot your password? <a href="/wp-login.php?action=lostpassword" class="reset-pass-link">Reset it</a>.
	    </p>
	        </div>
	   </form>
	      </div>
	</div>

	<div id="rt-lost-password" class="memberful-box">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	    <h1>Forgot password</h1>
	  </div>
	  <div class="checkout">
	    <form action="">
	     <div class="messages"></div>
	     <h2>Reset your password</h2>
	     <br>
	    <div class="fields">
	      <input name="user_login" id="login-eml" placeholder="Email" type="text" >
	       </div>
	       <p class="password-reset" style="float:left; text-align:left">We will email you instructions for resetting your password.</p>
	       <div class="memberful-pay">
	          <button id="rt-reset-instr">
	            <span class="default-message purchasing">
	              Reset my password
	  
	              </span>
	          </button>
	          <p class="password-reset">
	     <a href="#" class="header_login">Back to sign in</a>
	    </p>
	        </div>
	      </form>
	      </div>
	</div>

	<div id="guest-signup" class="memberful-box">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	    <h1>Sign Up</h1>
	    <h2>Sign-up for free tickets.</h2>
	   </div>
	  <div class="checkout">
	   <div class="messages"></div>  
	     

	    <div id="checkout-fields" class="partial active">
	        <div class="memberful-account-signup-form">
	          <input name="first_name" id="guest-fname" placeholder="First" type="text" style="width:48%; float:left;margin-right:2%;" >
	          <input name="last_name" id="guest-lname" placeholder="Last" type="text" style="width:48%; float:right;margin-left:2%;">
	          <input name="email" id="guest-email" placeholder="Email" type="email" >
	          <input type="hidden" id="guest-grouptoadd" value=""/>
	          <input name="password" id="guest-password" placeholder="Choose a password" type="password" autocomplete="new-password" />
	     </div>
	      
	      <div class="memberful-pay">
	        <button id="guest-checkout">
	          <span class="default-message purchasing">
	            Create a Free Account
	            </span>
	        </button>
	      </div>
	       <p class="member-sign-in memberful-form-note header_login">
	      Already have an account?
	      <a href="#" >Sign in</a>
	      </p>
	    </div>
	  </div>
	</div>

	<div id="stripe-signup" class="memberful-box">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	    <h1>RushTix</h1>
	    <h2>
	        <span>Starter Pass Membership</span>
	    </h2>
	  </div>
	  <div class="checkout">
	   <div class="messages"></div>
	  <?php if(!is_user_logged_in()){ ?>   
	      <p class="member-sign-in memberful-form-note header_login">
	      Already have an account?
	      <a href="#" >Sign in</a>
	      </p>

	    <div id="checkout-fields" class="partial active">
	        <div class="memberful-account-signup-form">
	          <input name="first_name" id="first_name" placeholder="First" type="text" style="width:48%; float:left;margin-right:2%;" value="Haris" >
	          <input name="last_name" id="last_name" placeholder="Last" type="text" style="width:48%; float:right;margin-left:2%;" value="20 Aug">
	          <input name="email" id="email" placeholder="Email" type="email" value="haris+20aug2@rushtix.com">
	          <input name="password" id="password" placeholder="Choose a password" type="password" autocomplete="new-password" value="123456" />
	     </div> 
	  <?php }else{
	    echo '<input type="hidden" id="email" value="'.$current_user->user_email.'" />
	    <input type="hidden" id="first_name" value="'.$current_user->first_name.'" />
	    <input type="hidden" id="last_name" value="'.$current_user->last_name.'" />';
	    } ?>
	      <form method="post">
	    <div id="credit-card-fields" class="memberful-payment-form">
	        <div class="secure-info">
	        <p class="memberful-form-note scpm">Secure Payment</p>
	        
	        <img alt="Visa, Mastercard, American Express." class="memberful-cc-icons" height="22" src="<?php echo plugins_url( 'rushtix-stripe/assets/cc-icons.png') ?>" width="108">
	        </div>
	      
	        <div class="memberful-card-information">
	          <div class="memberful-card-number">
	            <label style="display: none" for="cc-number">Card number:</label>
	            <input autocomplete="cc-number" data-stripe="number" id="cc-number"  placeholder="Card number" type="text" value="4242424242424242" > 
	          </div>
	          <div class="memberful-card-expires">
	            <label style="display: none" for="cc-expires">Expires:</label>
	            <input autocomplete="cc-exp" data-stripe="exp" id="cc-expires" maxlength="9" placeholder="MM / YY" type="text" 
	            value="02/2020">
	          </div>
	        <div class="memberful-billing-postal">
	          <input autocomplete="postal-code" autocompletetype="postal-code" data-stripe="address_zip" id="cc-postal-code" placeholder="Zip code" type="text">
	        </div>
	        <div class="memberful-csc">
	          <input autocomplete="cc-csc" data-stripe="cvc" id="cc-cvc" placeholder="CVC" type="text" value="987">
	        </div>
	         <div class="coupon-code">
	           <a href="#" class="strp-cpn-tgl password-reset" style="float:left;outline:none;margin-top: 0;font-size:13px; text-decoration: underline">Coupon Code</a>
	          <input data-stripe="coupon" id="cc-coupon" placeholder="Enter coupon code" type="text" style="display:none">
	        </div>
	      </div>
	       
	    </div>
	      <p class="tos" style="display: block; width:100%;color:#bbb; text-align:center; font-size:14px;">
	        <input type="checkbox" id="tos" value="yes" style="width: auto;margin-right: 5px; margin-top: 5px; display:none; float:left; margin-top:4px;" />I have read and agree with the<br>
	        <a href="/privacy" target="_blank" style="text-decoration:underline; color:#bbb">privacy policy</a>
	        and
	        <a href="/terms-of-use/" target="_blank" style="text-decoration:underline; color:#bbb">terms of service</a>.
	        
	        <p>
	      <div class="memberful-pay">
	        <button id="stripe-checkout" type="button">
	          <span class="default-message purchasing">
	            Start your free trial
	            </span>
	        </button>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>

	<div id="stripe-update-card" class="memberful-box mfp-hide">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	    <h1>RushTix</h1>
	    <h2>
	        <span>Update your card</span>
	    </h2>
	  </div>
	  <form action="#">
	  <div class="checkout">
	   <div class="messages"></div>
	      <div id="credit-card-fields" class="memberful-payment-form">
	        <div class="secure-info">
	        	<p class="memberful-form-note scpm">Secure Payment</p>
	        
	        	<img alt="Visa, Mastercard, American Express." class="memberful-cc-icons" height="22" src="<?php echo plugins_url( 'rushtix-stripe/assets/cc-icons.png')?>" width="108">
	        </div>
	        <div class="memberful-card-information">
	          <div class="memberful-card-number">
	            <label style="display: none" for="cc-number">Card number:</label>
	            <input autocomplete="cc-number" autocompletetype="cc-number" data-stripe="number" id="ucc-number" placeholder="Card number" type="text" value="" > 
	          </div>
	          <div class="memberful-card-expires">
	            <label style="display: none" for="cc-expires">Expires:</label>
	            <input autocomplete="cc-exp" autocompletetype="cc-exp" data-stripe="exp" id="ucc-expires" maxlength="9" placeholder="MM / YY" type="text" 
	            value="">
	            
	          </div>
	        </div>
	        <div>
	          <div class="memberful-billing-postal">
	            <input autocomplete="postal-code" autocompletetype="postal-code" data-stripe="address_zip" id="cc-postal-code" placeholder="Zip code" type="text">
	          </div>
	          <div class="memberful-csc">
	            <input autocomplete="cc-csc" autocompletetype="cc-csc" data-stripe="cvc" id="ucc-cvc" placeholder="CVC" type="text" value="">
	          </div>
	        </div>
	      </div>
	      <div class="memberful-pay">
	        <button id="card-update-btn">
	          <span class="default-message purchasing">
	            Update card info
	            </span>
	        </button>
	      </div>
	  </div>
	  </form>
	</div>

	<div id="verify-account-phone" class="memberful-box mfp-hide">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	    <h1>RushTix</h1>
	    <h2>
	        <span>Please add your mobile number to <br>verify your account.</span>
	    </h2>
	  </div>
	  <form action="#">
	  <div class="checkout">
	   <div class="messages"></div>
	      <div class="memberful-phone-number">
	            <label style="display: none" for="cc-number">Mobile number:</label>
	            <input id="mobile-phone-number" placeholder="" type="text" value="<?php echo get_user_meta( $current_user->ID,'mobile_number', true);?>" > 
	          </div>
	      <div class="memberful-pay">
	        <button id="phone-update-btn">
	          <span class="default-message updating">
	            Update
	            </span>
	        </button>
	      </div>
	  </div>
	  </form>
	</div>
	<?php if(is_user_logged_in()){ ?>
	<div id="rushtix-profile" class="memberful-box mfp-hide">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	  <h1><?php echo $current_user->display_name; ?></h1>
	  </div>
	  <div class="checkout">
	  <ul class="memberful-account-nav" >
	      <li><a href="#" class="active open-rt-profile">Profile</a></li>
	    <li><a href="<?php echo bp_core_get_user_domain($current_user->ID); ?>profile/edit" class="privacy open-rt-account">Privacy</a></li>
	      <li><a href="#" class="open-rt-account">Subscriptions</a></li>
	    
	    </ul>
	    <div class="memberful-account-content">
	    <div id="checkout-fields">
	     <div class="messages"></div>
	        <div class="memberful-account-signup-form">
	          <input name="finame" id="update-fname" placeholder="First" type="text" style="width:48%; float:left;margin-right:2%;" value="<?php echo $current_user->first_name;?>">
	          <input name="laname" id="update-lname" placeholder="Last" type="text" style="width:48%; float:right;margin-left:2%;" value="<?php echo $current_user->last_name;?>">
	          <input name="email" id="update-email" placeholder="Email" type="email" value="<?php echo $current_user->user_email;?>" >
	          <input name="password" id="update-password" placeholder="New password" type="password" autocomplete="new-password" />
	     </div>
	      
	      <div class="memberful-pay">
	        <button id="update-profile">
	          <span class="default-message purchasing">
	            Update my profile
	            </span>
	        </button>
	      </div>
	    </div>
	    </div>
	    <div class="prof-links">
	    <ul>
	      <li><a href="#" class="update-card-link">Update card</a><span>|</span></li>
	      <li><a href='<?php echo wp_logout_url("/bye"); ?>'>Sign out</a></li>
	    </ul>
	    </div>
	    </div>
	</div>

	<div id="rushtix-account" class="memberful-box mfp-hide">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	  <h1><?php echo $current_user->display_name; ?></h1>
	  </div>
	  <div class="checkout">
	  <ul class="memberful-account-nav" >
	      <li><a href="#" class="open-rt-profile">Profile</a></li>
	    <li><a href="<?php echo bp_core_get_user_domain($current_user->ID); ?>profile/edit" class="privacy open-rt-account" >Privacy</a></li>
	      <li><a href="#" class="active open-rt-account">Subscription</a></li>
	    </ul>
	    <div class="memberful-account-content">
	    <div class="memberful-item subscription">
	        
	 <?php 
	     $cus_inv=get_stripe_customer_next_invoice(); 
	                          
	    //echo '<pre>';   style="max-width:1000px"
	  //print_r($cus_inv->discount);
	  if(!empty($cus_inv)){
	   $nxt_amount=number_format($cus_inv->amount_due/100,2);
	     $due_date= date("n/j/y",$cus_inv->date); 
	      $plan=reset($cus_inv->lines->data); 
	      $plan_name=$plan->plan->name;
	    
	  echo '<div class="single-subscrip">
	  <h1>'.$plan_name.' - <span>Active</span></h1>
	  <p>Next charge is $'.$nxt_amount.' on '.$due_date.'</p>';
	     if(isset($cus_inv->discount->coupon)){
	      echo '<p>'.$cus_inv->discount->coupon->percent_off.'% off for '.$cus_inv->discount->coupon->duration_in_months.' months</p>';
	    }
	 // echo '<a href="#" class="cancel-subscription-n" data-cusid="'.$cur_plan->customer.'" data-subid="'.$cur_plan->id.'" data-plan="'.$cur_plan->plan->name.'">Cancel subscription</a>';
	  echo '</div>';
	   
	  } else echo '<p>No subscriptions. <a href="/invitation"  class="striplan"  data-plan="explorer">Join Now</a></p>';
	        ?>
	      <p>
	        <a href="https://rushtix.com/cancellation-request/" target="_blank" style="color: #777;text-decoration: underline;">
	          Cancel My Account
	        </a></p>
	      </div>
	    
	    </div>
	    </div>
	</div>
	<?php }?>  

	<div id="plan-exists" class="memberful-box mfp-hide">
	  <div class="header">
	    <div class="image"><img src="<?php echo get_option('popup_icon_url');?>" width="100" height="100"></div>
	    <h1>Plan already activated</h1>
	  </div>
	  <div class="checkout">
	   <div class="memberful-pay">
	      <p class="password-reset">Sorry you are already on this plan. You can manage your account
	     <a href="#" class="open-rt-account">here</a>
	    </p>
	        </div> 
	      
	      </div>
	</div>
</div><!--style="display:none;"-->
<?php
}