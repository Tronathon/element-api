<?php

namespace modules\base\twigextensions;

use Craft;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use yii\helpers\Inflector;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Base';
    }

    /**
     * @inheritdoc
     */
    public function getGlobals(): array
    {
        return [
            'doNotTrack' => $this->doNotTrackGlobal(),
            'currentUrl' => Craft::$app->getRequest()->getAbsoluteUrl(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ordinalize', [$this, 'ordinalize']),
            new TwigFilter('pluralize', [$this, 'pluralize']),
            new TwigFilter('singularize', [$this, 'singularize']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('version', [$this, 'version']),
        ];
    }

    /**
     * Detect if the DNT header is set.
     *
     * @return bool
     */
    public function doNotTrackGlobal()
    {
        return isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == 1;
    }

    /**
     * Converts number to its ordinal form.
     *
     * @param int $number
     *
     * @return string
     */
    public function ordinalize(int $number): string
    {
        return Inflector::ordinalize($number);
    }

    /**
     * Converts a word to its plural form.
     *
     * @param string $word
     * @param int    $number
     *
     * @return string
     */
    public function pluralize(string $word, int $number = 2): string
    {
        return abs($number) === 1 ? $word : Inflector::pluralize($word);
    }

    /**
     * Converts a word to its singular form.
     *
     * @param string $word
     * @param int    $number
     *
     * @return string
     */
    public function singularize(string $word, int $number = 1): string
    {
        return abs($number) === 1 ? Inflector::singularize($word) : $word;
    }

    /**
     * Version a static file by appending a query string based on a files
     * modification time.
     *
     * @param $filename
     * @param string $basePath
     * @return string
     */
    public function version($filename, $basePath = '@webroot'): string
    {
        $path = Craft::getAlias($basePath, false) . $filename;

        if (!file_exists($path)) {
           return $filename;
        }

        return $filename . '?v=' . filemtime($path);
    }
}
