<?php 
//Note: For Groups_Group class please install Groups Plugin https://wordpress.org/plugins/groups/
function rt_get_stripe_secret_key(){
	$stripe_mode=get_option('stripe_mode');
	if($stripe_mode=="live")
		return get_option('rt_live_secret_key'); 
	else
		return get_option('rt_test_secret_key');	
}
function rt_get_stripe_pub_key(){
	$stripe_mode=get_option('stripe_mode');
	if($stripe_mode=="live")
		return get_option('rt_live_publisher_key'); 
	else
		return get_option('rt_test_publisher_key');	
}

add_action("wp_ajax_nopriv_rt_stripe_payment","rt_loggedout_stripe_payment");

function rt_loggedout_stripe_payment(){
	global $current_user,$wpdb;
	extract($_POST);
	//print_r($_POST);exit; 
	$existing_user = email_exists($email);
	if($existing_user) die("email_exists");
  
  if(!class_exists("Stripe")) require( 'stripe/Stripe.php' );
	Stripe::setApiKey(rt_get_stripe_secret_key());
  // Get the credit card details submitted by the form
	$token = $tkn['id'];
	$new_customer=array(
	  "source" => $token,
	  /*"plan" => $plan,*/
	  "email" => $email,
	  "description" => "RushTix Customer: ".$name
	  );
  
  if($plan=="no"){
    if(!empty($coupon)){
      if($usergroup=="Explorer") $ctyp="exp";
      elseif($usergroup=="Premium") $ctyp="prm";
     $cpn_chk=$wpdb->get_row("Select * from {$wpdb->prefix}gilt_codes where code='{$coupon}' and type='{$ctyp}'");
       if(empty($cpn_chk)) die("Coupon code is invalid!");
    elseif(!empty($cpn_chk->used_on)) die("Coupon code already used by ".$cpn_chk->used_by." on ".date("m/d/y",$cpn_chk->used_on));
    }
 $customer = Stripe::create_customer($new_customer);
     Charge::create_charge(array(
        "amount" => $cpn_chk->code?$discounted_amount*100:$amount*100,
        "currency" => "usd",
        "customer" => $customer->id,
        "metadata" => array("giltCoupon" => $cpn_chk->code?$cpn_chk->code:'')));
  }
  else{
	$new_customer['plan']=$plan;
		if($coupon!='' && strlen($coupon) > 0) {
          try {
                $coupon_chk = Coupon::get_coupon($coupon); //check coupon exists
                if($coupon_chk !== NULL) {
                 $coupon_usable = true; //set to true our coupon exists or take the coupon id if you wanted to.
                }

             } catch (Exception $e) {
                // an exception was caught, so the code is invalid
                $cp_err_msg = $e->getMessage();
             }

        }
    
    if(isset($cp_err_msg)) die($cp_err_msg);
  
    if(isset($coupon_usable)) $new_customer['coupon']=$coupon;
	$customer = Customer::create($new_customer);
  
  }
  
	$groupp = null;
	if ( $group = Groups_Group::read_by_name($usergroup) ) {
	    $groupp = $group->group_id;
	}
  
  $group_pres=null;
  if ( $group1 = Groups_Group::read_by_name($presentergroup) ) {
	    $group_pres = $group1->group_id;
	}
	
	$detals=array("customer_id"=>$customer->id,
	"time"=>$customer->created,
	"email"=>$customer->email,
	"default_card"=>$customer->default_card
	);
	
	$user_name=str_replace(" ","_",$name);
	$user_id = username_exists( $user_name );
	if($user_id){
		$user_name=explode("@",$detals['email']);
		$user_name=str_replace(".","_",$user_name[0]);
		$user_name=str_replace("+","_",$user_name);
	} 
	 $userdata=array(
	    'user_login'  =>  $user_name,
	    'user_pass'   =>  $password ,
	     'first_name'=>$fname, 
      'last_name'=>$lname,
	    'display_name'=>$name,
	    'user_email'=>$detals['email'],
	    'role'=>"customer"
	);
	$new_user_id = wp_insert_user($userdata);
	if($new_user_id){
		update_user_meta($new_user_id,"stripe_plans",$detals);
		if($groupp)
		Groups_User_Group::create( array( 'user_id' => $new_user_id, 'group_id' => $groupp ) );
     if($group_pres)		
     	Groups_User_Group::create( array( 'user_id' => $new_user_id, 'group_id' => $group_pres ) ); 
    
    if(isset($cpn_chk->code)){
      $wpdb->update($wpdb->prefix.'gilt_codes', 
	array( 
		'used_by' => $detals['email'],	
		'used_on' => time()
	), array( 'id' => $cpn_chk->id ), 
	array( '%s','%s'), array( '%d' ) ); 
    update_user_meta($new_user_id,"voucher_code",$cpn_chk->code);  
    }
		$creds = array();
		$creds['user_login'] = $user_name;
		$creds['user_password'] =  $password;
		$creds['remember'] = true;
		$user_loggedin = wp_signon( $creds, true );
		wp_set_current_user($user_loggedin->ID);

		die("user_successfully_created");
	}	
	die();
}

