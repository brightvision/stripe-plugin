<?php
add_action('admin_menu', 'rushtix_stripe_keys');

function rushtix_stripe_keys() {
	add_menu_page('RushTix Stripe', 'RushTix Stripe', 'manage_options', 'rt-stripe-page', 'rushtix_stripe_page' );
}

function rushtix_stripe_page(){ 
	if(isset($_POST['rt_live_publisher_key'])){
		foreach ($_POST as $key => $value) update_option($key,$value);
	}
	?>
	<style type="text/css">
		label{ font-size: 16px; width: 200px; display: block; float: left;}
		input[type=text]{ width: 400px;}
		.grp{ margin: 10px;}
	</style>
	<div class="wrap"><div id="icon-tools" class="icon32"></div>
		<h2>Update Stripe Details</h2>
		<form action="" method="post">
		<div class="grp" style="font-weight:700">
			<label>Choose Stripe Mode</label>
			<input type="radio" name="stripe_mode" value="live" <?php echo get_option('stripe_mode')=="live"?'checked':'';?>/>Live 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" name="stripe_mode" value="test" <?php echo get_option('stripe_mode')=="test"?'checked':'';?>/>Test
		</div>
		<hr>
		<div class="grp">
			<label>Live Publisher Key</label>
			<input type="text" name="rt_live_publisher_key" value="<?php echo get_option('rt_live_publisher_key');?>" />
		</div>
		<div class="grp">
			<label>Live Secret Key</label>
			<input type="text" name="rt_live_secret_key" value="<?php echo get_option('rt_live_secret_key');?>"/>
		</div>
		<hr>
		<div class="grp">
			<label>Test Publisher Key</label>
			<input type="text" name="rt_test_publisher_key" value="<?php echo get_option('rt_test_publisher_key');?>" />
		</div>
		<div class="grp">
			<label>Test Secret Key</label>
			<input type="text" name="rt_test_secret_key" value="<?php echo get_option('rt_test_secret_key');?>"/>
		</div>
		<hr>
		<div class="grp">
			<label>Popup Icon URL</label>
			<input type="text" name="popup_icon_url" value="<?php echo get_option('popup_icon_url');?>"/>
		</div>
		<button type="submit" class="button button-primary" style="margin:10px 0 0 160px;">Update</button>
		</form>
</div>
<?php }