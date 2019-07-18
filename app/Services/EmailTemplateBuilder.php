<?php

namespace App\Services;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class EmailTemplateBuilder
{
    protected $twig;
    protected $inliner;

    public function __construct()
    {
        $this->twig = new Environment(new ArrayLoader());
        $this->inliner = new CssToInlineStyles();
    }

    public function build($mjml)
    {
        // Simple MJML to HTML conversion (basic implementation)
        $html = $this->convertMjmlToHtml($mjml);
        return $html;
    }

    protected function convertMjmlToHtml($mjml)
    {
        // Basic MJML conversion - production would use actual MJML compiler
        $html = str_replace('<mj-body>', '<body>', $mjml);
        $html = str_replace('</mj-body>', '</body>', $html);
        $html = str_replace('<mj-section>', '<table width="100%"><tr><td>', $html);
        $html = str_replace('</mj-section>', '</td></tr></table>', $html);
        $html = str_replace('<mj-text>', '<div>', $html);
        $html = str_replace('</mj-text>', '</div>', $html);

        return $this->wrapInEmailTemplate($html);
    }

    protected function wrapInEmailTemplate($content)
    {
        $css = $this->getDefaultStyles();

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>{$css}</style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center">
                {$content}
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    protected function getDefaultStyles()
    {
        return <<<CSS
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}
table {
    border-collapse: collapse;
}
.container {
    max-width: 600px;
    margin: 0 auto;
    background-color: #ffffff;
}
.header {
    background-color: #333333;
    color: #ffffff;
    padding: 20px;
    text-align: center;
}
.content {
    padding: 30px;
    color: #333333;
}
.button {
    display: inline-block;
    padding: 12px 30px;
    background-color: #007bff;
    color: #ffffff;
    text-decoration: none;
    border-radius: 4px;
}
.footer {
    padding: 20px;
    text-align: center;
    font-size: 12px;
    color: #666666;
}
CSS;
    }

    public function render($template, array $variables = [])
    {
        $twigTemplate = $this->twig->createTemplate($template);
        $html = $twigTemplate->render($variables);

        return $this->inlineStyles($html);
    }

    protected function inlineStyles($html)
    {
        preg_match('/<style>(.*?)<\/style>/s', $html, $matches);
        $css = $matches[1] ?? '';

        $htmlWithoutStyle = preg_replace('/<style>.*?<\/style>/s', '', $html);

        return $this->inliner->convert($htmlWithoutStyle, $css);
    }

    public function exportHtml($template, $outputPath)
    {
        file_put_contents($outputPath, $template);
        return $outputPath;
    }
}
