<?php

class MVAMP_iFrame_Embed extends AMP_Base_Embed_Handler {
    public function register_embed() {
    }

    public function unregister_embed() {
    }

    public function get_scripts() {
        return array( 'amp-iframe' => 'https://cdn.ampproject.org/v0/amp-iframe-0.1.js' );
    }
}

?>
