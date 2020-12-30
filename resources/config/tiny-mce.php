<?php

return [
    /**
     * Attributs de configuration de tinyMCE.
     * @see https://www.tiny.cloud/docs/configure/
     */
    'init'    => [
        /**
         * Personnalisation de la première ligne de la barre d'outils.
         */
        'toolbar1' => 'bold italic underline strikethrough blockquote ' .
            '| alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ' .
            '| . link unlink hr | dfw wp_adv',

        /**
         * Personnalisation de la deuxième ligne de la barre d'outils.
         */
        'toolbar2' => 'undo redo | pastetext | styleselect formatselect ' .
            '| fontselect fontsizeselect forecolor backcolor | removeformat | subscript superscript charmap',

        'textcolor_map' => [
            '000000',
            __('Noir', 'tify'),
            'FFFFFF',
            __('Blanc', 'tify'),
            '3B3B3B',
            __('Police standard', 'tify')
        ],

        'block_formats' => 'Paragraphe=p;Paragraphe sans espace=div;Titre 3=h3;Titre 4=h4;Titre 5=h5;Titre 6=h6',

        'font_formats' => 'Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;',

        'fontsize_formats' => '10px 11px 12px 13px 14px 16px 18px 20px 24px 28px 32px 36px 40px 44px 48px 52px 64px '.
            '128px 256px',

        'style_formats' => [
            [
                'title'    => __('Alignement à  Gauche', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'alignleft'
            ],
            [
                'title'    => __('Alignement au Centre', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'aligncenter'
            ],
            [
                'title'    => __('Alignement à  Droite', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'alignright'
            ],
            [
                'title'    => __('Alignement vertical en haut', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'aligntop'
            ],
            [
                'title'    => __('Alignement vertical au milieu', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'alignmiddle'
            ],
            [
                'title'    => __('Alignement vertical en bas', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'alignbottom'
            ],
            [
                'title'   => __('Texte en majuscules', 'tify'),
                'inline'  => 'span',
                'classes' => 'uppercase'
            ],
            [
                'title'    => __('Bouton #1', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--1'
            ],
            [
                'title'    => __('Bouton #1 alternatif', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--1 Button--alt'
            ],
            [
                'title'    => __('Bouton #2', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--2'
            ],
            [
                'title'    => __('Bouton #2 alternatif', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--2 Button--alt'
            ],
            [
                'title'    => __('Bouton 3', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--2'
            ],
            [
                'title'    => __('Bouton #3 alternatif', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--3 Button--alt'
            ],
            [
                'title'    => __('Etiquette #1', 'tify'),
                'selector' => 'p, span',
                'classes'  => 'Label--1'
            ],
            [
                'title'    => __('Etiquette #1 alternative', 'tify'),
                'selector' => 'p, span',
                'classes'  => 'Label--1 Label--alt'
            ],
            [
                'title'    => __('Etiquette #2', 'tify'),
                'selector' => 'p, span',
                'classes'  => 'Label--2'
            ],
            [
                'title'    => __('Etiquette #2 alternative', 'tify'),
                'selector' => 'p, span',
                'classes'  => 'Label--2 Label--alt'
            ],
            [
                'title'    => __('Etiquette #3', 'tify'),
                'selector' => 'p, span',
                'classes'  => 'Label--3'
            ],
            [
                'title'    => __('Etiquette #3 alternative', 'tify'),
                'selector' => 'p, span',
                'classes'  => 'Label--3 Label--alt'
            ]
        ]
    ],
    'plugins' => [
        /**
         * Intégration de la police de glyphs Wordpress (dashicons).
         * @see https://developer.wordpress.org/resource/dashicons
         */
        'dashicons',

        /**
         * Intégration de la police de glyphs Fontawesome.
         * @see https://fontawesome.com/icons?d=gallery
         */
        'fontawesome',

        /**
         * Activation du saut de ligne automatique.
         * {@internal Chargement avec webpack : "import 'presstify-plugins/tiny-mce/Resources/assets/jumpline';"}
         */
        'jumpline',

        /**
         * Intégration d'une police de glyphs personnelle.des scripts
         * @see http://fontastic.me/
         * @see http://fontello.com/
         * @see https://icomoon.io/app/#/select
         */
        /*'ownglyphs' => [
            'hookname'           => '{{ example }}',
            'path'               => get_stylesheet_directory_uri() . '/src/fonts/{{ example }}/styles.css',
            'version'            => '1.0',
            'dependencies'       => [],
            'prefix'             => '{{ example }}',
            'font-family'        => '{{ ExampleFontFamily }}',
            'button'             => 'logo',
            'title'              => __('Police de glyphs d\'exemple', 'tify'),
            'cols'               => 8
        ],*/

        /**
         * Gestion des tableaux.
         * @see https://www.tiny.cloud/docs/plugins/table/
         */
        'table',

        /**
         * Gabarits d'affichage préformatés.
         * @see https://www.tiny.cloud/docs/plugins/template/
         * {@internal Chargement avec webpack : "import 'presstify-plugins/tiny-mce/Resources/assets/template';"}
         */
        'template',

        /**
         * Asistance visuel d'affichage des blocs de saisie.
         * @see https://www.tiny.cloud/docs/plugins/visualblocks/
         */
        'visualblocks'
    ]
];