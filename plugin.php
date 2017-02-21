<?php

class InstagramFeedPlugin extends KokenPlugin
{
	function __construct()
	{
        $this->register_hook('before_closing_head', 'instagram_css');
		$this->register_filter('site.output', 'instagram');
	}

    public function instagram_css() {
        echo '<style>
            .k-instagram-images {
                width: 100%;
                display: flex;
            }

            .k-instagram-images > * {
                flex: 1;
            }

            .k-instagram-images > * + * {
                margin-left: 4px;
            }

            .k-instagram-image {
                display: block;
                position: relative;
                padding-top: 100%;
            }

            .k-instagram-image img {
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                height: 100%;
                display: block;
                position: absolute;
                max-width: 100%
            }
        </style>';
    }

	public function instagram($content)
    {
        return preg_replace_callback(
            '/<(\s*?)instagram(.*?)>(.*?)<(\s*?)\/instagram(\s*?)>/',
            function($matches) {
                $tags = $matches[2];
                $user = str_replace(' ', '', preg_replace('/user="(.*?)"/', '$1', $tags));
                $feed = json_decode(file_get_contents('https://www.instagram.com/'.$user.'/media/'), true);
                $images = $feed['items'];
                $elements = '';
                $index = 0;

                foreach ($images as $image) {
                    if ($index == 6) {
                        break;
                    }

                    $elements .= '
                        <div>
                            <a class="k-instagram-image" href="'.$image['link'].'" target="_blank">
                                <img src="'.$image['images']['thumbnail']['url'].'" alt="'.$image['caption']['text'].'">
                            </a>
                        </div>';

                    $index++;
                }

                return '<div class="k-instagram">
                    <a class="k-instagram-title" href="https://www.instagram.com/'.$user.'">
                        Instagram
                    </a>
                    <div class="k-instagram-images">'.
                        $elements.'
                    </div>
                </div>';
            },
            $content);
	}
}
