Stripe.setPublishableKey(params.publishable_key);
jq=jQuery;
original_href=window.location.href;
buy_btn_msg="";chosen_plan=""; plan_title="";plan_trial="";
plan_usergroup=""; plan_presentergroup="";amount="";
discounted_amount="";plan_price="";
rt_group_name=params.rt_group_name;
rt_level="";rt_partner="";
logged_in=params.logged_in=="yes"?true:false;

jq(function(){ 
	//console.log(params);return;
	jq("body").on("click","a.stripe-plan",function(){
		chosen_plan=jq(this).data("plan");
		plan_title=jq(this).data("plantitle");
		plan_trial=jq(this).data("trial");
		plan_usergroup=jq(this).data("usergroup");
 		plan_presentergroup=jq(this).data("partnergroup");
		plan_price=jq(this).data("price");
	    couponfield=jq(this).data("couponfield");
	    coupon=jq(this).data("coupon");
	    amount=jq(this).data("amount");
	    discounted_amount=jq(this).data("discountedamount");
	    btntxt=jq(this).data("btntxt");
			if(rt_group_name==plan_usergroup){
				jq.magnificPopup.open({items: {src: '#plan-exists',type: 'inline'}});
					return false;
			}
		jq("#stripe-signup .header h1").text(plan_title);
	    var per_month=" per month";
	    if(jq(this).data("permonth")=="no") per_month="";
			var display_title=plan_price+per_month;
	    jq("#stripe-checkout span").text("Order Now");
	    if(chosen_plan=="culturepass") jq("#stripe-checkout span").text("Verify  Now");
	    if(btntxt!="") jq("#stripe-checkout span").text(btntxt);
			if(plan_trial=="yes"){
				display_title="14 day free trial, then "+display_title;
	      jq("#stripe-checkout span").text("Start My Free Trial");
	       if(typeof mixpanel != 'undefined') mixpanel.track('Stripe Subscription'); 
	    }
	    if(coupon){
	      jq("#cc-coupon").val(coupon).attr("readonly","readonly");
	    }
	    if(couponfield=="open") jq("#cc-coupon").show();
			jq("#stripe-signup .header h2").text(display_title);
			jq.magnificPopup.open({items: {src: '#stripe-signup',type: 'inline'}});
	    if(jq(this).parent().hasClass('pre-booking-msg'))
	      if(typeof mixpanel != 'undefined') mixpanel.track('CTA "START MY FREE TRIAL" ');
			return false;
	});

	jq("body").on("click","a.verify-account",function(){
	    jq.magnificPopup.open({items: {src: '#verify-account-phone',
	                                   type: 'inline'}});
	    return false;  
	});

	jq("#stripe-checkout").click(function(){
			var fn=jq("#first_name").val()+" "+jq("#last_name").val();
			var eml=jq("#email").val();
			var pw=jq("#password").val();
			var card=jq('#cc-number').val();
			var exp=jq("#cc-expires").val();
			var cvc=jq('#cc-cvc').val();
			var cpn=jq('#cc-coupon').val();
	    	//console.log(fn+"-"+cpn);
	    	//return false;
			if(!logged_in){
				if(fn.length<3){
					jq("#full_name").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
					alert("Please Enter Name!");
					return false;
				}
				if(eml.length<3 || !validateEmail(eml)){ 
					jq("#email").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
					alert("Please Enter Correct Email Address!");
					return false;
				}
				if(pw.length<5){ 
					jq("#password").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
					alert("Password length should be minimum 5 characters!");
					return false;
				}
			} 
	   		/*if(!jq("#tos").is(":checked")){
				jq('#tos').css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
				alert("Please agree to the Terms of Service.");
				return false;
			}*/
			if(Stripe.card.validateCardNumber(card)==false){
				jq('#cc-number').css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
				alert("Card Number is Invalid!");
				return false;
			}
			if(Stripe.card.validateExpiry(exp)==false){
				jq('#cc-expires').css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
				alert("Please Enter date in Month/Year format!");
				return false;
			}
			if(Stripe.card.validateCVC(cvc)==false){
				jq('#cc-cvc').css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
				alert("Please Enter 3 or 4 digits cvc!");
				return false;
			}
			buy_btn_msg=jq(this).find(".default-message").text();
			jq(this).find(".default-message").text("Processing..");
			Stripe.card.createToken({
			 number:card , 
			 cvc: cvc,
			 exp: exp,
			 name:fn
			}, function(status, response){
	 		if(response.error){
	 			alert(response.error.message);
	 			jq("#stripe-checkout .default-message").text(buy_btn_msg);
	 		}else{
	 			jq.post(params.ajax_url,{
	 				action:"rt_stripe_payment",tkn:response,plan:chosen_plan,
	 					usergroup:plan_usergroup,
	          presentergroup:plan_presentergroup,
	          amount:amount,
	        discounted_amount:discounted_amount,
	 					email:eml,name:fn,
	            fname:jq("#first_name").val(),lname:jq("#last_name").val(),
	 					password:pw,coupon:cpn},
			 	function(ret){
			 		console.log(ret); 
	        jq.post('https://hooks.zapier.com/hooks/catch/160430/4z2wp7/',{action:"rushtix_membership",user_email:jq("#email").val(), firstname:jq("#first_name").val(), lastname:jq("#last_name").val(), promocode:67890,role:'customer'});
	        
	        if(typeof mixpanel != 'undefined') mixpanel.track('Credit Card Sign-up',{ "first_name":jq("#first_name").val(), "last_name":jq("#last_name").val() ,"email":eml,"plan":chosen_plan,"user_group":plan_usergroup , "presentergroup" : plan_presentergroup }); 

	        //fbq('track', 'Purchase', {value: '39.00', currency: 'USD'});
	       // mixpanel.people.track_charge(29.99);
			 		if(ret=="email_exists"){
			 			jq("#stripe-signup .checkout .messages").html("Email address already exists. Please Login ");
			 			jq("#stripe-checkout .default-message").text(buy_btn_msg);	
			 		} 
			 		else if(ret=="user_successfully_created"){
			 			
			 		 if(logged_in){ 
			 			jq("#stripe-signup .checkout")
				 		.css("color","#555555").css("text-align","center").css("padding","30px 15px")
				 		.html('Membership created successfully. <br>Let\'s get booking... ');
			 			window.location=params.home_url;
	          /*welcome*/
			 			 }else{ 
			 			jq("#stripe-signup .checkout")
				 		.css("color","#555555").css("text-align","center").css("padding","30px 15px")
				 		.html('Membership created successfully. <br>Redirecting to your events feed..');
				 		window.location=params.home_url;
	          /*welcome*/
				 		 } 
			 		} else{
			 		jq("#stripe-signup .checkout .messages").html(ret);	
			 		jq("#stripe-checkout .default-message").text(buy_btn_msg);
			 		}
			 		 
			 	});	 
	 		}
			}); 
		});
	
	jq("body").on("click",".header_login,.header_login a , .bp-login-nav a , #activate-page p a",function(){
		jq.magnificPopup.open({
       items: {
        src: '#rt-login',
        type: 'inline'
       }
		});
		return false;
	});
	
	jq("#rt-sign-in").click(function(e){
		e.preventDefault();
		var ths=jq(this);
		ths.attr("disabled","disabled");
		ths.text("Signing in...");
		jq("#rt-login .checkout .messages").html('');
		jq.post(params.ajax_url,{action:"rt_sign_in",user:jq("#login-user").val(),pass:jq("#login-pass").val()},
		 	function(ret){
      	//console.log(ret);
		 	if(ret=="login_success"){
        	if(typeof mixpanel != 'undefined') mixpanel.track("Logged In");
		    jq("#rt-login .checkout .messages").html('');
			 		jq("#rt-login .checkout .messages")
			 		.css("color","green")
			 		.html("Let's see what's new!");
        
		if (original_href.indexOf("event") > -1 || 
		original_href.indexOf("reactivate") > -1 ||
		original_href.indexOf("contest-winner") > -1 ||
		original_href.indexOf("invite-anyone") > -1) redirect=original_href;
		    else redirect=params.home_url;
			 		window.location=redirect;
		 		}else if(ret=="login_success_cont"){ 
          //Contributor Role is logged in ///my-dashboard/
			 		jq("#rt-login .checkout .messages")
			 		.css("color","green")
			 		.html("Let's see what's new!");
			 		window.location=params.home_url;
		 		}else{
			 		ths.removeAttr("disabled").text("Sign me in!");
			 		jq("#rt-login .checkout .messages").html(ret);
		 		}
		 	});	 
	});
	
	jq("#rt-reset-instr").click(function(e){
		e.preventDefault();
		var ths=jq(this);
		ths.attr("disabled","disabled");
		ths.text("Sending Email...");
		jq("#rt-lost-password .checkout .messages").html('');
		jq.post(params.ajax_url,{
			action:"rt_send_pass_reset",
			email:jq("#login-eml").val()},
		 	function(ret){
		 		if(ret=="no_email"){ 
		 			ths.removeAttr("disabled").text("Reset my password");
			 		jq("#rt-lost-password .checkout .messages").html("Email does not exists!");
		 			}else{
		 				console.log(ret);
			 		/*jq("#rt-lost-password .checkout .messages")
			 		.css("color","green")
			 		.html("Please check your email for password reset link!");*/
			 		jq("#rt-lost-password .checkout .messages").html('');
			 			ths.removeAttr("disabled").text("Send me reset instructions");
			 		jq("#login-eml").val('');
			 		jq(".header_login").click();
		 		}
		 	});	 
	});
	
	jq("body").on("click",".reset-pass-link",function(){
		jq.magnificPopup.open({
		 items: {
		 src: '#rt-lost-password',
		 type: 'inline'
		 }
		});
		return false;
	});

	jq("body").on("click",".rt-free-signup",function(){
	    if(logged_in){
	    alert("Please log out to create a new account.");
	     }else{ 
	 	title=jq(this).data("title")?jq(this).data("title"):'RushTix Guest Pass';
	 	subtitle=jq(this).data("subtitle")?jq(this).data("subtitle"):'Sign-up for free tickets.';
	    btntxt=jq(this).data("btntxt")?jq(this).data("btntxt"):'Create a Free Account';
	    rt_level=jq(this).data("level")?jq(this).data("level"):'';
	    rt_partner=jq(this).data("partner")?jq(this).data("partner"):'';
	    jq("#guest-signup .header").find("h1").text(title);
	    jq("#guest-signup .header").find("h2").text(subtitle);
	    jq("#guest-checkout span").text(btntxt);
	    
   
		jq.magnificPopup.open({items: {src: '#guest-signup', type: 'inline' }}); 
    	if(typeof mixpanel != 'undefined') mixpanel.track("Guest Pass Sign-up");
     } 
		return false;
	});

	jq("body").on("click",".social-signup,.social-signup a",function(){
		if(logged_in){
	    alert("Please log out to create a new account.");
	     }else{
	    jq("#guest-signup .header").find("h1").text('RushTix Guest Pass');
	    jq("#guest-grouptoadd").val("40");
	    
			jq.magnificPopup.open({items: {src: '#guest-signup', type: 'inline' }});
	    if(typeof mixpanel != 'undefined') mixpanel.track("Viewed Registration Modal");
	     } 
		return false;
	});
  
  	jq("#guest-checkout").click(function(){
		var fn=jq("#guest-fname").val();
    	var ln=jq("#guest-lname").val();
		var eml=jq("#guest-email").val();
		var pw=jq("#guest-password").val();
    	var group_id=jq("#guest-grouptoadd").val();
		if(fn.length<3){
			jq("#guest-name").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Please Enter Name!");
			return false;
		}
		if(eml.length<3 || !validateEmail(eml)){ 
			jq("#guest-email").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Please Enter Correct Email Address!");
			return false;
		}
		if(pw.length<5){ 
			jq("#guest-password").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Password length should be minimum 5 characters!");
			return false;
		}
			var ths=jq(this);
		ths.attr("disabled","disabled");
		ths.text("Registering Account...");
		jq("#guest-signup .checkout .messages").html('');
		jq.post(params.ajax_url,{
	      action:"rt_sign_up_user", fname:fn,lname:ln,  
	      pass:jq("#guest-password").val(), 
	      email:eml, group_id:group_id,
	      partner:rt_partner,
	      level:rt_level
	    }, 
		 	function(ret){
     // alert(ret); return false;
      
       if(typeof mixpanel != 'undefined') mixpanel.track('CTA "Create a Free Account"',{"first_name":fn, "last_name":ln,"email":eml}); 
      jq.post('https://hooks.zapier.com/hooks/catch/160430/468wfc/',{action:"rushtix_free_signup",user_email:jq("#guest-email").val(), firstname:jq("#guest-fname").val(), lastname:jq("#guest-lname").val(), promocode:12345,role:'Subscriber'});
		 			jq("#guest-signup .checkout .messages").html(ret);
		 		if(ret=="registered_successfully"){ 
             
			 		jq("#guest-signup .checkout")
			 		.css("color","#555555").css("text-align","center").css("padding","30px 15px")
			 		.html('Registered successfully. <br>Redirecting..');     
          
          
        if (original_href.indexOf("event") > -1) redirect=original_href;
        else redirect=params.home_url;
          /*welcome*/
        window.location=redirect;
			 		
		 		} else { 
			 		ths.removeAttr("disabled").text("Register my account");
			 		jq("#guest-signup .checkout .messages").html(ret);
		 		}
		 	});	 
	});
  
	jq("#update-profile").click(function(){
		var fn=jq("#update-fname").val();
    	var ln=jq("#update-lname").val();
		var eml=jq("#update-email").val();
		var pw=jq("#update-password").val();
		if(fn.length<3){
			jq("#update-fname").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Please Enter Name!");
			return false;
		}
		if(eml.length<3 || !validateEmail(eml)){ 
			jq("#update-email").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Please Enter Email Address!");
			return false;
		}
		if(pw.length && pw.length<5){ 
			jq("#update-password").css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Password length should be minimum 5 characters!");
			return false;
		}
		var ths=jq(this);
		ths.text("Updating Account...");
		jq("#rushtix-profile .checkout .messages").html('');
		jq.post(params.ajax_url,{action:"rt_update_user_profile",fname:fn,lname:ln,pass:jq("#update-password").val(),email:jq("#update-email").val()},
		 	function(ret){
      jq(".main-headline.mnh").text(fn+" "+ln);
		 		ths.text("Update my profile");
		 		if(ret=="profile_updated"){ 
			 		jq("#rushtix-profile").find(".messages")
			 		.css("color","green").css("text-align","center")
			 		.html('Profile updated successfully.');
		 		}else{
			 		ths.removeAttr("disabled").text("Sign me in!");
			 		jq("#rushtix-profile").find(".messages").html(ret);
		 		}
		 	});	 
	});
	
	jq(".open-rt-profile").click(function(){
		jq.magnificPopup.open({
		 items: {
		 src: '#rushtix-profile',
		 type: 'inline'
		 }
		});
		return false;
	});
	
	jq(".open-rt-account, .open-rt-account-menu>a").click(function(){
   	 if(jq(this).hasClass('privacy')) return true;
   	 else{
		jq.magnificPopup.open({
		 items: {
		 src: '#rushtix-account',
		 type: 'inline'
		 }
		});
		return false;
    	}
	});
	
	jq("body").on("keyup","#cc-number, #ucc-number",function(e){
		if (e.keyCode == '8' || e.keyCode == '37' || e.keyCode == '38' ||e.keyCode == '39' ||e.keyCode == '40') return;
		var val=this.value;
		//nos_only=val.replace(/[^0-9]/, "");
		nos_only=val.replace(/[^\d]+/g, "");
		if(!nos_only){
			jq(this).val("");
			return;
		}
		//console.log(nos_only);
		nos_arr=nos_only.match(/.{1,4}/g);
		res="";
		if(nos_arr.length) res=nos_arr.join(" ");
		len=res.length;
		if(len==4 || len==9 ||len==14) res+=" ";
		
		if(res.length>19) res=res.substr(0,19);
		jq(this).val(res);	
	});

	jq("body").on("keyup","#cc-expires ,#ucc-expires",function(e){
		if (e.keyCode == '8' || e.keyCode == '37' || e.keyCode == '38' ||e.keyCode == '39' ||e.keyCode == '40') return;
		val=this.value;
		nos_only=val.replace(/[^0-9\s/]/, "");
		len=nos_only.length;
		if(len==1 && nos_only>1) nos_only="0"+nos_only;
		if(len==2 && nos_only>12) nos_only="12";
		if(nos_only.length==2) nos_only+=" / ";
		jq(this).val(nos_only);
	});

	jq(".cancel-subscription-n").click(function(e){
		var ths=jq(this);
		var thsp=ths.parent();
		conf=confirm("Are you sure you want to cancel this subscription?");
		if(conf){
			thsp.html('Cancelling subscription...');
				jq.post(params.ajax_url,{action:"rt_stripe_cancel_plan",cus:ths.data('cusid'),plan:ths.data('plan'), subid:ths.data('subid')},
		 	function(ret){
		 		//jq(".memberful-item.subscription").html(ret);
		 		thsp.html('Subscription cancelled!').delay(1000).fadeOut();
		 		//console.log(ret);return; 
		 	});
		}
	 return false;
	});
	
	jq(".update-card-link").click(function(){
		jq.magnificPopup.open({items: {src: '#stripe-update-card',type: 'inline'
		 }});
		return false;
	});
	
	jq("#card-update-btn").click(function(){
		var card=jq('#ucc-number').val();
		var exp=jq("#ucc-expires").val();
		var cvc=jq('#ucc-cvc').val();	
		if(Stripe.card.validateCardNumber(card)==false){
			jq('#ucc-number').css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Card Number is Invalid!");
			return false;
		}
		if(Stripe.card.validateExpiry(exp)==false){
			jq('#ucc-expires').css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Please Enter date in Month/Year format!");
			return false;
		}
		if(Stripe.card.validateCVC(cvc)==false){
			jq('#ucc-cvc').css("border","1px solid red").css("box-shadow","0 0 6px red").focus();
			alert("Please Enter 3 or 4 digits cvc!");
			return false;
		}
		buy_btn_msg=jq(this).find(".default-message").text();
		jq(this).find(".default-message").text("Processing..");
		Stripe.card.createToken({
		 number:card , 
		 cvc: cvc,
		 exp: exp,
		 name: params.current_user_name
		}, function(status, response){
		
 		if(response.error){
 			alert(response.error.message);
 		}else{
 			jq.post(params.ajax_url,{action:"rt_stripe_update_card",tkn:response},
		 	function(ret){
		 		//console.log(ret);return; 
		 		if(ret=="card_updated"){
		 			jq("#stripe-update-card .checkout .messages")
			 		.css("color","#555555").css("text-align","center").css("padding","30px 15px")
			 		.html('Card updated successfully. <br>Let\'s get booking... ');
		 			//window.location=params.home_url;
		 		} else{
		 			jq("#stripe-update-card .checkout")
			 		.css("color","red").css("text-align","center").css("padding","30px 15px")
			 		.html('There was a problem in updating card!');	
		 		}
		 		 
		 	});	 
 		}
		});
    	return false;
	});
  	jq("#ccard_exp").ke
  	jq("#phone-update-btn").click(function(){
		var mobile=jq('#mobile-phone-number').val();
	    var isvalidphone = /^(1\s|1|)?((\(\d{3}\))|\d{3})(\-|\s)?(\d{3})(\-|\s)?(\d{4})$/.test(mobile);
		  if(!isvalidphone){
		    alert("Please Enter Valid Mobile Number");
		  }else{
			jq(this).find(".default-message").text("Updating..");
		
	 			jq.post(params.ajax_url,{action:"rt_update_mobile", mobile:mobile},
			 	function(ret){ 
	        if(ret=="updated"){
			 			jq("#verify-account-phone .checkout")
				 		.css("color","#555555").css("text-align","center").css("padding","30px 15px")
				 		.html('Mobile updated successfully. <br>Let\'s get booking... ');
			 			window.location=params.home_url;
			 		} 
			 		 console.log(ret);
			 	});	 
	  		}
	    return false;
	});
  
	jq(".strp-cpn-tgl").click(function(){
		jq("#cc-coupon").fadeToggle();
		return false;
	});

});//document ready end

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}