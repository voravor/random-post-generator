<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>Term Generator</h2>
	
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
                                        
                                        <th scope="row"><label for="term_type">Type: </label></th>
                                        <td>
                                            <select name="term_type">
                                                <?php foreach ( $taxonomies as $taxonomy ) : ?>
                                                    <option value="<?= $taxonomy ?>"><?= $taxonomy ?></option>
                                                <?php endforeach; ?>
                                                
                                            </select>
                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        
                                        <th scope="row"><label for="num_categories">Number of terms: </label></th>
                                        <td><input class="small-text" type="text" name="num_categories" value="10" /></td>
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