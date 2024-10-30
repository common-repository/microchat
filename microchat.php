<?php
/**
 * Plugin Name: MicroChat
 * Plugin URI: https://microchat.io
 * Description: Microchat is the free WordPress live chat plugin. Microchat will help you have streamlined and easy communication with customers.
 * Version: 1.0.1
 * Author: Microchat LLC
 * Author URI: https://microchat.io
 * License: GPLv2 or later
 */

class MicroChatWidgetManager
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $microchat_access_token;
    private $microchat_embed_widget;
    private $microchat_full_page_widget;

    /**
     * Start up
     */
    public function __construct()
    {
        $this->microchat_access_token = get_option('microchat_access_token');
        $this->microchat_embed_widget = get_option('microchat_embed_widget_style');
        $this->microchat_full_page_widget = get_option('microchat_full_page_widget_style');

        if ($this->microchat_embed_widget == 1)
        {
            add_action('wp_enqueue_scripts', array($this, 'microchat_enqueue_embed_widget_script'));
            add_action('wp_footer', array($this, 'microchat_insert_embed_widget_script_tag'));
        }

        if ($this->microchat_full_page_widget == 1) 
        {
            add_action('wp_enqueue_scripts', array($this, 'microchat_enqueue_full_page_widget_script'));
            add_shortcode('microchat_full_page_widget', array($this, 'microchat_insert_full_page_widget_tag'));
        }
    }

    public function microchat_enqueue_embed_widget_script()
    {
        wp_enqueue_script('microchat_widget_script', 'https://microchat.io/Scripts/widget.js', array('jquery'), true);
    }

    public function microchat_enqueue_full_page_widget_script()
    {
        wp_enqueue_script('microchat_widget_script', 'https://microchat.io/Scripts/widget.fullscreen.js', array('jquery'), true);
    }

    public function microchat_insert_embed_widget_script_tag() 
    {
    ?>
        <script type="text/javascript">
            window.microChat.init({
                websiteId: '<?php echo esc_js(base64_decode($this->microchat_access_token)) ?>',
                lang: "en"
            });
        </script>
    <?php
    }

    public function microchat_insert_full_page_widget_tag($attr)
    {
        $content = "<div \r\n";
        $content .= "id='MicroChatWebWidget' \r\n";
        $content .= "data-website-id='". sanitize_text_field(base64_decode($this->microchat_access_token)) ."' \r\n";
        $content .= "data-is-fullscreen='true' \r\n";
        $content .= "style='border: 0px;background-color: transparent;overflow: hidden;width: 100%;height: 100%;display: flex;min-height: 600px;'> \r\n";
        $content .= "</div> \r\n";

        return $content;
    }

}

