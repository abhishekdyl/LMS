<?php
global $wpdb,$woocommerce; 
get_header();
//$currentuserid = get_current_user_id();
//wp_enqueue_style('custom', plugin_dir_url(__FILE__) . '/assets/style/custom.css', false, '1.0.0', 'all');



?>
<br>
<br>
<br>
   
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form method="POST" id="school_quote" enctype="multipart/form-data"> 
            	<div class="row">
            		<h3 class="quots_title">Your contact information</h3>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="name">Your Name</label>
					    	<input type="text" name="name" class="form-control" id="name" value="">
					  	</div>
            			
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="email">Your Email*</label>
					    	<input type="email" name="email" class="form-control" id="email" value="">
					  	</div>
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="pnumber">Phone number*</label>
					    	<input type="number" name="pnumber" class="form-control" id="pnumber" value="">
					  	</div>
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="position">Position*</label>
					    	<SELECT name="position" id="position" class="form-control" value="">
					    		<option value="Select Position">Select Position</option>
					    		<option value="Teacher">Teacher</option>
					    		<option value="Technology Coordinator">Technology Coordinator</option>
					    		<option value="Principal">Principal</option>
					    		<option value="Administrator" selected="selected">Administrator</option>
					    		<option value="Other">Other</option>
					    	</SELECT>
					  	</div>
            		</div>
            	</div>
            	<div class="row">
            		<h3 class="quots_title">Your school information</h3>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="school">School*</label>
					    	<input type="text" name="school" class="form-control" id="school" value="">
					  	</div>
            			
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="education_authority">Local education authority*</label>
					    	<input type="text" name="education_authority" class="form-control" id="education_authority" value="">
					  	</div>
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="address">Address*</label>
					    	<input type="text" name="address" class="form-control" id="address" value="">
					  	</div>
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="city">Town/City*</label>
					    	<input type="text" name="city" class="form-control" id="city" value="">
					  	</div>
            		</div>
            	</div>
            	<div class="row">
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="state">County/State*</label>
					    	<input type="text" name="state" class="form-control" id="state" value="">
					  	</div>
            			
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="postcode">Postcode*</label>
					    	<input type="text" name="postcode" class="form-control" id="postcode" value="">
					  	</div>
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="country">Country*</label>
					    	<SELECT name="country" id="country" class="form-control" value="">
					    		<?php country_list();?>
					    	</SELECT>
					  	</div>
            		</div>
            		<div class="col-md-3"></div>
            	</div>

            	<div class="row">
            		<h3 class="quots_title">Your implementation</h3>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="subjects">Subjects*</label>
					    	<input class="form-check-input" type="radio" name="subjects" value="1" checked>
					    	<p class="subject-text">Math & English</p>
					  	</div>
            			
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="total_students">Number of students*</label>
					    	<input type="number" name="total_students" class="form-control" id="total_students" value="">
					  	</div>
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
					    	<label for="total_teachers">Number of teachers</label>
					    	<input type="number" name="total_teachers" class="form-control" id="total_teachers" value="">
					  	</div>
            		</div>
            		<div class="col-md-3">
            			
            		</div>
            	</div>
            	<div class="row">
            		<div class="col-md-12">
            			<div class="form-group">
					    	<label for="study_plan">How do you plan to use StudyIf?*</label>
					    	<textarea class="form-control" name="study_plan" id="study_plan" rows="3" value=""></textarea>
					  	</div>	
            		</div>
            	</div>
            	<div class="row ">
        			<div class="col-md-2"></div>	  
	                <div class="col-md-6">
	                   <input type="submit" name="submit" value="Submit" class="btn btn-info">
                    </div> 
	                <div class="col-md-4"></div>
	            </div>
	        </form>
	    </div>
	</div>
</div> 

