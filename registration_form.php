<?php 
function registartion_form_shortcode()
	{ ?>
<div class="wrap">
 <?php  global $wpdb;	 

  if($_GET['user']=='exist')  { echo '<h5>Please try another Username,This Username is already exist.</h5>'; } 

	function redirect($url)
				{
				    if (!headers_sent())
				    {    
				        header('Location: '.$url);
				        exit;
				        }
				    else
				        {  
				        echo '<script type="text/javascript">';
				        echo 'window.location.href="'.$url.'";';
				        echo '</script>';
				        echo '<noscript>';
				        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
				        echo '</noscript>'; exit;
				    }
				}

	if($_GET['return']=='cancel'){ redirect($alwaysurl."?pra=cancel");	}


	if($_GET['return']=='true') // user successfully pay registratin charges.
	{
	 	
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		foreach ($_GET as $key => $value) {
			$value = urlencode(stripslashes($value));
			$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
			$req .= "&$key=$value";
		}

		if($_GET['st']=='Completed')
		{
			// assign posted variables to local variables
			$data['item_name']			= $_GET['item_name']; 
			//$data['item_number'] 		= $_GET['item_number']; 
			$data['payment_status'] 	= $_GET['st'];
			$data['payment_amount'] 	= $_GET['amt'];
			$data['payment_currency']	= $_GET['cc'];
			$data['txn_id']			    = $_GET['tx'];
			//$data['receiver_email'] 	= $_POST['receiver_email'];
			//$data['payer_email'] 		= $_POST['payer_email'];
			$tempuserid01 				= $_GET['cm'];

			// select value from wp_pro_temp_users table
			$selectdataquery = "SELECT * FROM ".PRO_TABLE_PREFIX."temp_users WHERE id='$tempuserid01'";
			$resulvalues = $wpdb->get_results($selectdataquery); 
			$tempuserid = $resulvalues[0]->id;	
			$now = date('Y-m-d H:i:s');		
				
			
			// insert values in wp_users table
			$wpdb->insert($wpdb->prefix.'users', 
				array(
				  'user_login'           => $resulvalues[0]->username,
				  'user_pass'    	     => $resulvalues[0]->password,
				  'user_nicename'        => $resulvalues[0]->username,
				  'user_email'           => $resulvalues[0]->email,
	    		  'user_url'             => '',
				  'user_registered'      => $now,
				  'user_activation_key'  => '',
				  'user_status'          => 0,
				  'display_name'         => $resulvalues[0]->username
					),
				array(
				  '%s',
				  '%s',
				  '%s',
				  '%s',
				  '%s',
				  '%s',
				  '%s',
				  '%s',
				  '%s'
					) 
			 	 ); 	
				
			$uid = $wpdb->get_col('select *  from '.$wpdb->prefix.'users where user_login = "'.$resulvalues[0]->username.'"'); 
			$uid = $uid[0];
				
			
			// insert value in wp_usermeta table
			$values = array();
			$values[0]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'first_name', $resulvalues[0]->firstname);
			$values[1]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'last_name', $resulvalues[0]->lastname);
			$values[2]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'nickname', $resulvalues[0]->username);
			$values[3]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'description', '');
			$values[4]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'rich_editing', 'true');
			$values[5]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'comment_shortcuts', 'false');
			$values[6]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'admin_color', 'fresh');
			$values[7]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'use_ssl', 0);
			$values[8]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'show_admin_bar_front', 'true');
			$values[9]  = $wpdb->prepare( "(%s, %s, %s)", $uid, 'wp_capabilities', 'a:1:{s:10:"subscriber";b:1;}');
			$values[10] = $wpdb->prepare( "(%s, %s, %s)", $uid, 'wp_user_level', 0);
			$values[11] = $wpdb->prepare( "(%s, %s, %s)", $uid, 'transaction_id', $data['txn_id']);
			$values[12] = $wpdb->prepare( "(%s, %s, %s)", $uid, 'payment', $data['payment_amount'].$data['payment_currency']);
			$values[13] = $wpdb->prepare( "(%s, %s, %s)", $uid, 'payment_date', $now);
			
			$wpdb->query( "INSERT INTO  ".$wpdb->prefix."usermeta (user_id, meta_key, meta_value) VALUES " . join( ',', $values ) ) ;
			
			//Delete entry from wp_pro_temp_users table.
			$wpdb->query("DELETE FROM ".PRO_TABLE_PREFIX."temp_users WHERE id = '$tempuserid01'");
			redirect($alwaysurl."?pra=true");			
		}

	}