class MicroChatSettingPage
{
    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'microchat_add_plugin_page'));
    }

    /**
     * Add options page
     */
    public function microchat_add_plugin_page()
    {
        // This page will be under 'Settings"
        add_options_page(
            'MicroChat Setting', 
            'MicroChat', 
            'manage_options', 
            'microchat-setting-admin', 
            array($this, 'microchat_create_admin_page')
        );

        //call register settings function
	    add_action('admin_init', array($this, 'microchat_register_microchat_plugin_settings'));
    }

    function microchat_register_microchat_plugin_settings() 
    {
        register_setting('microchat-widget-plugin-settings-group', 'microchat_access_token');
        register_setting('microchat-widget-plugin-settings-group', 'microchat_embed_widget_style');
        register_setting('microchat-widget-plugin-settings-group', 'microchat_full_page_widget_style');
    }


    /**
     * Options page callback
     */
    public function microchat_create_admin_page()
    {
        if (isset($_POST['submit']) && $_POST['submit'] == "Save Changes") 
        {
            if (isset($_POST['microchat_access_token'])) 
                update_option('microchat_access_token', sanitize_text_field($_POST['microchat_access_token']));
    
            if (isset($_POST['microchat_embed_widget_style'])) 
                update_option('microchat_embed_widget_style', sanitize_text_field($_POST['microchat_embed_widget_style']));
            else
                update_option('microchat_embed_widget_style', '');
    
            if (isset($_POST['microchat_full_page_widget_style'])) 
                update_option('microchat_full_page_widget_style', sanitize_text_field($_POST['microchat_full_page_widget_style']));
            else
                update_option('microchat_full_page_widget_style', '');
        }

    ?>
        <div class="wrap">
            <h1 style="margin-bottom: 10px;">MicroChat - Settings</h1>
            <div>
                <a class="add-new-h2" target="_blank" href="https://docs.microchat.io/docs/how-to-deploy-chatbot-using-wordpress-plugin" style="margin-left: 0">Read Instructions</a>
                <a class="add-new-h2" target="_blank" href="https://www.youtube.com/watch?v=yraQAbQp56E">Watch Tutorial</a>
            </div>
            <div id="poststuff" style="margin-top: 10px;">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="postbox">
                            <div class="inside">
                                <div id="microchat-instructions">
                                    <h3 class="cc-labels">
                                        To begin, there are three simple steps to follow:
                                    </h3>
                                    <p>
                                        <b>1.</b> 
                                        If you're new to MicroChat, click here to sign up.
                                        <a 
                                            href="https://microchat.io/signup?c=confirmation&pid=70948589-b828-4ecf-a53b-53ffa859b544&isTrial=1" 
                                            target="_blank" 
                                            class="button button-primary" 
                                            style="margin: auto 15px; background-color: #208a46; border-color: #208a46; text-shadow: none; box-shadow: none;">
                                            Sign up for a free account.
                                        </a>
                                    </p>
                                    <p>
                                        <b>2.</b> Set widget options such as Chat Settings, Appearance, and Working Hours, among others.
                                    </p>
                                    <p>
                                        <b>3.</b> Copy and paste the access token here from the <a href="https://microchat.io/Settings/Developer">Developer</a> menu.
                                    </p>
                                </div>
                                <form method="post" action="">
                                    <?php settings_fields('microchat-widget-plugin-settings-group'); ?>
                                    <?php do_settings_sections('microchat-widget-plugin-settings-group'); ?>
                                    <table class="form-table">
                                        <tr valign="top">
                                            <th scope="row">Access Token</th>
                                            <td>
                                                <input type="text" name="microchat_access_token" value="<?php echo esc_attr(get_option('microchat_access_token')); ?>" class="large-text" required/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Widget Style</th>
                                            <td>
                                                <fieldset>
                                                    <legend class="screen-reader-text">
                                                        <span>Widget Style</span>
                                                    </legend>
                                                    <label>
                                                        <input type="checkbox" name="microchat_embed_widget_style" value="1" <?php checked( '1', get_option('microchat_embed_widget_style')); ?>> 
                                                            <span class="format-i18n">
                                                                Embed
                                                            </span>
                                                    </label>
                                                    <p>This option will add a floating micro chat widget on pages.</p>
                                                    <br/>
                                                    <label>
                                                        <input type="checkbox" name="microchat_full_page_widget_style" value="1" <?php checked( '1', get_option('microchat_full_page_widget_style')); ?>> 
                                                            <span class="format-i18n">
                                                                Full Page
                                                            </span>
                                                    </label>
                                                    <p class="description">
                                                        <strong>Note:</strong> Please place <code>[microchat_full_page_widget]</code> this short code where you want to show full screen chat window.
                                                    </p>
                                                </fieldset>
                                            </td>
                                        </tr>
                                    </table>
                                    <p class="submit">
                                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <div class="postbox">
                            <h3 class="hndle">Show us some Love</h3>
                            <div class="inside">
                                <p>
                                    Have you found MicroChat.io useful? Please rate it five stars and leave a brief comment on wordpress.org. That would be extremely helpful
                                </p>
                                <p>
                                    <a href="https://wordpress.org/plugins/microchat/#reviews" target="_blank" class="button">Rate 5 Stars</a>
                                </p>
                            </div>
                        </div>
                        <div class="postbox">
                            <h3 class="hndle">Need Help?</h3>
                            <div class="inside">
                                <p>For assistance, please consult our documentation or contact our support.</p>
                                <p>
                                    <strong>
                                        <a href="https://docs.microchat.io/" target="_blank" class="button">Explore Helpdesk</a>
                                    </strong>
                                </p>
                            </div>
                        </div>
                        <div class="postbox">
                            <h3 class="hndle">Let's be Friends</h3>
                            <div class="inside">
                                <a href="https://twitter.com/Microchat1" target="_blank" class="button" style="margin-bottom: 5px;">Follow on Twitter</a>
                                <a href="https://www.instagram.com/microchat.io/" target="_blank" class="button" style="margin-bottom: 5px;">Follow on Instagram</a>
                                <a href="https://www.facebook.com/gomicrochat/" target="_blank" class="button" style="margin-bottom: 5px;">Follow on FaceBook</a>
                                <a href="https://www.linkedin.com/company/microchat" target="_blank" class="button" style="margin-bottom: 5px;">Follow on Linked In</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}

if(is_admin())
{
    $microchat_settings_page = new MicroChatSettingPage();
}

$microchat_widget_manager = new MicroChatWidgetManager();
?>