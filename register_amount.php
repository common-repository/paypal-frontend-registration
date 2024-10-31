<?php global $wpdb;	
if(isset($_POST['submit']))
{	 $charge = $_POST['charge'];
	 $sandbox = $_POST['sandbox'];	
	 $email = $_POST['email'];	
	 $currency = $_POST['currency'];	
	
	$wpdb->update( 
	PRO_TABLE_PREFIX.'registration_detail', 
			array( 
				'value' => $charge
			), 
			array( 'id' => 1 ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
			);
	
	$wpdb->update( 
	PRO_TABLE_PREFIX.'registration_detail', 
			array( 
				'value' => $sandbox
			), 
			array( 'id' => 2 ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
			);

	$wpdb->update( 
	PRO_TABLE_PREFIX.'registration_detail', 
			array( 
				'value' => $email
			), 
			array( 'id' => 3 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
	$wpdb->update( 
	PRO_TABLE_PREFIX.'registration_detail', 
			array( 
				'value' => $currency
			), 
			array( 'id' => 4 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
			echo '<div class="wrap">
      <div class="updated" style="background-color:#7AD03A;">
        <p><strong style="color:#FFF;" >Details are updated successfully.
        </strong> </p>
      </div>
    </div>';
			
}
?>

<script type="text/javascript">
function checkform()
{
		var charge = document.getElementById('charge').value;
		var email = document.getElementById('email').value;
		
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		if(charge =='')
		{
			alert('Please Enter Charge for Registration. ')
			return false;
		}
		else if(email == '')
		{
			alert('Please Enter your Paypal Email ID. ')
			return false;
		}
		else if(reg.test(email) == false)
		{
			alert('Invalid Paypal Email ID. ')
			return false;
				
		}
					return true;
		

}
function isNumber(evt) 
				{
					evt = (evt) ? evt : window.event;
					var charCode = (evt.which) ? evt.which : evt.keyCode;
					/*if (charCode == 32) 
					{
						return false;
					}*/
					if (charCode > 31 && (charCode < 48 || charCode > 57)) 
					{
						return false;
					}
					return true;
				}

function CopyToClipBoard(whichcode) 
{
  /* Get the text field */
  var copyText = document.getElementById(whichcode);

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

  /* Copy the text inside the text field */
  document.execCommand("copy");

  /* Alert the copied text */
  alert("Copied the code: " + copyText.value);
} 
</script>
<div id="wpbody">
  <div tabindex="0" aria-label="Main content"  style="overflow: hidden;">
    <div id="execphp-message"></div>
    <div class="wrap">
      <div class="updated fade">
        <p><strong>Paypal Details
        </strong> </p>
      </div>
    </div>
    <div class="clear"></div>
    <div class="wrap">
      <div class="updated" style=" background-color: #0074A2;">
        <p><strong style="color:#FFF;">Short code for Registration Form : <input type="text" id="BaseShortCode" value="[registartion_form]" style=" border-radius: 5px; width: 200px;" readonly="readonly" > <button onclick="CopyToClipBoard('BaseShortCode')" class="button">Copy code</button>
        </strong> </p>
      </div>
    </div>
  </div>
  
  <!-- wpbody-content -->
  <div class="clear"></div>
  <?php $myrows = $wpdb->get_col("SELECT value FROM ".PRO_TABLE_PREFIX."registration_detail" ); ?>
  <div  class="postbox">
  <form action="" method="post" >
    <table id="misc-publishing-actions " class="misc-pub-section" cellpadding="0" cellspacing="0">
      <tr>
        <td  class="misc-pub-section" >Registration Charge : </td>
      </tr>
      <tr>
        <td class="misc-pub-section"><input id="charge" type="text" name="charge" value="<?php echo $myrows[0]; ?>" onkeypress="return isNumber(event)" /><b> US$</b></td>
      </tr>
      <tr>
        <td class="misc-pub-section">Sandbox ( Enable / Disable ) : </td>
      </tr>
      <tr>
        <td class="misc-pub-section"><select name="sandbox">
            <option  value="1" <?php if($myrows[1]==1) echo 'selected="selected"'; ?> >Yes</option>
            <option  value="0"  <?php if($myrows[1]==0) echo 'selected="selected"'; ?> >No</option>
          </select></td>
      </tr>
      <tr >
        <td class="misc-pub-section">Admin Paypal Address (Defined email address will get the payment) : </td>
      </tr>
      <tr>
        <td class="misc-pub-section"><input type="text" id="email" name="email" value="<?php echo $myrows[2]; ?>" /></td>
      </tr>
      
      
       <tr >
        <td class="misc-pub-section">Currency : </td>
      </tr>
      <tr>
        <td class="misc-pub-section"><select name="currency">
            <option  value="USD" <?php if($myrows[3]=='USD') echo 'selected="selected"'; ?> >U.S. Dollar : USD</option>
            <option  value="EUR"  <?php if($myrows[3]=='EUR') echo 'selected="selected"'; ?> >Euro : EUR</option>
            <option  value="AUD"  <?php if($myrows[3]=='AUD') echo 'selected="selected"'; ?> >Australian Dollar : AUD</option>
            <option  value="GBP"  <?php if($myrows[3]=='GBP') echo 'selected="selected"'; ?> >Pound Sterling  : GBP</option>
          </select></td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td class="misc-pub-section"><input type="submit" class="button button-primary button-large"  name="submit" value="Submit" onclick="return checkform();" /></td>
      </tr>
    </table>
  </form>
  </div>
</div>
<div class="wrap">
      <div class="updated" style=" background-color: #0074A2;">
        <p><strong style="color:#FFF;">Short code for Registration Form : <input type="text" id="BaseShortCode" value="[registartion_form]" style=" border-radius: 5px; width: 200px;" readonly="readonly" >  <button onclick="CopyToClipBoard('BaseShortCode')" class="button">Copy code</button>
        </strong> </p>
      </div>
    </div>
