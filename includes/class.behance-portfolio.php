<?php

class BehancePortfolio
{
    private static $initiated = false; //to ensure only one instance of the plugin is running

    /**
     * Initializes the plugin with required templates, js, css and assets
     *
     * @since Version 1.0.0
     */
    public static function ecpt_init()
    {
        if (!self::$initiated) {
            self::ecpt_init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    private static function ecpt_init_hooks()
    {
        self::$initiated = true;
        wp_register_style('fontawesome', BEHANCE_PORTFOLIO_PLUGIN_URL . 'public/fontawesome5.8.1.css');
        wp_register_style('bootstrap-css', BEHANCE_PORTFOLIO_PLUGIN_URL.'public/bootstrap.min.css');
        wp_register_script('bootstrap-js', BEHANCE_PORTFOLIO_PLUGIN_URL.'public/bootstrap.min
		.js', array(''),BEHANCE_PORTFOLIO_VERSION,true);
        if (is_admin()) {
            add_action('admin_menu', array ('BehancePortfolio', 'ecpt_behance_portfolio_options_page'));

            wp_register_style('bp_admin_styles', BEHANCE_PORTFOLIO_PLUGIN_URL . 'admin/styles.css');
            wp_enqueue_style('bp_admin_styles');
            wp_enqueue_style('bootstrap-css');
            wp_enqueue_script('bootstrap-js');
        }
    }

    static function ecpt_behance_portfolio_options_page()
    {
        add_menu_page(
            'Behance Portfolio',
            'Behance Portfolio',
            'manage_options',
            'behance_portfolio',
            array('BehancePortfolio', 'ecpt_behance_portfolio_options_page_html'),
            'dashicons-editor-bold',
            20
        );
        register_setting('bp-settings-display', 'bp_display_option_number');
        register_setting('bp-settings-display', 'bp_display_option_grid');
        register_setting('bp-settings-profile', 'bp_profile_option_username');

    }

    /**
     * Plugin Settings Page
     */
    public static function ecpt_behance_portfolio_options_page_html()
    {
        ?>
        <section id="wpbody">
            <div id="wpbody-content">
                <div class="wrap">
                    <h1 class="wp-heading-inline mb-3">
                        Behance Portfolio
                    </h1>
                    <p>You can use this shortcode: <code>[behanceportfolio]</code></p>
                    <hr class="wp-header-end">

                    <section class="bp-content-area">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-settings-tab"
                                   data-toggle="tab" href="#nav-settings" role="tab"
                                   aria-controls="nav-settings"
                                   aria-selected="true">Display Settings</a>
                                <a class="nav-item nav-link" id="nav-api-tab" data-toggle="tab"
                                   href="#nav-api" role="tab" aria-controls="nav-api"
                                   aria-selected="false">Connect to Behance</a>
                            </div>
                        </nav>
                        <div class="tab-content border border-top-0 pt-3 pl-3" id="nav-tabContent">
                            <?php self::ecpt_behance_api_settings(); ?>
                            <?php self::ecpt_display_settings(); ?>
                        </div>
                    </section>
                </div>
            </div>
        </section>
        <?php
    }

    public static function ecpt_display_settings()
    {
        ?>
        <div class="tab-pane fade show active" id="nav-settings" role="tabpanel"
             aria-labelledby="nav-settings-tab">
            <section class="display-settings">
                <h3>Number of Projects</h3>
                <form method="post" action="options.php">
                    <div class="form-group">
                        <?php settings_fields('bp-settings-display'); ?>
                        <?php do_settings_sections('bp-settings-display'); ?>
                        <label for="display-option-number" class=""></label>
                        <input type="number" id="display-option-number"
                               class="form-control mb-2 mr-sm-2 w-25 d-inline-block" name="bp_display_option_number"
                               aria-describedby="displayOptionNumber"
                               value="<?php echo esc_attr(!empty(get_option('bp_display_option_number')) ? get_option('bp_display_option_number') : 8) ?>">
                        <small id="displayOptionNumber" class="form-text small
                                                                text-muted">Please Select the number of projects you
                            wish to display or leave 0 for all
                        </small>
                    </div>

                    <hr>
                    <div class="form-group disabled">
                        <h3 class="text-muted">Display Style  (coming soon)</h3>
                        <?php
                        $checked = get_option('bp_display_option_grid');
                        $grid = "";
                        $load_more = "";
                        switch ($checked) {
                            case 'grid':
                                $grid = "checked";
                                break;
                            case 'load-more':
                                $load_more = "checked";
                                break;
                            default:
                                $grid = "checked";
                                break;
                        }
                        ?>

                        <label class="mr-3 text-muted" for="display-option-grid">
                            <input type="radio"
                                   id="display-option-grid"
                                   name="bp_display_option_grid" value="grid" disabled
                                <?= $grid ?>
                                   aria-describedby="displayOptionGrid"
                                   aria-label="Display in Grid">&nbsp;
                            Display in Grid (Pagination)
                        </label>
                        <label for="display-option-load-more" class="text-muted">
                            <input type="radio" id="display-option-load-more"
                                   aria-label="Display in Ajax Load More"
                                   value="load-more" <?= $load_more ?>
                                   aria-describedby="displayOptionGrid" disabled
                                   name="bp_display_option_grid">&nbsp;Display
                            in Load More
                        </label>
                        <small id="displayOptionGrid" class="form-text small
                                                                text-muted">Please Select the display style for
                            the projects you wish to display per page or
                            per ajax load.
                        </small>
                    </div>
                    <div class="form-group">
                        <?php submit_button(); ?>
                    </div>

                </form>
            </section>
        </div>
        <?php
    }

    public static function ecpt_behance_api_settings()
    {
        ?>
        <div class="tab-pane fade" id="nav-api" role="tabpanel"
             aria-labelledby="nav-api-tab">
            <section class="api-settings">
                <h3>Access Control</h3>
                <form method="post" action="options.php">
                    <div class="form-group">
                        <?php settings_fields('bp-settings-profile'); ?>
                        <?php do_settings_sections('bp-settings-profile'); ?>

                        <label for="profile-option-username" class=""></label>
                        <input type="text" id="profile-option-username"
                               class="form-control mb-2 mr-sm-2 w-25 d-inline-block"
                               name="bp_profile_option_username"
                               aria-describedby="profileOptionUsername"
                               value="<?php echo esc_attr(!empty(get_option('bp_profile_option_username')) ? get_option('bp_profile_option_username') : '') ?>">
                        <small id="profileOptionUsername" class="form-text small text-muted">
                            Please Enter your Behance Username.
                        </small>
                    </div>
                    <div class="form-group">
                        <?php submit_button(); ?>
                    </div>

                </form>
            </section>
        </div>
        <?php
    }


    /**
     * Activate's plugin after validating requirements
     *
     * @since Version 1.0.0
     */
    static function ecpt_activate_plugin()
    {
        if (version_compare($GLOBALS['wp_version'], BEHANCE_PORTFOLIO_MINIMUM_WP_VERSION, '<')) {
            load_plugin_textdomain('behance-portfolio');

            $message = '<strong>' . sprintf(esc_html__('Yikes! Looks like we\'re not compatible. Behance Portfolio (%s)
                        requires WordPress %s or higher.', 'behance-portfolio'), BEHANCE_PORTFOLIO_VERSION,
                    BEHANCE_PORTFOLIO_MINIMUM_WP_VERSION) . '</strong> '
                . sprintf(__('Please <a href="%1$s" target="_blank">consider upgrading WordPress</a> to a
                        newer version.', 'behance-portfolio'), 'https://codex.wordpress.org/Upgrading_WordPress');
            BehancePortfolio::ecpt_bail_on_activation($message);
        }
    }

    /**
     * Deactivate's plugin after validating it's not from external source
     *
     * @since Version 1.0.0
     */
    static function ecpt_deactivate_plugin()
    {

    }

    /**
     * Error reporting if activation fails
     *
     * @since Version 1.0.0
     */
    private static function ecpt_bail_on_activation($message, $deactivate = true)
    {
        ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>"/>
            <style>
                * {
                    text-align: center;
                    margin: 0;
                    padding: 0;
                    color: #0a0a0a;
                    font-family: "Montserrat", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
                }

                p {
                    margin-top: 1em;
                    font-size: 16px;
                }

                a {
                    color: #2f56e4;
                }
            </style>
        </head>
        <body>
        <p><?php echo($message); ?></p>
        </body>
        </html>
        <?php
        if ($deactivate) {
            $plugins = get_option('active_plugins');
            $behance_portfolio = plugin_basename(BEHANCE_PORTFOLIO_PLUGIN_DIR . 'behance-portfolio.php');
            $update = false;
            foreach ($plugins as $i => $plugin) {
                if ($plugin === $behance_portfolio) {
                    $plugins[$i] = false;
                    $update = true;
                }
            }

            if ($update) {
                update_option('active_plugins', array_filter($plugins));
            }
        }
        exit;
    }

}