<?php
if(isset($_POST['submit'])){
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    //die;
}
function country_list(){
	echo $html = '
	<option value="">SELECT</option>
    <option value="Aruba">Aruba</option>
    <option value="Afghanistan">Afghanistan</option>
    <option value="Angola">Angola</option>
    <option value="Albania">Albania</option>
    <option value="Andorra">Andorra</option>
    <option value="Argentina">Argentina</option>
    <option value="Algeria">Algeria</option>
    <option value="Armenia">Armenia</option>
    <option value="American Samoa">American Samoa</option>
    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
    <option value="Australia">Australia</option>
    <option value="Austria">Austria</option>
    <option value="Azerbaijan">Azerbaijan</option>
    <option value="Burundi">Burundi</option>
    <option value="Belgium">Belgium</option>
    <option value="Benin">Benin</option>
    <option value="Burkina Faso">Burkina Faso</option>
    <option value="Bangladesh">Bangladesh</option>
    <option value="Bulgaria">Bulgaria</option>
    <option value="Bahrain">Bahrain</option>
    <option value="Bahamas">Bahamas</option>
    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
    <option value="Belarus">Belarus</option>
    <option value="Belize">Belize</option>
    <option value="Bermuda">Bermuda</option>
    <option value="Bolivia">Bolivia</option>
    <option value="Brazil">Brazil</option>
    <option value="Barbados">Barbados</option>
    <option value="Brunei Darussalam">Brunei Darussalam</option>
    <option value="Bhutan">Bhutan</option>
    <option value="Botswana">Botswana</option>
    <option value="Central African Republic">Central African Republic</option>
    <option value="Cambodia">Cambodia</option>
    <option value="Canada">Canada</option>
    <option value="Chile">Chile</option>
    <option value="China">China</option>
    <option value="Côte d Ivoire">Côte d Ivoire</option>
    <option value="Cameroon">Cameroon</option>
    <option value="Congo">Congo</option>
    <option value="Congo">Congo</option>
    <option value="Cook Islands">Cook Islands</option>
    <option value="Colombia">Colombia</option>
    <option value="Comoros">Comoros</option>
    <option value="Croatia">Croatia</option>
    <option value="Cape Verde">Cape Verde</option>
    <option value="Costa Rica">Costa Rica</option>
    <option value="Chad">Chad</option>
    <option value="Cuba">Cuba</option>
    <option value="Cayman Islands">Cayman Islands</option>
    <option value="Cyprus">Cyprus</option>
    <option value="Czech Republic">Czech Republic</option>
    <option value="Djibouti">Djibouti</option>
    <option value="Dominica">Dominica</option>
    <option value="Denmark">Denmark</option>
    <option value="Dominican Republic">Dominican Republic</option>
    <option value="Ecuador">Ecuador</option>
    <option value="Egypt">Egypt</option>
    <option value="Eritrea">Eritrea</option>
    <option value="El Salvador">El Salvador</option>
    <option value="Estonia">Estonia</option>
    <option value="Ethiopia">Ethiopia</option>
    <option value="Finland">Finland</option>
    <option value="Fiji">Fiji</option>
    <option value="France">France</option>
    <option value="Gabon">Gabon</option>
    <option value="Georgia">Georgia</option>
    <option value="Ghana">Ghana</option>
    <option value="Guinea">Guinea</option>
    <option value="Gambia">Gambia</option>
    <option value="Guinea-Bissau">Guinea-Bissau</option>
    <option value="Germany">Germany</option>
    <option value="Greece">Greece</option>
    <option value="Grenada">Grenada</option>
    <option value="Guatemala">Guatemala</option>
    <option value="Guam">Guam</option>
    <option value="Guyana">Guyana</option>
    <option value="Hong Kong">Hong Kong</option>
    <option value="Honduras">Honduras</option>
    <option value="Haiti">Haiti</option>
    <option value="Hungary">Hungary</option>
    <option value="Indonesia">Indonesia</option>
    <option value="India">India</option>
    <option value="Ireland">Ireland</option>
    <option value="Iran">Iran</option>
    <option value="Iraq">Iraq</option>
    <option value="Iceland">Iceland</option>
    <option value="Israel">Israel</option>
    <option value="Italy">Italy</option>
    <option value="Jamaica">Jamaica</option>
    <option value="Jordan">Jordan</option>
    <option value="Japan">Japan</option>
    <option value="Kazakhstan">Kazakhstan</option>
    <option value="Kenya">Kenya</option>
    <option value="Korea">Korea</option>
    <option value="Kyrgyzstan">Kyrgyzstan</option>
    <option value="Kiribati">Kiribati</option>
    <option value="Korea">Korea</option>
    <option value="Kuwait">Kuwait</option>
    <option value="Lao People’s Democratic Republic">Lao People’s Democratic Republic</option>
    <option value="Lebanon">Lebanon</option>
    <option value="Liberia">Liberia</option>
    <option value="Libya">Libya</option>
    <option value="Liechtenstein">Liechtenstein</option>
    <option value="Lesotho">Lesotho</option>
    <option value="Lithuania">Lithuania</option>
    <option value="Luxembourg">Luxembourg</option>
    <option value="Latvia">Latvia</option>
    <option value="Morocco">Morocco</option>
    <option value="Monaco">Monaco</option>
    <option value="Moldova">Moldova</option>
    <option value="Madagascar">Madagascar</option>
    <option value="Micronesia">Micronesia</option>
    <option value="Maldives">Maldives</option>
    <option value="Mexico">Mexico</option>
    <option value="Marshall Islands">Marshall Islands</option>
    <option value="Macedonia">Macedonia</option>
    <option value="Mali">Mali</option>
    <option value="Malta">Malta</option>
    <option value="Myanmar">Myanmar</option>
    <option value="Montenegro">Montenegro</option>
    <option value="Mongolia">Mongolia</option>
    <option value="Mozambique">Mozambique</option>
    <option value="Mauritania">Mauritania</option>
    <option value="Mauritius">Mauritius</option>
    <option value="Malawi">Malawi</option>
    <option value="Malaysia">Malaysia</option>
    <option value="Namibia">Namibia</option>
    <option value="Niger">Niger</option>
    <option value="Nigeria">Nigeria</option>
    <option value="Nicaragua">Nicaragua</option>
    <option value="Netherlands">Netherlands</option>
    <option value="Norway">Norway</option>
    <option value="Nepal">Nepal</option>
    <option value="Nauru">Nauru</option>
    <option value="New Zealand">New Zealand</option>
    <option value="Oman">Oman</option>
    <option value="Pakistan">Pakistan</option>
    <option value="Panama">Panama</option>
    <option value="Peru">Peru</option>
    <option value="Philippines">Philippines</option>
    <option value="Palau">Palau</option>
    <option value="Papua New Guinea">Papua New Guinea</option>
    <option value="Poland">Poland</option>
    <option value="Puerto Rico">Puerto Rico</option>
    <option value="Portugal">Portugal</option>
    <option value="Paraguay">Paraguay</option>
    <option value="Palestine">Palestine</option>
    <option value="Qatar">Qatar</option>
    <option value="Romania">Romania</option>
    <option value="Russian Federation">Russian Federation</option>
    <option value="Rwanda">Rwanda</option>
    <option value="Saudi Arabia">Saudi Arabia</option>
    <option value="Sudan">Sudan</option>
    <option value="South Africa">South Africa</option>
    <option value="Senegal">Senegal</option>
    <option value="Singapore">Singapore</option>
    <option value="Solomon Islands">Solomon Islands</option>
    <option value="Sierra Leone">Sierra Leone</option>
    <option value="San Marino">San Marino</option>
    <option value="Somalia">Somalia</option>
    <option value="Serbia">Serbia</option>
    <option value="Saint Lucia">Saint Lucia</option>
    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
    <option value="Sao Tome and Principe">Sao Tome and Principe</option>
    <option value="Suriname">Suriname</option>
    <option value="Slovakia">Slovakia</option>
    <option value="Slovenia">Slovenia</option>
    <option value="Sweden">Sweden</option>
    <option value="Spain">Spain</option>
    <option value="Switzerland">Switzerland</option>
    <option value="Swaziland">Swaziland</option>
    <option value="Samoa">Samoa</option>
    <option value="Sri Lanka">Sri Lanka</option>
    <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
    <option value="Seychelles">Seychelles</option>
    <option value="Syrian Arab Republic">Syrian Arab Republic</option>
    <option value="Togo">Togo</option>
    <option value="Thailand">Thailand</option>
    <option value="Tajikistan">Tajikistan</option>
    <option value="Turkmenistan">Turkmenistan</option>
    <option value="Timor-Leste">Timor-Leste</option>
    <option value="Tonga">Tonga</option>
    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
    <option value="Tunisia">Tunisia</option>
    <option value="Turkey">Turkey</option>
    <option value="Tuvalu">Tuvalu</option>
    <option value="Taiwan, Province of China">Taiwan, Province of China</option>
    <option value="Tanzania">Tanzania</option>
    <option value="Uganda">Uganda</option>
    <option value="Ukraine">Ukraine</option>
    <option value="Uruguay">Uruguay</option>
    <option value="United Arab Emirates">United Arab Emirates</option>
    <option value="United Kingdom">United Kingdom</option>
    <option value="United States">United States</option>
    <option value="Uzbekistan">Uzbekistan</option>
    <option value="Venezuela">Venezuela</option>
    <option value="Virgin Islands">Virgin Islands</option>
    <option value="Viet Nam">Viet Nam</option>
    <option value="Vanuatu">Vanuatu</option>
    <option value="Yemen">Yemen</option>
    <option value="Zambia">Zambia</option>
    <option value="Zimbabwe">Zimbabwe</option>';
    return $html;
}

?>
<style type="text/css">
    #site-loader{
        display: none;
    }
</style>
<script type="text/javascript">
</script>
	<?php get_footer(); ?>