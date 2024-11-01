<?php

if ( ! class_exists( 'PluginOptionsPage' ) ) :
  
  require_once('AdminSection.php');
  
  class PluginOptionsPage {
    
    private $pluginName;
    private $pluginMenu;
    private $pluginAlias;
    private $pluginOptions;
    private $adminSection;
    
    function __construct($adminSection=0, $pluginName = "", $pluginAlias = "myPlugin", &$pluginOptions=array()) {
      $this->pluginName = $pluginName;
      $this->pluginMenu = $pluginName;
      $this->pluginAlias = $pluginAlias;
      $this->pluginOptions = $pluginOptions;
      $this->adminSection = $adminSection;
      add_action('admin_menu', array($this, 'adminPage'));
    }
    
    public function install() {
        foreach ($this->pluginOptions as $value) {
            if( isset($value['id']) && isset($value['std']) ){
                add_option( $value['id'], $value['std'], '', 'yes' );
            }
        }
    }
    
    public function uninstall() {
        foreach ($this->pluginOptions as $value) {
            delete_option($value['id']);
        }
    }
    
    public function adminPage() {
            if ($this->pluginAlias == $_REQUEST['page'] &&  'save' == $_REQUEST['action']) {
                //die(print_r($_REQUEST));
                foreach ($this->pluginOptions as $value) {
                    if (isset($_REQUEST[$value['id']])):
                        $newVal = $_REQUEST[$value['id']];
                    else: $newVal = 0; endif;
                    //echo "<H1>$newVal</H1>";
                    update_option($value['id'], $newVal);
                }
                $_REQUEST['saved'] = true;
                //header("Location: themes.php?page=".$wooHeaderProducts_alias."&saved=true");
                //die();
            }
        AdminSection::register($this->adminSection, $this->pluginName, $this->pluginMenu, 'manage_options', $this->pluginAlias, array($this, 'render'));
    }
    
    public function render(){
        $i = 0;
        
        if ($_REQUEST['saved'])
            echo '<div id="message" class="updated <?php echo $pluginAlias;?>"><p>Plugin settings updated.</p></div>';
        if ($_REQUEST['reset'])
            echo '<div id="message" class="updated <?php echo $pluginAlias;?>"><p>Plugin settings reset.</p></div>';
        
?>
		<div class="wrap">
		<h2><?php echo $this->pluginName;?> Settings</h2>

		<form method="post">

			<?php
        foreach ($this->pluginOptions as $value) {
            switch ($value['type']) {
                case "open":
                    break;
                case "close":
                  ?>
                    </tbody>
                    </table>
                    <p class="submit"><input type="submit"  name="save<?php echo $i;?>" id="submit" class="button-primary" value="Save Changes"></p>
                 <?php
                    break;
                case "section":
                    $i++;
                  ?>
						              <h3><?php echo $value['name'];?></h3>
						              <p>
                  <?php
                    echo $value['description'];
                  ?>
                    </p>
						              <table class="form-table">
							               <tbody>
                  <?php
                    break;
                case 'text':
                  ?>
                        <tr valign="top">
                          <th scope="row">
                           <label for="<?php echo $value['id'];?>"><?php echo $value['name'];?></label>
                          </th>
                          <td>  
                            <input name="<?php  echo $value['id'];?>" id="<?php echo $value['id'];?>" class="regular-text" type="<?php echo $value['type'];?>" value="<?php
                                            echo stripslashes(get_option($value['id']));
                            ?>" />
                            <span class="description"><?php echo $value['desc'];?></span>
                          </td>
						                  </tr>
					             <?php
                    break;
                case 'number':
                  ?>
                        <tr valign="top">
                          <th scope="row">
                           <label for="<?php echo $value['id'];?>"><?php echo $value['name'];?></label>
                          </th>
                          <td>  
                            <input name="<?php  echo $value['id'];?>" id="<?php echo $value['id'];?>" class="small-text" type="<?php echo $value['type'];?>" value="<?php
                                            echo stripslashes(get_option($value['id']));
                            ?>" />
                            <span class="description"><?php echo $value['desc'];?></span>
                          </td>
						                  </tr>
					             <?php
                    break;
                case 'textarea':
                  ?>
                        <tr valign="top">
                         <th scope="row"><label for="<?php echo $value['id'];?>"><?php echo $value['name'];?></label></th>
                         <td>
                          <textarea name="<?php echo $value['id'];?>" type="<?php echo $value['type'];?>" cols="50" rows="6"><?php
                                          echo stripslashes(get_option($value['id']));
                        ?></textarea>
                          <span class="description"><?php echo $value['desc'];?></span>
                         </td>
                        </tr>
					             <?php
                    break;
                case 'select':
                  ?>
                        <tr valign="top">
                         <th scope="row"><label for="<?php echo $value['id'];?>"><?php echo $value['name'];?></label></th>
                         <td>
                          <select name="<?php echo $value['id'];?>" id="<?php echo $value['id'];?>">
                          <?php
                                      foreach ($value['options'] as $option) {
                                      ?>
                                        <option value="<?php echo $option['value'];?>" <?php
                                          if (get_option($value['id']) == $option['value']) {
                                              echo 'selected="selected"';
                                          } ?> ><?php echo $option['name'];?></option><?php
                                      }?>
                          </select>
                          <span class="description"><?php echo $value['desc'];?></span>
                         </td>
                        </tr>
					             <?php
                    break;
                case 'checkbox':
                  ?>
                        <tr valign="top">
                         <th scope="row"><label for="<?php echo $value['id'];?>"><?php echo $value['name'];?></label></th>
                         <td>
                          <?php
                                    if (get_option($value['id']) == 1) {
                                        $checked = "checked=\"checked\"";
                                    } else {
                                        $checked = "";
                                    }
                          ?>
                            <input type="checkbox" name="<?php echo $value['id'];?>" id="<?php echo $value['id'];?>" <?php checked('1', get_option($value['id']));?> value="1" />
                            <span class="description"><?php echo $value['desc'];?></span>
                         </td>
                        </tr>
					             <?php
                    break;
            }
        }
?>
			   <input type="hidden" name="action" value="save" />
		  </form>
		</div>
<?php
    }

    public function pluginName($name) {
        if (is_null($name)):
            return  $this->pluginName;
        else: $this->pluginName = $name;
        endif;
    }
    public function pluginAlias($name) {
        if (is_null($name)):
            return  $this->pluginAlias;
        else: $this->pluginAlias = $name;
        endif;
    }
    public function menu($name) {
        if (is_null($name)):
            return  $this->pluginMenu;
        else: $this->pluginMenu = $name;
        endif;
    }
    public function options($array) {
        if (is_null($array)):
            return  $this->pluginOptions;
        else: $this->pluginOptions = $array;
        endif;
    }
    public function section($value) {
        if (is_null($value)):
            return  $this->adminSection;
        else: $this->adminSection = $value;
        endif;
    }
    
  }

endif;
?>