<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>User Generator</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-1">
		
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox">
					
						<div class="inside">
                            
                            <form action="" method="post">
                                
                                <table class="form-table">
                                    <tr>
                                        
                                        <th scope="row"><label for="num_users">Number of users: </label></th>
                                        <td><input class="small-text" type="text" name="num_users" value="1" /></td>
                                    </tr>
                                     
                                     <tr>
                                        <th scope="row"><label for="role">Role:</label></th>
                                        <td>
                                            <select name="role">
                                                <?php wp_dropdown_roles(); ?>
                                            </select>
                                        </td>
                                     </tr>

                                </table>
                                
                                <?php submit_button(
                                        'Generate',
                                        $type = 'submit',
                                        $name = 'generate',
                                        $wrap = true,
                                        $other_attributes = null );
                                ?>
                                
                            </form>
						</div> <!-- .inside -->
					
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables .ui-sortable -->
				
			</div> <!-- post-body-content -->
			
			
			
		</div> <!-- #post-body .metabox-holder .columns-2 -->
		
		<br class="clear">
	</div> <!-- #poststuff -->
	
</div> <!-- .wrap -->