add_action("wp_ajax_rt_stripe_payment","rt_stripe_payment");
function rt_stripe_payment(){
	global $current_user;
	extract($_POST);
	if(!class_exists("Stripe")) require( 'stripe/Stripe.php' );
	Stripe::setApiKey(rt_get_stripe_secret_key());
	$token = $tkn['id'];
	if($coupon!='' && strlen($coupon) > 0) {
          try {
                $coupon_chk = Coupon::retrieve($coupon); //check coupon exists
                if($coupon_chk !== NULL) {
                 $coupon_usable = true; //set to true our coupon exists or take the coupon id if you wanted to.
                }

             } catch (Exception $e) {
                // an exception was caught, so the code is invalid
                $cp_err_msg = $e->getMessage();
             }

        }
        if(isset($cp_err_msg)) die($cp_err_msg);
	$cid=rt_stripe_customer_key();
	if(!empty($cid)){
     //if($plan=="culturepass") die("You already have a subscription.");
		try {
		$customer = Customer::retrieve($cid);
    $customer->source = $token; 
		$customer->save(); 
		$exis_customer=array("plan" => $plan);
		if(isset($coupon_usable)) $exis_customer['coupon']=$coupon;
    if($plan!="culturepass") 
		$customer->subscriptions->create($exis_customer); 
    
		} catch (Exception $e) {}
	
  }else{
		
	$new_customer=array(
	  "source" => $token,
	  "plan" => $plan,
	  "email" => $email,
	  "description" => "RushTix Customer: ".$name
	  );
    if($plan=="culturepass") unset($new_customer['plan']);
	if(isset($coupon_usable)) $new_customer['coupon']=$coupon;
	
	$customer = Customer::create($new_customer);	
	$detals=array("customer_id"=>$customer->id,
		"time"=>$customer->created,
		"email"=>$customer->email,
		"default_card"=>$customer->default_card
		);
		update_user_meta($current_user->ID,"stripe_plans",$detals);
	}
	$group_pres=null;
  if ( $group1 = Groups_Group::read_by_name($presentergroup) ) {
	    $group_pres = $group1->group_id;
  }
  
	$groupp = null;
	if ( $group = Groups_Group::read_by_name($usergroup) ) {
	    $groupp = $group->group_id;
	}
	if($groupp){
	$groups_user = new Groups_User($current_user->ID);
		$groups = $groups_user->groups;
		if(is_array($groups)){
		foreach( $groups as $group ) {
			Groups_User_Group::delete($current_user->ID, $group->group_id);
			}}
	Groups_User_Group::create( array( 'user_id' => $current_user->ID, 'group_id' => $groupp ) );
	}
   if($group_pres)
     	Groups_User_Group::create( array( 'user_id' => $current_user->ID, 'group_id' => $group_pres ) ); 
	die("user_successfully_created");
}

add_action("wp_ajax_rt_stripe_update_card","rt_stripe_update_card");
function rt_stripe_update_card(){
	global $current_user;
	extract($_POST);
	if(!class_exists("Stripe")) require( 'stripe/Stripe.php' );
	Stripe::setApiKey(rt_get_stripe_secret_key());
	$token = $tkn['id'];
	
	$cid=rt_stripe_customer_key();
	if(!empty($cid)){
		try{
		$customer = Customer::retrieve($cid);
		$customer->source = $token; 
		$customer->save();
		} catch (Exception $e) {}
		die("card_updated");
	}else die("error");	
	die();
}

add_shortcode("rushtix_my_account","rushtix_my_account");
function rushtix_my_account(){
	global $current_user;

	$cid=rt_stripe_customer_key();
	if(!empty($cid)){
	require( 'stripe/Stripe.php' );
	Stripe::setApiKey(rt_get_stripe_secret_key());
	try{
	$customer = Customer::retrieve($cid);
	$plan=$customer->subscriptions->data;
	$cur_plan=$plan[0]->plan;
	} catch (Exception $e) {}
	return '<table id="subscriptions-details">
		<tr>
		<th>Name</th><td>'.$cur_plan->name.'</td>
		</tr><tr>
		<th>Amount</th><td>$'.($cur_plan->amount/100).'</td>
		</tr><tr>
		<th>Started on</th>
		<td>'.date("m/d/Y",$cur_plan->created).'</td>

		</tr>
	</table>';
	}
}

