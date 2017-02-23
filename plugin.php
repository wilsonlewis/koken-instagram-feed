<?php

class InstagramFeedPlugin extends KokenPlugin
{
    /**
     * Template for rendering a full Instagram feed.
     *
     * @var string
     */
    protected $feedTemplate;

    /**
     * Template for rendering an Instagram image.
     *
     * @var string
     */
    protected $imageTemplate;

    /**
     * A pattern to find and replace 'instagram' tags.
     *
     * @var string
     */
    protected $instagramTag;

    /**
     * Create a new Instagram Feed Plugin instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->feedTemplate = file_get_contents(__DIR__.'/assets/views/feed.html');

        $this->imageTemplate = file_get_contents(__DIR__.'/assets/views/image.html');

        $this->instagramTag = '/<(\s*?)instagram(.*?)>(.*?)<(\s*?)\/instagram(\s*?)>/';

        $this->register_hook('before_closing_head', 'insertInstagramStyles');
        
        $this->register_filter('site.output', 'insertInstagramFeed');
    }

    /**
     * Append default styles to the <head> tag.
     *
     * @return void
     */
    public function insertInstagramStyles()
    {
        echo '<style>'.file_get_contents(__DIR__.'/assets/css/main.css').'</style>';
    }

    /**
     * Replace any 'instagram' tags found in a page before it is rendered.
     *
     * @see   $this->replaceInstagramTag()
     *
     * @param  string $content
     *
     * @return string
     */
    public function insertInstagramFeed($content)
    {
        return preg_replace_callback($this->instagramTag, array($this, 'replaceInstagramTag'), $content);
    }

    /**
     * Replaces any 'instagram' tag found in html markup with a feed of most recent images.
     *
     * Example: '<instagram username="name" count="4"></instagram>'
     *
     * @param  array $matches
     *
     * @return string
     */
    protected function replaceInstagramTag($matches)
    {
        $attributes = (new SimpleXMLElement('<element '.$matches[2].' />'))->attributes();
        
        if ($feed = $this->getFeedForUsername($attributes['username'])) {
            return $this->createFeedTag($feed, $attributes['images']);
        }
        
        return '';
    }

    /**
     * Retrieves and converts a user's Instagram feed to an array.
     *
     * @param  string|null $username
     *
     * @return array|null
     */
    protected function getFeedForUsername($username = null)
    {
        if ($username = $username ?? $this->data->username) {
            return json_decode(file_get_contents("https://www.instagram.com/{$username}/media/"), true);
        }
    }

    /**
     * Creates an html tag for using data from an Instagram feed.
     *
     * @param  array $feed
     * @param  integer $count
     *
     * @return string
     */
    protected function createFeedTag($feed, $count = null)
    {
        return $this->replaceTemplateVariables($this->feedTemplate, [
            'images' => $this->createImageTags($feed['items'], $count),
            'username' => $feed['items'][0]['user']['username'],
        ]);
    }

    /**
     * Creates multiple html tags using data from an Instagram feed.
     *
     * @see    $this->createImageTag()
     *
     * @param  array $images
     * @param  integer $count
     *
     * @return string
     */
    protected function createImageTags($images, $count = null)
    {
        $images = array_slice($images, 0, $count ?: $this->data->count, true);

        $imageViews = array_map(function ($image) {
            return $this->createImageTag($image);
        }, $images);

        return join('', $imageViews);
    }

    /**
     * Creates a html tag using data from an Instagram feed image.
     *
     * @param  array $image
     *
     * @return string
     */
    protected function createImageTag($image)
    {
        return $this->replaceTemplateVariables($this->imageTemplate, [
            'src' => $image['images']['thumbnail']['url'],
            'link' => $image['link'],
            'title' => $image['caption']['text'],
        ]);
    }

    /**
     * Replaces multiple variables in a template with values.
     *
     * @see    $this->replaceTemplateVariable()
     *
     * @param  string $template
     * @param  array $variables
     *
     * @return string
     */
    protected function replaceTemplateVariables($template, $variables)
    {
        foreach ($variables as $name => $value) {
            $template = $this->replaceTemplateVariable($template, $name, $value);
        }

        return $template;
    }

    /**
     * Replaces variables in a template with values using handlebar syntax.
     *
     * Example: 'Hello, {{ name }}!', 'name', 'world'
     *
     * Result: 'Hello, world!'
     *
     * @param  string $template
     * @param  array $variables
     *
     * @return string
     */
    protected function replaceTemplateVariable($template, $name, $value)
    {
        return preg_replace('/\{\{(\s*?)'.$name.'(\s*?)\}\}/', $value, $template);
    }
}
