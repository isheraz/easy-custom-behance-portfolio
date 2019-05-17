<?php

class BehanceShortcode
{

    private $api_key;
    private $number_of_posts;
    private $username;

    public function __construct()
    {
        $this->api_key = 'somerandom#';
        $this->number_of_posts = get_option('bp_display_option_number');
        $this->username = get_option('bp_profile_option_username');

        add_shortcode('behanceportfolio', array('BehanceShortcode', 'ecpt_behanceportfolio'));
        add_action('wp_enqueue_scripts', array('BehanceShortcode', 'ecpt_plugin_scripts_styles'));
    }

    public static function ecpt_behanceportfolio($attr)
    {
        $posts = (new BehanceShortcode)->ecpt_get_posts_from_behance();
        $return = '<div class="container" id="portfolio-container">';
        $return .= '    <div class="row">';
        foreach ($posts as $post) {
            $return .= "\n" . '<article class="col-md-4 project more-projects mb-3" id="bp-' . $post['published_on'] . '">';
            $return .= "\n\r\t" . '<img class="img project-img" width="350" src="' . $post['covers']['max_808'] . '">' . "\n";
            $return .= "\n\r\t" . '<div class="project-info"><h5>' . str_replace('(COPY)', '', $post['name']) . '</h5>';
            $return .= "\n\r\t" . ' <p><a href="' . $post['url'] . '" class="bp-url btn-link">Details</a></p>   </div>' . "\n";
            $return .= "</article>";
        }
        $return .= '    </div>';
        $return .= '</div>';
        return $return;
    }

    function ecpt_get_posts_from_behance()
    {

        $posts = $this->ecpt_api_call("GET", "https://api.behance.net/v2/users/" . $this->username
            . "/projects?client_id=hrTy3jUSOr7t9gxltDXxWJMDyLYhqgXU");
//        print_r("<pre>".print_r($posts)."</pre>");
        $posts = json_decode($posts, true);
        if ($this->number_of_posts != 0) {
            $posts = array_splice($posts['projects'], 0, $this->number_of_posts);
        }
        return $posts;
    }

    function ecpt_api_call($method, $url, $data = false)
    {


        switch ($method) {
            case "POST":
               /* $args = array(
                    'body' => '',
                    'timeout' => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'cookies' => array()
                );
                wp_remote_post( $url, $args );*/
                break;
            case "PUT":

                break;
            default:
                if ($data){
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }

        $result = wp_remote_get($url);
        return $result['body'] ;
    }

    static function ecpt_plugin_scripts_styles()
    {

        wp_register_style('bp_styles', BEHANCE_PORTFOLIO_PLUGIN_URL . 'public/styles.css');
        wp_register_style('bootstrap-css', BEHANCE_PORTFOLIO_PLUGIN_URL . 'public/bootstrap.min.css');
        wp_enqueue_style('fontawesome');
        wp_enqueue_style('bootstrap-css');
        wp_enqueue_style('bp_styles');

        wp_register_script('bp_scripts', BEHANCE_PORTFOLIO_PLUGIN_URL . 'public/scripts.js', array('jquery'),
            BEHANCE_PORTFOLIO_VERSION, true);
        wp_enqueue_script('bootstrap-js');
        wp_enqueue_script('bp_scripts');
    }
}