function get_custom_stripe_account($user_id=false){
	global $current_user;
  
  if($user_id) $cid=rt_stripe_customer_key($user_id);
  else $cid=rt_stripe_customer_key();
  
	if(!empty($cid)){
		if(!class_exists("Stripe")) require( 'stripe/Stripe.php' );
		
		Stripe::setApiKey(rt_get_stripe_secret_key());
		try{
		$customer = Customer::retrieve($cid);
		$plan=$customer->subscriptions->data;
		} catch (Exception $e) {
	        // an exception was caught, so the code is invalid
	        $cp_err_msg = $e->getMessage();
	        $plan=array();
        }
		return $plan;
		} 
		else return '';
}

function get_stripe_customer_coupon($user_id=false){
	global $current_user;
  
  if($user_id) $cid=rt_stripe_customer_key($user_id);
  else $cid=rt_stripe_customer_key();
  
	if(!empty($cid)){
		if(!class_exists("Stripe")) require( 'stripe/Stripe.php' );
		
		Stripe::setApiKey(rt_get_stripe_secret_key());
		try{
		$customer = Customer::retrieve($cid);
		} catch (Exception $e) {
	        // an exception was caught, so the code is invalid
	        $cp_err_msg = $e->getMessage();
	        $plan=array();
        }
		return isset($customer)?$customer:'';
		} 
		else return '';
}

function get_stripe_customer_next_invoice($user_id=false){
	global $current_user;
  
  if($user_id) $cid=rt_stripe_customer_key($user_id);
  else $cid=rt_stripe_customer_key();
  
	if(!empty($cid)){
		if(!class_exists("Stripe")) require( 'stripe/Stripe.php' );
		
		Stripe::setApiKey(rt_get_stripe_secret_key());
		try{
		$customer_inv =Invoice::upcoming(array("customer" =>$cid));
      
		} catch (Exception $e) {
	        // an exception was caught, so the code is invalid
	        $cp_err_msg = $e->getMessage();
	        $plan=array();
        }
		return isset($customer_inv)?$customer_inv:'';
		} 
		else return '';
}

add_action("wp_ajax_nopriv_rt_send_pass_reset","rt_send_pass_reset");
function rt_send_pass_reset(){
	extract($_POST);
	//print_r($_POST);
	$get_user=get_user_by("email",$email);
	
	$email_chk=email_exists( $email);
	if(!$email_chk)  die("no_email");
	else {
		$ret_pass=rt_retrieve_password($email);
		if($ret_pass) die("email_sent");
		
	}
   
	die();
}


