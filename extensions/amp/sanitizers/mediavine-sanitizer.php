<?php
if (!class_exists('MVAMP_Sanitizer')) {
    class MVAMP_Sanitizer extends AMP_Base_Sanitizer
    {
        private $site_id;

        public function appendAdNodeBefore($afterNode)
        {
            $ad_wrapper = AMP_DOM_Utils::create_node($this->dom, 'div', array(
                'class' => 'mv-ad-wrapper'
            ));

            $ad_node = AMP_DOM_Utils::create_node($this->dom, 'amp-ad', array(
                'width' => 300,
                'height' => 250,
                'type' => 'mediavine',
                'data-site' => $this->site_id,
            ));

            $ad_wrapper->appendChild($ad_node);

            $afterNode->parentNode->insertBefore($ad_wrapper, $afterNode);
        }

        public function sanitize()
        {
            $this->site_id = $site_id = $this->args['site_id'];
            $ad_frequency = $this->args['ad_frequency'];
            $ad_offset = $this->args['ad_offset'];
            $disable_in_content = $this->args['disable_in_content'];
            $disable_sticky = $this->args['disable_sticky'];

            if (!isset($ad_frequency)) {
                $ad_frequency = 6;
            }

            if (!isset($ad_offset)) {
                $ad_offset = 6;
            }

            if (!isset($disable_in_content)) {
                $disable_in_content = FALSE;
            }

            if (!isset($disable_sticky)) {
                $disable_sticky = FALSE;
            }

            if (!$this->args['site_id']) {
                return;
            }

            $body = $this->get_body_node();

            if (TRUE !== $disable_sticky) {
                $sticky_node = AMP_DOM_Utils::create_node($this->dom, 'amp-sticky-ad', array(
                    'layout' => 'nodisplay'
                ));
                $sticky_inner = AMP_DOM_Utils::create_node($this->dom, 'amp-ad', array(
                    'data-site' => $site_id,
                    'type' => 'mediavine',
                    'width' => 320,
                    'height' => 50
                ));
                $sticky_node->appendChild($sticky_inner);
                $body->insertBefore($sticky_node, $body->firstChild);
            }

            if (TRUE !== $disable_in_content) {
                $p_nodes = $body->getElementsByTagName('p');
                if ($p_nodes->length > $ad_offset) {
                    for ($i = 0; $i < $p_nodes->length - 1; $i++) {
                        $offset = $i - $ad_offset;
                        if (0 <= $offset && 0 === $offset % $ad_frequency) {
                            $this->appendAdNodeBefore($p_nodes->item($i));
                        }
                    }
                } else if ($p_nodes->length > 0) {
                    $this->appendAdNodeBefore($p_nodes->item($p_nodes->length - 1));
                }
            }

            $this->replace_videos($body);
        }

        public function replace_videos($body)
        {
            $findpath = '//*[@id][@data-volume|@data-ratio]';
            $find = new DOMXPath($this->dom);

            $videos = $find->query($findpath);

            foreach ($videos as $index => $videoNode) {
                $id = $videoNode->getAttribute('id');
                $opts = $this->replace_video_options($id, $videoNode->getAttribute('data-volume'), $videoNode->getAttribute('data-ratio'));

                $placeholderOpts = $opts;
                $placeholderOpts['src'] = "https://scripts.mediavine.com/videos/{$id}/poster/{$opts['width']}/{$opts['height']}";
                $placeholderOpts['placeholder'] = 1;
                unset($placeholderOpts['sandbox']);
                unset($placeholderOpts['allowfullscreen']);
                unset($placeholderOpts['frameborder']);

                $replacement = AMP_DOM_Utils::create_node($this->dom, 'amp-iframe', $opts);
                $placeholder = AMP_DOM_Utils::create_node($this->dom, 'amp-img', $placeholderOpts);

                // Script is auto sanitized by the AMP plugin
                $replacement->appendChild($placeholder);
                $videoNode->parentNode->replaceChild($replacement, $videoNode);
            }
        }

        public function replace_video_options($id, $volume, $ratio = '16:9')
        {
            $ratio_parts = explode(':', $ratio);
            $width = ceil(intval($ratio_parts[0]) * 100 / 2);
            $height = ceil(intval($ratio_parts[1]) * 100 / 2);

            if ($width < 400) {
                $width = 400;
            }

            if ($height < 300) {
                $height = 300;
            }

            return array(
                'width' => $width,
                'height' => $height,
                'sandbox' => 'allow-scripts allow-same-origin',
                'layout' => 'responsive',
                'frameborder' => '0',
                'src' => "https://scripts.mediavine.com/videos/{$id}/iframe",
                'allowfullscreen' => null
            );
        }
    }
}
?>
