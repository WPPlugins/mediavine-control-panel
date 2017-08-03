<?php
$setting_prefix = $this->SETTING_PREFIX;
$amp = $this->get_extension('amp');
$security = $this->get_extension('security');
?>
<style>
    .option-group {
        width: 80%;
        margin-left: 2%;
    }

    .mvoption {
        clear: both;
        width: 100%;
        border: 1px solid #333;
        border-top: none;
        box-sizing: border-box;
        padding: 25px;
    }

    .mvoption:first-of-type {
        border-top: 1px solid #333;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .mvoption:last-of-type {
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .option-group > .notice {
        margin-bottom: 15px;
    }

    .mvoption .option {
        display: inline-block;
        max-width: 50%;
        min-width: 300px;
    }

    .mvoption .description {
        display: inline-block;
        width: 49%;
        font-style: italic;
    }

    .mvoption > div {
        vertical-align: text-top;
    }

    .mvoption .tag, .option-group h3 > .tag {
        font-size: 11px;
        background: #ff0000;
        display: inline-block;
        color: #fff;
        border-radius: 2px;
        padding: 2px 10px;
        margin-left: 10px;
        box-shadow: -2px 2px #990000, 0px 1px #990000, -1px 0px #990000;
    }

    .mvoption label {
        font-size: 16px;
    }

    .mvoption label.opt {
        display: block;
        margin-left: 1px;
        cursor: default;
    }

    .mvoption input[type=number] {
        width: 50px;
    }

    .mvoption input[type=text], .mvoption input[type=number] {
        border-radius: 1px;
        line-height: 24px;
        margin-top: 5px;
    }

</style>
<form method="post" action="options.php">
    <?php @settings_fields($setting_prefix); ?>
    <?php @do_settings_sections($setting_prefix); ?>
    <h2 class="mv-head">Mediavine Settings</h2>
    <hr/>
    <div class="option-group">
        <h3>General Settings</h3>

        <section class="mvoption">
            <div class="option">
                <label class="opt">Mediavine Site Id &nbsp;</label>
                <input type="text" name="<?php echo $this->get_key('site_id'); ?>"
                       value="<?php echo $this->option('site_id'); ?>"/>
            </div>
            <div class="description">
                The unique identifier Mediavine has given your blog. This can be found in the Ad Setup tab of your <a
                    href="https://dashboard.mediavine.com" target="_blank">Mediavine Dashboard,</a> and will look like
                this: food-fanatic, my-baking-addiction, etc
            </div>
        </section>
        <section class="mvoption">
            <div class="option">
                <input id="<?php echo $this->get_key('include_script_wrapper'); ?>"
                       name="<?php echo $this->get_key('include_script_wrapper'); ?>"
                    <?php checked("true" == $this->option('include_script_wrapper')); ?> value=true
                       type="checkbox"/>
                &nbsp;<label for="<?php echo $this->get_key('include_script_wrapper'); ?>">Include Script
                    Wrapper</label>
            </div>
            <div class="description">
                Automatically include the mediavine script wrapper into the header of your blog. This should only be
                done if you are a new member to mediavine and do not have the script wrapper installed
            </div>
        </section>
    </div>
    <?php if ($amp->hasAMP()) { ?>
        <div class="option-group">
            <h3>AMP Settings</h3>
            <?php if ($this->hasAMPForWP()) { ?>
                <div class="notice notice-error">
                    <p>
                        It looks like you're using AMP For WP. While we're trying our best to support this plugin,
                        things
                        may not work as expected.<br/>
                        If you notice anything out of place, please contact <a href="mailto:publishers@mediavine.com">publishers@mediavine.com</a>
                    </p>
                </div>
            <?php } ?>
            <section class="mvoption">
                <div class="option">
                    <label class="opt">Ad Frequency</label>
                    <input type="number" name="<?php echo $amp->get_key('ad_frequency'); ?>"
                           value="<?php echo $amp->option('ad_frequency'); ?>"/>
                    <br/>
                </div>
                <div class="description">
                    The Amount of paragraphs between each ad on AMP articles. Should ideally be between 4 and 8
                </div>
            </section>
            <section class="mvoption">
                <div class="option">
                    <label class="opt">Ad Offset</label>
                    <input type="number" name="<?php echo $amp->get_key('ad_offset'); ?>"
                           value="<?php echo $amp->option('ad_offset'); ?>"/>
                    <br/>
                </div>
                <div class="description">
                    The amount of paragraphs before the first ad on an AMP article. This should be at least 6.
                </div>
            </section>
            <section class="mvoption">
                <div class="option">
                    <input id="<?php echo $amp->get_key('disable_in_content'); ?>"
                           name="<?php echo $amp->get_key('disable_in_content'); ?>"
                        <?php checked("true" == $amp->option('disable_in_content')); ?> value=true type="checkbox"/>
                    &nbsp;<label for="<?php echo $amp->get_key('disable_in_content'); ?>">Disable in-content Ads</label>
                </div>
                <div class="description">
                    <p>Turns off in content AMP ads</p>
                </div>
            </section>
            <section class="mvoption">
                <div class="option">
                    <input id="<?php echo $amp->get_key('disable_sticky'); ?>"
                           name="<?php echo $amp->get_key('disable_sticky'); ?>"
                        <?php checked("true" == $amp->option('disable_sticky')); ?> value=true type="checkbox"/>
                    &nbsp;<label for="<?php echo $amp->get_key('disable_sticky'); ?>">Disable AMP Adhesion</label>
                </div>
                <div class="description">
                    <p>Disables Adhesion units on AMP pages</p>
                </div>
            </section>
            <section class="mvoption">
                <div class="option">
                    <input id="<?php echo $amp->get_key('disable_amphtml_link'); ?>"
                           name="<?php echo $amp->get_key('disable_amphtml_link'); ?>"
                        <?php checked("true" == $amp->option('disable_amphtml_link')); ?> value=true type="checkbox"/>
                    &nbsp;<label for="<?php echo $amp->get_key('disable_amphtml_link'); ?>">Disable AMP Links</label>
                </div>
                <div class="description">
                    <span class="tag">Only enable this if you are specifically instructed to do so by Mediavine Publisher Support.</span>
                    <p>Disables linking amp versions of pages so search engines stop crawling the page</p>
                </div>
            </section>
        </div>
        <div class="option-group">
            <h3>AMP Analytics Settings</h3>
            <section class="mvoption">
                <?php if ($this->hasAMPForWP()) { ?>
                    <div class="option disabled">
                        Enable AMP Google Analytics
                    </div>
                    <div class="description error">
                        Please use the Analytics settings in AMP For WP
                    </div>
                    <div class="description">

                    </div>
                <?php } else { ?>
                    <div class="option">
                        <input id="<?php echo $amp->get_key('use_analytics'); ?>"
                               name="<?php echo $amp->get_key('use_analytics'); ?>"
                            <?php checked("true" == $amp->option('use_analytics')); ?> value=true type="checkbox"/>
                        &nbsp;<label for="<?php echo $amp->get_key('use_analytics'); ?>">Enable AMP Google Analytics</label>
                    </div>
                    <div class="description">
                        Insert Google Analytics on AMP Pages
                    </div>
                <?php } ?>
            </section>
            <section class="mvoption">
                <div class="option">
                    <label class="opt">UA Code</label>
                    <input type="text" name="<?php echo $amp->get_key('ua_code'); ?>"
                           value="<?php echo $amp->option('ua_code'); ?>"/>
                </div>
                <div class="description">
                    Your Google Analytics UA Code
                </div>
            </section>
        </div>
    <?php } ?>
    <div class="option-group">
        <h3>Security Settings<span class="tag">Experimental</span></h3>
        <section class="mvoption">
            <div class="option">
                <input id="<?php echo $security->get_key('enable_forced_ssl'); ?>"
                       name="<?php echo $security->get_key('enable_forced_ssl'); ?>"
                    <?php checked("true" == $security->option('enable_forced_ssl')); ?> value=true type="checkbox"/>
                &nbsp;<label for="<?php echo $security->get_key('enable_forced_ssl'); ?>">Force Secure Assets</label>
            </div>
            <div class="description">
                Force all scripts, images and advertisements on your page to be loaded over https. <strong>Only enable
                    if your blog is running SSL</strong>
            </div>
        </section>
    </div>
    <?php @submit_button(); ?>
</form>