add_action("wp_ajax_nopriv_rt_sign_up_user","rt_sign_up_user");
function rt_sign_up_user(){
	extract($_POST);
  //print_r($_POST);die();
	if(email_exists( $email)) die("Email Atready Exists!");
   elseif(!is_email( $email)) die("Invalid Email Address!");
   else{
     $name=$fname." ".$lname;
   		$user_name=str_replace(" ","_",$name);
		$user_ch = username_exists( $user_name );
		if($user_ch){
			$user_name=explode("@",$email);
			$user_name=str_replace(".","_",$user_name[0]);
			$user_name=str_replace("+","_",$user_name);
		} 
   	$userdata=array(
		    'user_login'  =>  $user_name,
		    'user_pass'   =>  $pass ,
		    'first_name'=>$fname,
        'last_name'=>$lname,
		    'display_name'=>$name,
		    'user_nicename'=>$name,
		    'user_email'=>$email,
		    'role'=>"subscriber"
		);
	$new_user_id = wp_insert_user($userdata);
		if($new_user_id) {
		Groups_User_Group::create( array( 'user_id' => $new_user_id, 'group_id' => 1) );
      if(!empty($group_id)){
      Groups_User_Group::create( array( 'user_id' => $new_user_id, 'group_id' => $group_id) );
      }
      if(!empty($level)){
        if ( $lvl = Groups_Group::read_by_name($level) ) {
          Groups_User_Group::create( array( 'user_id' => $new_user_id, 'group_id' => $lvl->group_id) );
        }}
      if(!empty($partner)){
       if ( $ptnr = Groups_Group::read_by_name($partner) ) {
          Groups_User_Group::create( array( 'user_id' => $new_user_id, 'group_id' => $ptnr->group_id) );
        }
      }
       
		$creds = array();
		$creds['user_login'] = $user_name;
		$creds['user_password'] =  $pass;
		$creds['remember'] = true;
		$user_loggedin = wp_signon( $creds, true );
		wp_set_current_user($user_loggedin->ID);
		die("registered_successfully");
    }
   }
	die();
}
add_action("wp_ajax_rt_sign_in","rt_sign_in");
add_action("wp_ajax_nopriv_rt_sign_in","rt_sign_in");
function rt_sign_in(){
	extract($_POST);
	if(is_user_logged_in()) die("You're already logged in.");
	if(email_exists( $user)) {
		$user_eml = get_user_by( 'email', $user);
		 $creds = array(
	        'user_login'    => $user_eml->user_login,
	        'user_password' => $pass,
	        'rememember'    => true
	    );
	    $user_chk = wp_signon( $creds, true );
	    if ( is_wp_error($user_chk) ){
			$err_codes = $user_chk->get_error_codes();
			if ( in_array( 'incorrect_password', $err_codes ) ) {
				$error = 'The password you entered is incorrect.';
			}
		die($error);
		}
		else {
			wp_set_current_user($user_chk->ID);
      do_action("rushtix_user_logged_in",$user_chk);
			if(in_array("contributor",$user_chk->roles))
        die("login_success_cont");
			else die("login_success");
    }
		
   }elseif(username_exists( $user)) {
		 $creds = array(
	        'user_login'    => $user,
	        'user_password' => $pass,
	        'rememember'    => true
	    );
	    $user_chk = wp_signon( $creds, true );
	     if ( is_wp_error($user_chk) ){
			$err_codes = $user_chk->get_error_codes();
			if ( in_array( 'incorrect_password', $err_codes ) ) {
				$error = 'The password you entered is incorrect.';
			}
		die($error);
		}
		else {
			wp_set_current_user($user_chk->ID); 
      do_action("rushtix_user_logged_in",$user_chk);
			if(in_array("contributor",$user_chk->roles))
        die("login_success_cont");
			else die("login_success");
    }
   }
   else{
   	die("No Username or Email Exists!");
   }
	die();
}

add_action("wp_ajax_rt_update_user_profile","rt_update_user_profile");
function rt_update_user_profile(){
	global $current_user;
	extract($_POST);
	if(!is_email( $email)) die("Invalid Email Address!");
  $name=$fname." ".$lname;
	$data=array( 'ID' =>$current_user->ID , 
		'user_email' => $email,
		'first_name'=>$fname,
    'last_name'=>$lname,
    'user_nicename'=>$name,
    'display_name'=>$name
		);
	if(!empty($pass)) $data['user_pass']=$pass;
  update_user_meta($current_user->ID, 'display_name', $name);
	$user_id = wp_update_user($data);
  if(function_exists('xprofile_set_field_data'))  
    xprofile_set_field_data('field_1', $current_user->ID,  $name);
	if ( is_wp_error( $user_id ) ) die("Error in updating info!");

	$cid=rt_stripe_customer_key();
	if(!empty($cid)){
		if(!class_exists("Stripe")) require( 'stripe/Stripe.php' );
		Stripe::setApiKey(rt_get_stripe_secret_key());
		try{
			$customer = Customer::retrieve($cid);
			$customer->email = $email; 
			$customer->description = "RushTix Customer: ". $name;
			$customer->save();
			} catch (Exception $e) {}
	}
	die("profile_updated");
	die();
}

add_action("wp_ajax_rt_update_mobile","rt_update_mobile");
function rt_update_mobile(){
	global $current_user;
	extract($_POST);
   update_user_meta( $current_user->ID,'mobile_number', $mobile);
  Groups_User_Group::create( array( 'user_id' => $current_user->ID, 'group_id' => 9) );
  die("updated");
}

add_action("wp_ajax_rt_stripe_cancel_plan","rt_stripe_cancel_plan");
function rt_stripe_cancel_plan(){
	global $current_user;
	extract($_POST);
	//print_r($_POST);
	$groups_user = new Groups_User($current_user->ID);
		$groups = $groups_user->groups;
		if(is_array($groups)){
		foreach( $groups as $group ) {
			if($plan==$group->name)
			Groups_User_Group::delete($current_user->ID, $group->group_id);
			}}
	if(!class_exists("Stripe")) require( 'stripe/Stripe.php' );
	Stripe::setApiKey(rt_get_stripe_secret_key());
	try{
	$cu = Customer::retrieve($cus);
	$cu->subscriptions->retrieve($subid)->cancel();
	} catch (Exception $e) {}
	echo "Subscription Cancelled Successfully!";
	die();
}

