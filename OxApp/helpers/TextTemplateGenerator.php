<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 14:04
 */

namespace OxApp\helpers;

/**
 * @file
 * Text generation from template.
 * Template example: {My|Your} {name|surname} is {Semen {Angarsky|Aleksandrov}|Alex|Dima}.
 *
 * @author http://www.angarsky.ru/
 */
class TextTemplateGenerator
{

    // Text template provided by user.
    private $template = '';
    // Text template prepared for generation.
    private $generator = '';
    // Array of replacements for text generator.
    private $replacements = array();
    // Symbol that used as replacement in generator text template.
    private $replace_symbol = 'RPLC';

    /**
     * Constructor.
     *
     * @param string $template
     *   Template for text generation.
     */
    function __construct($template = '')
    {
        $this->set_template($template);
    }

    /**
     * Sets text template for further generation.
     *
     * @param $template
     *   Template for text generation.
     */
    public function set_template($template)
    {
        $this->template = $template;
        $this->prepare($template);
    }

    /**
     * Starts text generation from template.
     *
     * @param $count
     *   Number of lines to generate.
     * @return string
     *   Generated text.
     */
    public function generate($count)
    {
        $result = array();
        for ($i = 0; $i < $count; $i++) {
            $result[] = $this->generate_line();
        }
        return $result;
    }

    /**
     * Prepares text template for generation.
     *
     * Converts text template to line with replacements.
     *
     * @param $template
     * @return string
     */
    private function prepare($template)
    {

        // Number of iteration for while loop.
        $i = 0;
        // ID of replacement in array.
        $rid = 0;
        $replacements = array();

        // Find all sets of {a|b|c}.
        while (preg_match_all("/{([^{}]+)}/", $template, $matches)) {

            foreach ($matches[1] as $match) {
                $rid++;
                $replace = "{" . $match . "}";
                $template = str_replace($replace, '_' . $this->replace_symbol . $rid . '_', $template);

                // Explode match to array.
                $elements = explode('|', $match);
                foreach ($elements as $element) {
                    $replacements[$rid][] = $element;
                }
            }

            // Prevents infinite loop.
            if ($i++ > 100) {
                $template = 'INFINITE LOOP!';
                break;
            }
        }

        $this->generator = $template;
        $this->replacements = $replacements;
    }

    /**
     * Generates single line of text from template array.
     *
     * @return string
     */
    private function generate_line()
    {
        $result = $this->process_replacements($this->generator);
        return $result;
    }

    /**
     * Recursively generates text.
     *
     * Replaces all replacement symbols in generator text template.
     *
     * @param $text - text with replacements to generate.
     */
    private function process_replacements($text)
    {
        preg_match_all('/_' . $this->replace_symbol . '([0-9]+)_/', $text, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $id) {
                if (isset($this->replacements[$id])) {
                    $rand = mt_rand(0, count($this->replacements[$id]) - 1);
                    $case = $this->replacements[$id][$rand];
                    $replace = $this->process_replacements($case);
                    $text = str_replace('_' . $this->replace_symbol . $id . '_', $replace, $text);
                }
            }
        }

        return $text;
    }
}
