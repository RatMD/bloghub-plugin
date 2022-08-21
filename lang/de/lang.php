<?php declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'RatMD BlogHub',
        'description' => 'Erweitert RainLab\'s Blog Plugin mist benutzerdefinierten Meta Feldern, Archivseiten und mehr.',
    ],

    'model' => [
        'tags' => [
            'label' => 'Schlagwörter',
            'slug' => 'Slug',
            'slugComment' => 'Slugs werden für die Archivseiten im Frontend verwendet.',
            'title' => 'Titel',
            'titleComment' => 'Unterstütztende Templates können den Titel anstelle des Slugs anzeigen.',
            'description' => 'Beschreibung',
            'descriptionComment' => 'Unterstützende Templates können die Beschreibung auf den Archivseiten anzeigen.',
            'promote' => 'Schlagwort bewerben',
            'promoteComment' => 'Unterstützende Templates können zu-bewerbende Schlagwörter speziell darstellen.',
            'color' => 'Farbe',
            'colorComment' => 'Unterstützende Templates können die gewählte Farbe zur Darstellung nutzen.',
            'posts' => 'Zugeordnete Beiträge',
            'postsComment' => 'Die einzelnen Beiträge die diesem Schlagwort zugeordnet sind.',
            'postsEmpty' => 'Keine Beiträge verfügbar.',
            'postsNumber' => 'Beitragsanzahl'
        ],
        'users' => [
            'displayName' => 'Anzeigename',
            'displayNameComment' => 'Eine eigene Version deines Namens, unterstützende Templates können diesen an deinen Beiträgen anzeigen.',
            'authorSlug' => 'Autorenslug',
            'authorSlugComment' => 'Autorenslugs werden für die Archivseiten anstelle des Loginnamens verwendet.',
            'aboutMe' => 'Über Mich',
            'aboutMeDescription' => 'Eine kleine Beschreibung über dich selbst, unterstützende Templates können diesen bei deinen Beiträgen anzeigen.'
        ],
        'visitors' => [
            'views' => 'Aufrufe / Einzigartig'
        ]
    ],

    'components' => [
        'authors_title' => 'Beiträge eines Autoren',
        'authors_description' => 'Zeigt eine Liste von Beiträgen eines Autorens an.',
        'author_filter' => 'Autoren-Filter',
        'author_filter_description' => 'Giv einen Slug oder Login-Namen eines Autoren oder einen URL Parameter an um die Beiträge zu filtern.',
        'dates_title' => 'Beiträge eines Datums',
        'dates_description' => 'Zeigt eine Liste von Beiträgen anhand eines Datums.',
        'date_filter' => 'Datums-Filter',
        'date_filter_description' => 'Gib ein spezielles Datum (Jahr, Monat und/oder Tag) oder einen URL Parameter an um die Beiträge zu filtern..',
        'tag_title' => 'Beiträge nach Schlagwort',
        'tag_description' => 'Zeigt eine Liste von Beiträgen anhand eines Schlagwortes.',
        'tag_filter' => 'Schagwort-Filter',
        'tag_filter_description' => 'Gi einen Schlagwort-Slug oder URL Parameter an um die Beiträge zu filtern..',
        'tags_title' => 'Schalgwort-Lsite',
        'tags_description' => 'Zeigt eine Liste von beliebten Schlagwörtern an.',
        'tags_page' => 'Schlagwort-Seite',
        'tags_page_description' => 'Der Name der Schlagwort CMS Seite für die Verlinkung.',
    ],

    'sorting' => [
        'bloghub_views_asc' => 'Aufrufe (aufsteigend)',
        'bloghub_views_desc' => 'Aufrufe (absteigend)',
        'bloghub_unique_views_asc' => 'Einzigartige Aufrufe (aufsteigend)',
        'bloghub_unique_views_desc' => 'Einzigartige Aufrufe (absteigend)'
    ],

    'settings' => [
        'defaultTab' => 'Meta Daten',
        'label' => 'Benutzerdefinierte Meta-Daten',
        'description' => 'Verwalte die globalen benutzerdefinierten Meta-Daten für deine Beiträge.',
        'prompt' => 'Ein neues Meta-Feld hinzufügen',

        'hint' => [
            'label' => 'Benutzerdefiniere Meta-Namen müssen einzigartig sein',
            'comment' => 'Die hier konfigurierten benutzerdefinierten Meta-daten werden von den - in der theme.yaml gesetzten Werten - überschrieben. Achte daher auf die Vergabe von einzigarten Namen.'
        ],
        'name' => [
            'label' => 'Meta Feldname',
            'comment' => 'Der Meta Feldname auf dem im Frontend zugegriffen werden kann.'
        ],
        'type' => [
            'label' => 'Meta Feldtyp',
            'comment' => 'Der Meta Feldtyp bestimmt die Ausgabe im Backend.'
        ],
        'config' => [
            'label' => 'Meta Feld-Konfiguration',
            'comment' => 'Trage hier deine benutzerdefinierte Konfiguration ein. Die Dokumentation findest du <a href="https://docs.octobercms.com/3.x/element/form/widget-taglist.html" target="_blank">bei OctoberCMS Docs</a>.'
        ],
        'types' => [
            'text' => 'Text Field',
            'number' => 'Number Field',
            'password' => 'Password Field',
            'email' => 'E-Mail Field',
            'textarea' => 'Textarea Field',
            'dropdown' => 'Dropdown Selector',
            'radio' => 'Radio Field',
            'balloon' => 'Balloon Selector',
            'checkbox' => 'Checkbox Field',
            'checkboxlist' => 'Checkbox List',
            'switch' => 'Switch Button',
            'codeeditor' => 'Code Editor',
            'colorpicker' => 'Color Picker',
            'datepicker' => 'Date/Time Picker',
            'fileupload' => 'File Upload Field',
            'markdown' => 'Markdown Editor',
            'mediafinder' => 'Media Finder',
            'richeditor' => 'Rich WYSIWYG Editor',
            'taglist' => 'Tag List',
        ]
    ]
];