function rt_retrieve_password($user_login) {
    global $wpdb, $current_site;

    if ( empty( $user_login) ) {
        return false;
    } else if ( strpos( $user_login, '@' ) ) {
        $user_data = get_user_by( 'email', trim( $user_login ) );
        if ( empty( $user_data ) )
           return false;
    } else {
        $login = trim($user_login);
        $user_data = get_user_by('login', $login);
    }

    do_action('lostpassword_post');


    if ( !$user_data ) return false;

    // redefining user_login ensures we return the right case in the email
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;

    do_action('rt_retreive_password', $user_login);  // Misspelled and deprecated
    do_action('rt_retrieve_password', $user_login);

    $allow = apply_filters('allow_password_reset', true, $user_data->ID);

    if ( ! $allow )
        return false;
    else if ( is_wp_error($allow) )
        return false;

    $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
    if ( empty($key) or true) {
        // Generate something random for a key...
        $key = wp_generate_password(20, false);
        do_action('rt_retrieve_password_key', $user_login, $key);
        // Now insert the key, hashed, into the DB.
		if ( empty( $wp_hasher ) ) {
		    require_once ABSPATH . WPINC . '/class-phpass.php';
		    $wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' .$wp_hasher->HashPassword( $key );

        // Now insert the new md5 key into the db
        $wpdb->update($wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user_login));
    }
    $message = __('<h3>Password Reset Instructions</h3>');
    $message .= "A password reset request has been made for:". "<br><br>";
    $message .= sprintf(__('Email address: %s'), $user_email) . "<br><br>";
    $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "<br><br>";
    $message .= __('To reset your password, give a clicky right here:') . "<br>";
    $message .= '<a style="color:#ec008b" href="' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . '">Click here to reset your password</a>'. "<br><br>";
    $message .= "Cheers,". "<br><br>";
    $message .= "Team RushTix". "<br><br>";

    

    $title = sprintf( __('Get Your Shiny New Password'), $blogname );

    $title = apply_filters('retrieve_password_title', $title);
    $message = apply_filters('retrieve_password_message', $message, $key);

    if ( $message && !wp_mail($user_email, $title, $message,"Content-type: text/html") )
        wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

    return true;
}


function rt_stripe_customer_key($user_id=0){ 
	global $current_user;
  
  $cus_id=$current_user->ID;
  if($user_id>0) $cus_id=$user_id;
  
  $ret_key="";
	$st_details=get_user_meta($cus_id,"stripe_plans",true);
	if(!empty($st_details) && !empty($st_details['customer_id'])) 
    return $st_details['customer_id'];
	else{
		$woo_stripe=get_user_meta($cus_id,"_stripe_customer_id",true);
		if(!empty($woo_stripe)){
			if(is_array($woo_stripe)) {
        if(isset($woo_stripe['customer_id']))  
          $ret_key=$woo_stripe['customer_id'];
      	}
			else $ret_key=$woo_stripe;
		}
	}
  return $ret_key;
} 

add_filter( 'send_email_change_email', '__return_false' );




add_filter( 'login_errors', function( $error ) {
	global $errors;
	$err_codes = $errors->get_error_codes();

	// Incorrect password.
	if ( in_array( 'incorrect_password', $err_codes ) ) {
		$error = 'The password you entered is incorrect.';
	}

	return $error;
} );

/*Stripe webhook start*/
add_action("wp_ajax_nopriv_rt_stripe_payment_hook","rt_stripe_payment_hook");
add_action("wp_ajax_rt_stripe_payment_hook","rt_stripe_payment_hook");
function rt_stripe_payment_hook(){

$body = @file_get_contents('php://input');
$event_json = json_decode($body);
  $customer=$event_json->data->object->customer;
$amount=$event_json->data->object->amount;
 if($amount) $amount=$amount/100;
  else $amount=0;
  require 'mixpanel-php/lib/Mixpanel.php';

// get the Mixpanel class instance, replace with your project token
$mp = Mixpanel::getInstance("8e580c1616630787833e81553a54478f");

// track an event
$mp->track("Stripe Payment", array("customer"=>$customer,"amount" => $amount));
$mp->people->trackCharge($customer,$amount);
  die("Payment Updated in MixPanel!");
}
/*Stripe webhook end*/

/*Presenter Plan and group 7-Apr-16 start*/

?>