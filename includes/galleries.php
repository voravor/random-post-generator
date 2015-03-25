<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>Gallery Generator</h2>
	
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
                                        
                                        <th scope="row"><label for="galleries">Number of galleries: </label></th>
                                        <td><input class="small-text" type="text" name="galleries" value="3" /></td>
                                    </tr>
                                    <tr>
                                        
                                        <th scope="row"><label for="max_slides">Max slides: </label></th>
                                        <td><input class="small-text" type="text" name="max_slides" value="20" /></td>
                                    </tr>
                                    
                                     <tr>
                                        <th scope="row"><label for="featured_image">Include featured image:</label> </th>
                                        <td><input type="checkbox" name="featured_image" value="1" checked/></td>
                                     </tr>
                                     
                                     <tr>
                                        <th scope="row"><label for="links">Decorate:</label> </th>
                                        <td><input type="checkbox" name="decorate" value="1" checked /></td>
                                     </tr>

                                    <tr>
                                        <th scope="row"><label for="paragraphs">Number of paragraphs per post:</label> </th>
                                        <td><input class="small-text" type="text" name="paragraphs" value="3" /></td>
                                     </tr>

                                    <tr>
                                        <th scope="row"></th>
                                        <td></td>
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