?>




 <?php  if(isset($_POST["submit"]))
 	{ 

 		
 		echo '<div class="pra_loader_form"></div>';
 		echo '<h3 style="text-align: center;">Please Wait..</h3>';
 		echo '<h4 style="text-align: center;">Redirecting you to PayPal</h4>';
 		echo '<p style="text-align: center;">Please wait while we redirect you. Do not press back button or refresh.</p>';
		$user_login=$_POST['user_login'];
		$first_name=$_POST["first_name"];
	 	$last_name=$_POST["last_name"];
	  	$user_email=$_POST['payer_email'];
	 	$user_pass=md5($_POST["user_pass"]);
		
		
		/* Check user is exist or not */
		$user_exist = $wpdb->query('SELECT * FROM '.$wpdb->prefix.'users WHERE user_login="'.$user_login.'" or user_email="'.$user_email.'"'); 
		$user_exist01 = $wpdb->query('SELECT * FROM '.PRO_TABLE_PREFIX.'users WHERE  username="'.$user_login.'" or email="'.$user_email.'"'); 
		
		$alwaysurl = get_permalink();
		if (strpos($alwaysurl, '?') !== false)
		{
		    $connectvar = '&';
		}
		else
		{
		    $connectvar = '?';
		}
			
			
		
		// Check if  user is exist
		if($user_exist > 0) //if username exist in wp_user table
			{ ?>
				 <script type="text/javascript">
						window.location='<?php echo $alwaysurl.$connectvar."user=exist"; ?>';
			 	</script>
		<?php }
			else if($user_exist01 > 0) //if username exist in wp_pro_temp_users table
			{ ?>
					<script type="text/javascript">
						window.location='<?php echo $alwaysurl.$connectvar."user=exist"; ?>';
					 </script>
					
		 <?php }
			else
			{
	
	
					// Insert Data in temporary table
					$wpdb->insert(PRO_TABLE_PREFIX.'temp_users', 
					array(
					  'username'     => $user_login,
					  'firstname'    => $first_name,
					  'lastname'     => $last_name,
					  'email'        => $user_email,
					  'password'     => $user_pass
						),
					array(
					  '%s',
					  '%s',
					  '%s',
					  '%s',
					  '%s'
						) 
				 	 ); 
			
		

					$query = "SELECT * FROM ".PRO_TABLE_PREFIX."temp_users WHERE username='$user_login'";
					$resulvalues = $wpdb->get_results($query); 
					$tempuserid = $resulvalues[0]->id;	
					
					$paypalquery = "SELECT * FROM ".PRO_TABLE_PREFIX."registration_detail";
					$paypalvalues = $wpdb->get_results($paypalquery); 	
					$sandboxenable =$paypalvalues[1]->value;


					// PayPal settings
					$alwaysurl = get_permalink();
					if (strpos($alwaysurl, '?') !== false)
					{
					    $connectvar = '&';
					}
					else
					{
					    $connectvar = '?';
					}
			
					$paypal_email = $paypalvalues[2]->value;
					$return_url = $alwaysurl.$connectvar."return=true";
					$cancel_url = $alwaysurl.$connectvar."&return=cancel";
					$notify_url = $alwaysurl.$connectvar."&return=notify";    
					
					$item_name = 'Registration Charges';
					$item_amount = $paypalvalues[0]->value;
					
				// Check if paypal request or response
				if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"]))
				{
			
				// Firstly Append paypal account to querystring
				$querystring .= "?business=".urlencode($paypal_email)."&";	
				
				// Append amount& currency (£) to quersytring so it cannot be edited in html
				
				//The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
				$querystring .= "item_name=".urlencode($item_name)."&";
				$querystring .= "amount=".urlencode($item_amount)."&";
				
				//loop for posted values and append to querystring
				foreach($_POST as $key => $value){
					$value = urlencode(stripslashes($value));
					$querystring .= "$key=$value&";
				}
				
				$querystring.= "custom=$tempuserid&";
				// Append paypal return addresses
				$querystring .= "return=".urlencode(stripslashes($return_url))."&";
				$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
				$querystring .= "notify_url=".urlencode($notify_url)."&";
				
				// Append querystring with custom field
				//$querystring .= "&custom=".USERID;
				
				
				
				// Redirect to paypal IPN
				if($sandboxenable==1)
				{
					
					redirect('https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
				}
				else
				{
					redirect('https://www.paypal.com/cgi-bin/webscr'.$querystring);
				}
			
				
				exit();
			
			}else{
				
				echo '<h3>Someting went wrong, Please contact website admin.</h3>'; 
					
			}
	}
		
		
 }
 
 ?>
		 <script type="text/javascript">
		function form_validate()
		{
			var e = 0;
			var userchk = isCheckUsername("user_login", "Please Enter User Name", "status")

			if(userchk=='decline')
			{
				e++;
			}
			if(isEmpty("first_name", "Please Enter First Name", "err_first_name"))
			{
				e++;
			}
			if(isEmpty("last_name", "Please Enter Last Number", "err_last_name"))
			{
				e++;
			}
			if(emailcheck("payer_email", "Please Enter Correct Email Id", "err_payer_email"))
			{
				e++;
			}
			if(isEmpty("user_pass", "Please Enter Your Password", "err_user_pass"))
			{
				e++;
			}
			

		if(e > 0)
			{
				//alert("Please fill login details");
				return false;
			}
			else
			{
				return true;
			}

		}

		function isCheckUsername(e, t, n)
		{
			var n = document.getElementById(n);
			var r = document.getElementById(e);
			var msg_length = n.innerHTML.length;


			if(r.value=='')
			{
				n.innerHTML = t;
				return 'decline';
			}
			else if(msg_length>0)
			{
				return 'decline';
			}
			else
			{
				n.innerHTML = "";
				r.focus();
				return 'accept';
			}
		}


		function emailcheck(e, t, n)
		{
			var reg=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			var r = document.getElementById(e);
			var n = document.getElementById(n);
			
			if(reg.test(r.value) == false)
			{	
				n.innerHTML = t;
				return true;	
			}
			else
			{	
				n.innerHTML = "";
				r.focus();
				return false
				
			}
			
		}
		function passCheck(e, t, n)
		{
			var pass = document.getElementById(e).value;
			var co_pass = document.getElementById('con_pass').value;
			var n = document.getElementById(n);
			
			
			if(pass!='' && co_pass!='')
			{
				if(pass==co_pass)
				{
					n.innerHTML = "";
					return false;	
				}
				else
				{	
					n.innerHTML = t;		
					return true;		
				}
			}
			
			
		}
		function isEmpty(e, t, n)
		{
				var r = document.getElementById(e);
				var n = document.getElementById(n);
				if(r.value.replace(/\s+$/, "") == "")
				{
					n.innerHTML = t;
					return true
				}
				else
				{
					n.innerHTML = "";
					return false
				}
		}

		function nospaces(t)
		{
			if(t.value.match(/\s/g))
			{
				alert('Sorry, you are not allowed to enter any spaces');
				t.value=t.value.replace(/\s/g,'');
			}
		}  
		</script>
<?php
 
 	

			if ( is_user_logged_in() ) 
			{
			    echo 'Sign Up form will appear if you are not logged in';
			} 
			else 
			{
			 	ob_start();
				if($_GET['pra']=='true')
				{
					echo '<h2>Your Registartion is Completed Successfully, Please Login</h2>'; 
				}
				else if($_GET['pra']=='cancel')
				{
					echo '<h2>Your have cancelled the payment.</h2>'; 
				}
				else
				{
			 
					 $output = '<form class="regi_form" action="" method="post" >
					    <table width="100%" cellspacing="0" cellpadding="0" class="regtable">
					      <tbody>
					      <input type="hidden" name="cmd" value="_xclick" />
					      <input type="hidden" name="no_note" value="1" />
					      <input type="hidden" name="lc" value="UK" />'; 
						 $currency = $wpdb->get_col("SELECT value FROM ".PRO_TABLE_PREFIX."registration_detail  WHERE metaname='currency'" );  
					      
						 $output .='<input type="hidden" name="currency_code" value="'.$currency[0].'" />
					      <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest" />
					    
					      <tr>
					        <td>Username<em>&nbsp;*</em>:</td>
					        <td><input type="text" onkeyup="checkname(this.value)"  value="" id="user_login" name="user_login" /> <label id="status" ></label></td>
					      </tr>
					      <tr>
					        <td>First Name<em>&nbsp;*</em>:</td>
					        <td><input type="text" onkeyup="nospaces(this)" value="" id="first_name" name="first_name" /><label id="err_first_name" ></label></td>
					      </tr>
					      <tr>
					        <td>Last Name<em>&nbsp;*</em>:</td>
					        <td><input type="text" id="last_name" value="" name="last_name"><label id="err_last_name" ></label></td>
					      </tr>
					      <tr>
					        <td>Email<em>&nbsp;*</em>:</td>
					        <td><input type="text" value="" id="payer_email" name="payer_email"><label id="err_payer_email" ></label></td>
					      </tr>
					      <tr>
					        <td>Password<em>&nbsp;*</em>:</td>
					        <td><input type="password" value="" name="user_pass" id="user_pass"><label id="err_user_pass" ></label></td>
					      </tr>
					      <tr>
					        <td>&nbsp;</td>
					        <td><input type="submit" value="Submit" name="submit"  onclick="return form_validate();" /></td>
					      </tr>
					      </tbody>
					      
					    </table>
					  </form>
					</div>';
				    $output .= ob_get_clean();
					return $output;
				}  
			}

	
 } ?>