<?php declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'BlogHub von rat.md',
        'description' => 'Erweitert RainLab.Blog mit Kommentaren, Schlagwörtern, Meta-Feldern, Archiven, Statistiken, Klickzähler und mehr.',
    ],
    

    'components' => [
        'bloghub_group' => 'BlogHub Einstellungen',

        'base' => [
            'label' => 'Basis Konfiguration',
            'comment' => 'Die Basis BlogHub Konfiguration, sollte im besten Fall in den CMS Layouts eingesetzt werden.',
            'archive_author' => 'Autor CMS Seite',
            'archive_author_comment' => 'Name der CMS Seite die für die Autenarchive genutzt wird.',
            'archive_date' => 'Datums CMS Seite',
            'archive_date_comment' => 'Name der CMS Seite die für die Datumsarchive genutzt wird.',
            'archive_tag' => 'Schlagwort CMS Seite',
            'archive_tag_comment' => 'Name der CMS Seite die für die Schlagwortarchive genutzt wird..',
            'author_slug' => 'Nur Autorenslug verwenden',
            'author_slug_comment' => 'Nutzt lediglich das author_slug Nutzerfeld.',
            'date_invalid' => '404 wenn ungültig',
            'date_invalid_comment' => 'Zeigt die 404 Fehlerseite bei ungültigen Daten.',
            'date_empty' => '404 wenn Leer',
            'date_empty_comment' => 'Zeigt die 404 Fehlerseite bei leeren Datumsarchiven.',
            'tag_multiple' => 'Mehrere Schlagwörter',
            'tag_multiple_comment' => 'Erlaube die Kombination mehrerer Tags mit dem + oder , Zeichen bei den Schlagwortarchiven.',
        ],
        'author' => [
            'label' => 'Beiträge nach Autor',
            'comment' => 'Zeigt eine Liste von Beiträgen nach dem Autoren an.',
            'filter' => 'Autorenfilter',
            'filter_comment' => 'Gib einen Autorenslug oder einen URL Parameter an um die Beiträge zu filtern.',
        ],
        'comment_count' => [
            'label' => 'Beiträge nach Kommentare',
            'comment' => 'Zeigt eine Liste von Beiträgen nach der Anzahl der Kommentare.'
        ],
        'comments_list' => [
            'label' => 'Kommentarliste',
            'comment' => 'Zeigt eine Liste von Kommentaren auf der Seite an.',
            'exclude_posts' => 'Beiträge ausnehmen',
            'exclude_posts_description' => 'Eine Komma-getrennte Liste von BeitragsIds oder Slugs die ausgenommen werden sollen.',
            'amount' => 'Anzahl der Kommentare',
            'amount_description' => 'Bestimme die Anzahl der Kommentare die angezeigt werden soll.',
            'amount_validation' => 'Die Angaben zur Anzahl der Kommentare ist ungültig.',
            'only_favorites' => 'Nur Favoriten anzeigen',
            'only_favorites_description' => 'Zeigt nut Kommentare an die von dem Autoren als Favorit festgelegt wurden.',
            'default_tab' => 'Standard-Tab',
            'default_tab_comment' => 'Der Standard-Tab der bei dem Widget vorausgewählt werden soll.'
        ],
        'comments_section' => [
            'label' => 'Kommentarbereich',
            'comment' => 'Zeigt einen Kommentarbereich mit Formular auf der Beitragsseite an.',
            'group_form' => 'Formular',
            'post_slug' => 'Beitragsfilter',
            'post_slug_comment' => 'Gib einen Beitragsslug oder URL Parameter an um die Kommentare entsprechend zu filtern.',
            'comments_per_page' => 'Kommentare / Seite',
            'comments_order' => 'Sortierung',
            'comments_order_comment' => 'Wert nachdem die Kommentare sortiert werden sollen.',
            'comments_hierarchy' => 'Hierarchisch anzeigen',
            'comments_hierarchy_comment' => 'Zeigt Antworten entweder hierarchisch unter den Kommentar an oder flach übereinander mit Zitat.',
            'comments_anchor' => 'Container Anker',
            'comments_anchor_comment' => 'Die ID des Hauptcontainers des Kommentarbereichs der bei den Pagination Links verwendet wird,',
            'pin_favorites' => 'Favoriten anpinnen',
            'pin_favorites_comment' => 'Vom Autor festgelegte Favoriten werden oben in der Liste angezeigt.',
            'hide_on_dislike' => 'Ungemochte verstecken',
            'hide_on_dislike_comment' => 'Versteckt ungemochte Kommentare, entweder per absolute Zahl oder - mit Doppelpunkt angefüht - Relation zum Like Verhältnis.',
            'form_position' => 'Formular Position',
            'form_position_comment' => 'Verändere die Position des Formulars im Kommentarbereich.',
            'form_position_above' => 'Über den Kommentaren',
            'form_position_below' => 'Unter den Kommentaren',
            'disable_form' => 'Formular deaktivieren',
            'disable_form_comment' => 'Deaktiviert den Formularbereich unabhängig der gewählten Beitragsoption.',
        ],
        'date' => [
            'label' => 'Beiträge nach Datum',
            'comment' => 'Zeigt eine Liste von Beiträgen nach Datum.',
            'filter' => 'Datumsfilter',
            'filter_comment' => 'Gib ein spezifisches Datum oder einen URL Parameter um um die Beiträge zu filtern.',
        ],
        'post' => [
            'date_range' => 'Standard Interval',
            'date_range_comment' => 'Ändere den vorselektierten Datumsinterval der bei den Graphen verwendet werden soll.',
            '7days' => 'Letzten 7 Tage',
            '14days' => 'Letzten 14 Tage',
            '31days' => 'Letzten 31 Tage',
            '3months' => 'Letzten 3 Monate',
            '6months' => 'Letzten 6 Monate',
            '12months' => 'Letzten 12 Monate',
            'views_visitors' => 'Aufrufe / Besucher',
            'views' => 'Aufrufe',
            'visitors' => ' Besucher',
            'published_posts' => 'Veröffentlichte Beiträge',
            'default_order' => 'Standard-Sortierung',
            'default_order_comment' => 'Verändere die Standard-Sortierung die bei der Beitragsliste genutzt werden soll.',
            'by_published' => 'Nach Veröffntlichungsdatum',
            'by_views' => 'Nach Aufrufen',
            'by_visitors' => 'Nach Besuchern',
            'total' => 'Gesamten Beiträge',
            'published' => 'Veröffentlicht',
            'scheduled' => 'Geplant',
            'draft' => 'Entwurf',
            'posts_list' => 'Beitragsliste',
        ],
        'tag' => [
            'label' => 'Beiträge nach Schlagwort',
            'comment' => 'Zeigt eine Liste von Beiträgen nach Schlagwort an.',
            'filter' => 'Schlagwortfilter',
            'filter_comment' => 'Gib ein Schlagwort oder einen URL Parameter an um die Beiträge zu filtern.',
        ],
        'tags' => [
            'label' => 'Schlagwort-Liste',
            'comment' => 'Zeigt eine Liste von (beworbenen) Schalgwörtern an.',
            'tags_page' => 'Schlagwort-Archiveseite',
            'tags_page_comment' => 'Name der CMS Seite die für die Schlagwort-Archive verwendet wird.',
            'only_promoted' => 'Nur Beworbene',
            'only_promoted_comment' => 'Zeigt nur beworbene Schlagwörter an',
            'amount' => 'Anzahl',
            'amount_description' => 'Die Anzahl an Schlagwörtern die angezeigt werden soll.',
            'amount_validation' => 'Die Angaben zur Anzahl der Kommentare ist ungültig.',
            'view' => 'Ansicht',
            'view_comment' => 'Verändere die Ansicht der Schlagwörter.'
        ],
        'deprecated' => [
            'authors_label' => '[ALT] Beiträge nach Autor',
            'authors_comment' => '[VERALTET] - Bitte verwende "Beiträge nach Autor" oberhalb.',
            'dates_label' => '[ALT] Beiträge nach Datum',
            'dates_comment' => '[VERALTET] - Bitte verwende "Beiträge nach Datum" overhalb.',
            'tags_label' => '[ALT] Beitäge nach Schlagwort',
            'tags_comment' => '[VERALTET] - Bitte verwende "Beiträge nach Schlagwort" overhalb.',
        ],
    ],

    'frontend' => [
        'comments' => [
            'username' => 'Dein Name',
            'email' => 'Deine E-Mail Addresse',
            'title' => 'Dein Kommentar-Titel',
            'comment' => 'Dein Kommentar',
            'comment_markdown_hint' => 'Du kannst bei deinem Kommentar die Markdown Syntax verwenden.',
            'captcha' => 'Captcha Code',
            'captcha_reload' => 'Captcha neuladen', 
            'captcha_placeholder' => 'Gib den Code vom Bild hier ein', 
            'submit_comment' => 'Neuen Kommentar schreiben',
            'cancel_reply' => 'Antwort abbrechen',
            'submit_reply' => 'Auf diesen Kommentar antworten',
            'approve' => 'Akzeptieren',
            'approve_title' => 'Diesen Kommentar akzeptieren',
            'reject' => 'Ablehnen',
            'reject_title' => 'Diesen Kommentar ablehnen',
            'spam' => 'Als Spam markieren',
            'spam_title' => 'Diesen Kommentar als Spam markieren',
            'like' => 'Gefällt',
            'like_title' => 'Dieser Kommentar gefällt mir',
            'dislike' => 'Gefällt nicht',
            'dislike_title' => 'Dieser Kommentar gefällt mir nicht',
            'favorite' => 'Favorit',
            'favorite_title' => 'Diesen Kommentar favorisieren',
            'unfavorite' => 'De-Favorit',
            'unfavorite_title' => 'Diesen Kommentar von den FAvoriten entfernen',
            'reply' => 'Antworten',
            'reply_title' => 'Auf diesen Kommentar Antworten',
            'disabled_open' => 'Dir ist es nicht erlaubt auf diesen Beitrag zu kommentieren.',
            'disabled_restricted' => 'Du musst angemeldet sein um auf diesen Beitrag zu kommentieren.',
            'disabled_private' => 'Nur Backend-Benutzer können auf diesen Beitrag kommentieren.',
            'disabled_closed' => 'Der Kommentarbereich für diesen Beitrag wurde geschlossen.',
            'awaiting_moderation' => 'Moderation ausstehend',
            'previous' => 'Vorherige',
            'next' => 'Nächste',
            'replyto' => 'Antwort auf :name',
            'comment_by' => 'Kommentar von',
            'reply_by' => 'Antwort von',
            'by' => 'Von',
            'on' => 'am',
        ],
        'errors' => [
            'unknown_post' => 'Die angegebene BeitragsID order -slug fehlt oder ist ungültig.',
            'missing_form_id' => 'Die angegebene Komponenten-ID fehlt oder ist ungültig.',
            'form_disabled' => 'Der Kommentarbereich für diesen Beitrag wurde deaktiviert.',
            'not_allowed_to_comment' => 'Du bist nicht befugt um auf diesen Beitrag zu kommentieren oder zu antworten.',
            'invalid_csrf_token' => 'Der angegebene CSRF Token ist ungültig. Bitte lade die Seite neu und versuche es erneut.',
            'invalid_validation_code' => 'Der angegebene Validierungscode ist ungültig. Bitte lade die Seite neu und versuche es erneut.',
            'invalid_captcha' => 'Der angegebene Captcha-Code ist ungütig.',
            'honeypot_filled' => 'Mit den angegebenen Daten stimmt etwas nicht, bitte versuche es erneut.',
            'tos_not_accepted' => 'Du musst die Nutzungsbedingungen akzeptieren um auf diesen Beitrag zu kommentieren.',
            'parent_not_found' => 'Der angegebene Kommentar wurde nicht gefunden oder bereits gelöscht.',
            'parent_invalid' => 'Der angebene Kommentar ist ungültig oder verschoben worden.',
            'not_allowed_to' => 'Du bist nicht befugt diese Aktion aufzurufen.',
            'moderate_permission' => 'Du bist nicht befugt den Kommentarbereich zu moderieren.',
            'invalid_sttus' => 'Der angegebene Kommentarstatus ist ungültig.',
            'unknown_comment' => 'Der angegebene Kommentar existiert nicht (mehr).',
            'disabled_method' => 'Diese Funktion wurde vom Administrator deaktiviert.',
            'no_permissions_for' => 'Du hast nicht die erforderlichen Berechtigungen um diese Aktion durchzuführen.',
            'missing_comment_id' => 'Die Kommentar-ID fehlt oder ist ungültig.',
            'invalid_comment_id' => 'Die angegebene Kommentar-ID existiert nicht.',
            'unknown_error' => 'Ein unbekannter Fehler ist aufgetreten, bitte versuche es später erneut.'
        ],
        'success' => [
            'update_status' => 'Der Kommentar-Status wurde erfolgreich aktualisiert.'
        ]
    ],

    'model' => [
        'comments' => [
            'label' => 'Kommentare',
            'manage' => 'Kommentare verwalten',
            'recordName' => 'Kommentar',
            'status' => 'Kommentare-Status',
            'statusColumn' => 'Status',
            'statusComment' => 'Den aktuellen Kommentar-Status verändern',
            'statusPending' => 'Ausstehend',
            'statusApproved' => 'Akzeptiert',
            'statusRejected' => 'Abgelehnt',
            'statusSpam' => 'Spam',
            'title' => 'Kommentar-Titel',
            'titleComment' => 'Der Kommentar-Titel (entsprechend der Bloghub Konfiguration).',
            'content' => 'Kommentarinhalt',
            'contentComment' => 'Der Inhalt des Kommentars.',
            'favorite' => 'Kommentar favorisieren',
            'favoriteComment' => 'Favorisierte Kommentare werden speziell dargestellt und können oben in der Liste festgepinnt werden.',
            'favoriteColumn' => 'Favorit',
            'likes' => 'Gefällt Mir',
            'dislikes' => 'Gefällt mir nicht',
            'author' => 'Autor Benutzername',
            'authorComment' => 'Der Benutzername des Autoren, sofern dieser nicht angemeldet war.',
            'authorEmail' => 'Author E-Mail Adresse',
            'authorEmailComment' => 'Die E-Mail Adresse des Autoren, sofern dieser nicht angemeldet war.',
            'post_visibility' => [
                'label' => 'Sichtbarkeit des Kommentarbereichs',
                'comment' => 'Zeige oder Verstecke den Kommentarbereich in diesem Beitrag.'
            ],
            'post_mode' => [
                'label' => 'Kommentarmodus',
                'comment' => 'Verändere den Kommentarmodus für diesen Beitrag.',
                'open' => 'Offen (Jeder kann kommentieren)',
                'restricted' => 'Eingeschränkt (Nur angemeldete Nutzer können kommentieren)',
                'private' => 'Privat (Nur Backend-Nutzer können kommentieren)',
                'closed' => 'Geschlossen (Niemand kann kommentieren)'
            ],
            'guest' => 'Gast',
            'seconds_ago' => 'Vor ein paar Sekunden',
            'x_ago' => 'Vor :amount :format',
            'no_comment' => 'Kein Kommentar verfügbar', 
            'no_further_comments' => 'Keine weiteren Kommentare verfügbar', 
        ],
        'post' => [
            'read_time' => 'Lesezeit: :min Minuten :sec Sekunden',
            'read_time_sec' => 'Lesezeit: :sec Sekunden',
            'published_seconds_ago' => 'Vor ein paar Sekunden veröffentlicht.',
            'published_ago' => 'Vor :amount :format veröffentlicht.',
            'published_format_years' => 'Jahre',
            'published_format_months' => 'Monate',
            'published_format_days' => 'Tage',
            'published_format_hours' => 'Stunden',
            'published_format_minutes' => 'Minuten',
            'statistics' => 'Beitragsstatistiken',
        ],
        'tags' => [
            'label' => 'Schlagwörter',
            'manage' => 'Schlagwörter verwalten',
            'recordName' => 'Schlagwort',
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

    'permissions' => [
        'access_comments' => 'Kommentare verwalten',
        'access_comments_comment' => 'Berechtigt den Zugriff auf das Kommentar-Menü für sämtliche Beiträge.',
        'manage_post_settings' => 'Berechtigt die Verwaltung von Beitrags-spezifischen Einstellungen',
        'moderate_comments' => 'Berechtigt die Moderation der Kommentare',
        'delete_commpents' => 'Berechtigt die Löschung von bereits veröffentlichten Kommentaren',
        'access_tags' => 'Bereichtigt die Verwaltung der Schlagwörter',
        'access_tags_comment' => 'Berechtigt den Zugriff auf das Schlagwört Menü und dem Feld bei den Beiträgen.',
        'promote_tags' => 'Berechtigt das bewerben von Schlagwörtern',
    ],

    'sorting' => [
        'bloghub_views_asc' => 'Aufrufe (aufsteigend)',
        'bloghub_views_desc' => 'Aufrufe (absteigend)',
        'bloghub_unique_views_asc' => 'Einzigartige Aufrufe (aufsteigend)',
        'bloghub_unique_views_desc' => 'Einzigartige Aufrufe (absteigend)',
        'bloghub_comments_count_asc' => 'Kommentaranzahl (aufsteigend)',
        'bloghub_comments_count_desc' => 'Kommentaranzahl (absteigend)',
        'created_at_desc' => 'Veröffentlchungsdatum (absteigend)',
        'created_at_asc' => 'Veröffentlchungsdatum (aufsteigend)',
        'comments_count_desc' => 'Kommentaranzahl (absteigend)',
        'comments_count_asc' => 'Kommentaranzahl (aufsteigend)',
        'likes_desc' => 'Gefällt Mir Anzahl (absteigend)',
        'likes_asc' => 'Gefällt Mir Anzahl (aufsteigend)',
        'dislikes_desc' => 'Gefällt Mir Nicht Anzahl (absteigend)',
        'dislikes_asc' => 'Gefällt Mir Nicht Anzahl (aufsteigend)',
    ],

    'settings' => [
        'config' => [
            'label' => 'BlogHub',
            'description' => 'Verwalte die BlogHub spezifischen Einstellungen.'
        ],

        'comments' => [
            'tab' => 'Kommentare',
            'general_section' => 'Allgemeine Einstellungen',
            'comment_form_section' => 'Formular Einstellungen',

            'author_favorites' => [
                'label' => 'Autor-Favoriten',
                'comment' => 'Erlaubt Autoren Kommentare zu favorieren.'
            ],
            'like_comment' => [
                'label' => 'Gefällt Mir',
                'comment' => 'Aktiviere die "Gefällt Mir" Option bei den einzelnen Kommentaren.'
            ],
            'dislike_comment' => [
                'label' => 'Gefällt Mir Nicht',
                'comment' => 'Aktiviere die "Gefällt Mir Nicht" Option bei den einzelnen Kommentaren.'
            ],
            'restrict_to_users' => [
                'label' => 'Gefällt Mir (Nciht) nur für Benutzer',
                'comment' => 'Beide Optionen können nur von angemeldeten Benutzern genutzt werden.'
            ],
            'guest_comments' => [
                'label' => 'Gast-Kommentare',
                'comment' => 'Erlaubt Gästen Beiträge zu kommentieren.'
            ],
            'moderate_guest_comments' => [
                'label' => 'Gast-Kommentare moderieren',
                'comment' => 'Gast-Kommentare müssen moderiert werden bevor sie im Beitrag ersichtlich sind.'
            ],
            'moderate_user_comments' => [
                'label' => 'Bnutzer-Kommentare moderieren',
                'comment' => 'Frontend-Nutzer-Kommentare müssen moderiert werden bevor sie im Beitrag ersichtlich sind.'
            ],
            'form_comment_title' => [
                'label' => 'Kommentar-Titel',
                'comment' => 'Aktiviere das Feld "Kommentar-Titel".',
            ],
            'form_comment_markdown' => [
                'label' => 'Kommentar-Markdown',
                'comment' => 'Erlaubt die Nutzung der Markdown Syntax im Kommentarfeld.',
            ],
            'form_comment_honeypot' => [
                'label' => 'Kommentar Honeypot',
                'comment' => 'Fügt ein Honeypot Feld zum Schutz vor simplen Bots hinzu.',
            ],
            'form_comment_captcha' => [
                'label' => 'Kommentar Captcha',
                'comment' => 'Fügt ein GREGWAR Captcha Feld zum Schutz von einigen Bots hinzu.',
            ],
            'form_tos_checkbox' => [
                'label' => 'Nutzungsbedingungen erfordern',
                'comment' => 'Zeigt eine zusätzliche Checkbox mit der man den Nutzungsbedingungen zustimmen kann.',
            ],
            'form_tos_hide_on_user' => [
                'label' => 'Für bekannte Nutzer verstecken',
                'comment' => 'Versteckt die Checkbox für die Nutzungsbedingungen für angemeldete Benutzer.',
            ],
            'form_tos_type' => [
                'label' => 'Nutzungsbedingungen Typ',
                'cms_page' => 'CMS Seite',
                'static_page' => 'Statische Seite'
            ],
            'form_tos_label' => [
                'label' => 'Nutzungsbedingungen Label',
                'default' => 'Ich habe die [Nutzungsbedingungen] gelesen und akzeptiere sie.',
                'comment' => 'Der Text in den eckigen Klammern wird mit einem Link zur ausgewählten Seite versehen.'
            ],
            'form_tos_page' => [
                'cmsLabel' => 'Nutzungsbedingungen - CMS Seite',
                'staticLabel' => 'Nutzungsbedingungen - Statische Seite',
                'emptyOption' => '-- Wähle die seite mit den Nutzungsbedingungen --',
                'comment' => 'Wähle die entsprechende Seite oder lasse dieses Feld frei um nur den Label anzuzeigen.'
            ]
        ],

        'meta' => [
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
    ],

    'widgets' => [
        'comments_list' => [
            'label' => 'BlogHub - Kommentarliste'
        ],
        'posts_list' => [
            'label' => 'BlogHub - Beitragsliste'
        ],
        'posts_statistics' => [
            'label' => 'BlogHub - Beitragsstatistiken'
        ]
    ]
];
