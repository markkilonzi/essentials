<?php

namespace Nyumbani\Essentials\Docs;

use Nyumbani\Docs\Docs;

/**
 * The docs of Essentials: its pages, grouped by the concept they explain, and the demos they run
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class EssentialsDocs
{
    /**
     * Describe the docs of Essentials, which load the app's own styles and scripts because their examples run real components
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public static function describe(): Docs
    {
        $demos = new EssentialsDemos;

        return (new Docs('nyumbani-essentials', 'Essentials', [
            ['title' => 'Getting started', 'pages' => [
                'introduction' => 'Introduction',
                'installation' => 'Installation',
                'quick-start' => 'Quick start',
                'configuration' => 'Configuration',
            ]],
            ['title' => 'Foundation', 'pages' => [
                'defaults' => 'App defaults',
                'helpers' => 'Helpers',
                'period' => 'Period filter',
                'validation' => 'Validation',
                'error-bags' => 'Error bags',
                'javascript' => 'JavaScript',
                'logging' => 'Logging to Discord',
                'messaging' => 'Messaging',
                'daraja' => 'Daraja',
                'dt-tables' => 'Data tables',
            ]],
            ['title' => 'Users and models', 'pages' => [
                'authentication' => 'Authentication',
                'permissions' => 'Roles and permissions',
                'traits' => 'Traits',
            ]],
            ['title' => 'Interface', 'pages' => [
                'layouts' => 'Layouts',
                'theme' => 'Theme and buttons',
                'modal' => 'Modal',
                'alerts-and-loader' => 'Alerts and loader',
                'error-pages' => 'Error pages',
            ]],
            ['title' => 'Conventions', 'pages' => [
                'rules-and-skills' => 'Rules and skills',
                'anatomy' => 'Anatomy of a feature',
                'code-style' => 'Code style',
                'app-code' => 'Application code',
                'http-layer' => 'HTTP layer',
                'data-layer' => 'Models and data',
                'view-layer' => 'Views and scripts',
            ]],
        ], 'essentials::docs.pages'))
            ->withAppAssets('essentials::docs.head', 'essentials::docs.foot', ['data-currency' => config('essentials.currency')])
            ->demo('period', $demos->period(...))
            ->demo('money', $demos->money(...))
            ->demo('dates', $demos->dates(...))
            ->demo('text', $demos->text(...))
            ->demo('phone-rules', $demos->phoneRules(...))
            ->demo('clean-name', $demos->cleanName(...))
            ->demo('kenyans', $demos->kenyans(...))
            ->demo('model-traits', $demos->modelTraits(...))
            ->demo('discord', $demos->discord(...));
    }
}
