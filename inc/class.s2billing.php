<?php

if(!defined('WPINC')) // MUST have WordPress.
    exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * s2Billing_hacks
 */
class s2Billing_hacks
{

    private static $instance;


    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    function __construct()  {


          add_action('wp_footer', [$this, 'hide_custom_fields'], 1000);

          add_action('template_redirect', function() {
            if( !current_user_can('administrator') ) return;

            if (empty($_GET['inspect_user'])) return;

            $user_meta = get_user_meta($_GET['inspect_user']);

            Kint::dump($user_meta);

            die();

          });


          add_action('template_redirect', [$this, 'payment_notification']);

          add_filter('ws_plugin__s2member_recaptcha_post_vars', [$this, 'ws_plugin__s2member_recaptcha_post_vars'], 10, 2);
    }


    public function hide_custom_fields() {

      ?>
      <script type="text/javascript">
      jQuery(document).ready(function($) {
        $("#s2member-pro-paypal-checkout-form-custom-reg-field-billing-tax-state-div").css('display', 'none');
        $("#s2member-pro-paypal-checkout-form-custom-reg-field-billing-tax-zip-div").css('display', 'none');
        $("#s2member-pro-paypal-checkout-form-custom-reg-field-billing-tax-country-div").css('display', 'none');
        $("#s2member-pro-paypal-checkout-form-custom-reg-field-billing-tax-street-div").css('display', 'none');
        $("#s2member-pro-paypal-checkout-form-custom-reg-field-billing-tax-city-div").css('display', 'none');
        $("#s2member-pro-paypal-checkout-form-custom-reg-field-billing-tax-street-divider-section").css('display', 'none');
        $("#s2member-pro-paypal-checkout-form-street-div").css('display', 'block');
        $("#s2member-pro-paypal-checkout-form-city-div").css('display', 'block');

        console.log($("#s2member-pro-paypal-checkout-form-street-div").css('display'));

        $('#s2member-pro-paypal-checkout-country').attr('disabled', 'disabled');

        $.get( "https://ipapi.co/json", function( data ) {
          $('#s2member-pro-paypal-checkout-country').val(data.country).trigger('change');
          let hidden_checkout_country = '<input type="hidden" id="s2member-pro-paypal-checkout-country" name="s2member_pro_paypal_checkout[country]" value="'+data.country+'"> ';
          $('#s2member-pro-paypal-checkout-country').parent("#s2member-pro-paypal-checkout-form-country-label").append(hidden_checkout_country);
        });



      })
      </script>
      <?php

    }

    public function ws_plugin__s2member_recaptcha_post_vars($post_vars, $defined_vars) {

      if (is_user_logged_in()) {
        $custom_fields = get_user_option('s2member_custom_fields', get_current_user_id());
        $custom_fields['billing_tax_street'] = $post_vars['street'];
        $custom_fields['billing_tax_city'] = $post_vars['city'];
        $custom_fields['billing_tax_state'] = $post_vars['state'];
        $custom_fields['billing_tax_zip'] = $post_vars['zip'];
        $custom_fields['billing_tax_country'] = $post_vars['country'];
        update_user_option(get_current_user_id(), 's2member_custom_fields', $custom_fields);
      }
      $post_vars['custom_fields']['billing_tax_state'] = $post_vars['state'];
      $post_vars['custom_fields']['billing_tax_zip'] = $post_vars['zip'];
      $post_vars['custom_fields']['billing_tax_country'] = $post_vars['country'];
      $post_vars['custom_fields']['billing_tax_street'] = $post_vars['street'];
      $post_vars['custom_fields']['billing_tax_city'] = $post_vars['city'];

      return $post_vars;
    }

    public function payment_notification()
    {
      if (empty($_GET['payment_notify'])) return;

      file_put_contents(s2tax_PLUGIN_DIR."tmp".DS.time().".txt", maybe_serialize($_GET));

    }

}


?>
