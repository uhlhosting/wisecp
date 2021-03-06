<?php 
return [
    'meta-list'                             => [
        'title' => 'Browse Languages',
    ],
    'meta-add'                              => [
        'title' => 'Add Language',
    ],
    'meta-edit'                             => [
        'title' => 'Edit Language',
    ],
    'meta-find-and-replace'                 => [
        'title' => 'Find and Replace',
    ],
    'breadcrumb-list'                       => 'Browse Languages',
    'breadcrumb-add'                        => 'Add Language',
    'breadcrumb-edit'                       => [
        'content'   => '{name} - Edit',
        'variables' => '{name}',
    ],
    'breadcrumb-find-and-replace'           => 'Find and Replace',
    'page-list'                             => 'Browse Languages',
    'page-add'                              => 'Add Language',
    'page-edit'                             => 'Edit Language',
    'page-find-and-replace'                 => 'Find and Replace',
    'list-description'                      => 'WISECP uses English as a default language. It is possible to add more languages, export, import language files. The default language can be also changed.',
    'list-description2'                     => 'You can access free languages shared by our community via the link below. If you have translated your local language for WISECP, you can share it with our community and get discounts on WISECP license fees. <strong><a href="https://translate.wisecp.com" target="_blank">Please click</a></strong> to visit the translate platform.',
    'list-table-country'                    => 'Country',
    'list-table-name'                       => 'Language Name',
    'list-table-country-code'               => 'Country Code',
    'list-table-phone-code'                 => 'Phone Code',
    'list-table-local'                      => 'Default',
    'list-table-status'                     => 'Status',
    'list-delete-are-youu-sure'             => 'Do you really want to delete the language of <strong>{name}</strong>?',
    'list-delete-modal-title'               => 'Delete Language',
    'add-description'                       => 'To add a new language, select which country the language belongs to and then select the reference language. The text of the reference language you chose will be added in new language. If you have added any content before adding a new language, do not forget to translate the relevant content.',
    'add-select-country'                    => 'Country',
    'add-select-country-desc'               => 'Select the country that language belongs to',
    'add-please-select'                     => 'Select',
    'add-select-language'                   => 'Select Language',
    'add-select-language-desc'              => 'Select the official language you will define for the country.',
    'add-native-name'                       => 'Language Title',
    'add-native-name-desc'                  => 'The title that the visitors can see when choosing language, or based on their location',
    'add-country-code'                      => 'Country Code',
    'add-country-code-desc'                 => '---',
    'add-phone-code'                        => 'Phone Code',
    'add-copy-language'                     => 'Copy (Reference) Language',
    'add-copy-language-desc'                => 'Select the language from which the language content will be copied',
    'add-rank'                              => 'Sequence Number',
    'add-rank-desc'                         => ' ',
    'add-rtl'                               => 'RTL',
    'add-rtl-active'                        => 'Enable RTL (Right to Left) for this language.',
    'edit-local'                            => 'Default Language',
    'edit-local-label'                      => '---',
    'edit-local-desc'                       => ' ',
    'edit-language-info'                    => 'Select Language',
    'edit-copied-language'                  => 'Copied Language',
    'edit-status'                           => 'Status',
    'edit-import'                           => 'Import',
    'edit-export'                           => 'Export',
    'edit-upload-file'                      => 'Upload File',
    'edit-download-file'                    => 'Download File',
    'edit-import-desc'                      => 'The file can be imported to replace any texts. It must be in excel format and must follow the template that WISECP follows. It is also recommended that the file is exported to know the format of WISECP.',
    'edit-export-desc'                      => 'The excel file is exported which is currently running on WISECP, it can be usually used to translate and understand the template that WISECP uses for languages.',
    'find-and-replace-description'          => 'You can search for any word you want to change from the field below and make changes on it. (Case sensitive)',
    'find-and-replace-select-language'      => 'Choose a Language',
    'find-and-replace-select-language-none' => 'Select',
    'find-and-replace-search'               => 'Enter word or sentence',
    'find-and-replace-search-placeholder'   => 'Enter text here (sensitive to upper-lower case)',
    'find-and-replace-not-found-match'      => 'No results were found that matched <strong>"{word}"</strong>',
    'find-and-replace-is-found-match'       => '<strong>"{count}"</strong> results found that match <strong>"{word}"</strong>',
    'find-and-replace-variables'            => 'Variables',
    'delete-ok'                             => 'Yes',
    'delete-no'                             => 'No',
    'add-new-button'                        => 'Add New',
    'edit-button'                           => 'Update',
    'api-text1'                             => 'Translator',
    'api-text2'                             => 'Enable',
    'api-text3'                             => "<ul class=\"langenableinfo\">\n<h5><strong>Explanations and Instructions</strong></h5>\n<li>This language was prepared by a translator who wanted to contribute to WISECP development.</li>\n<li>Although it is controlled by the WISECP editors, there may be some misspellings in language translations. You can report such situations to the translator.</li>\n<li>When you enable a language, existing contents must also be set for the new language. For this reason, we recommend that you enable the languages you want immediately after installation.</li>\n<li>You can access the translator profile by <a target=\"_blank\" id=\"translator_link\"><strong>clicking here</strong></a> to rate the translation and report a problem.</li> \n<li>If you have made a translation, you can publish it on <a href=\"translate.wisecp.com\" target=\"_blank\"><strong>translate.wisecp.com</strong></a> to contribute to the development of WISECP.</li>\n</ul>",
    'api-text4'                             => 'Enable {language} Language',
    'error1'                                => 'There\'s no such language.',
    'error2'                                => 'The local language cannot be Inactive.',
    'error3'                                => 'Please select your country.',
    'error4'                                => 'Please specify the language',
    'error5'                                => 'Please select a language to copy.',
    'error6'                                => 'A previously added language is available in the criteria you specified.',
    'error7'                                => 'The user (visitor) view cannot be left blank.',
    'error8'                                => 'This language cannot be deleted because it is local.',
    'error9'                                => 'An error has occurred. The file type is not supported.',
    'error10'                               => 'An error has occurred. File upload failed.',
    'error11'                               => [
        'content'   => 'An error has occurred. The file cannot be read. {error}',
        'variables' => '{error}',
    ],
    'success1'                              => 'The situation has been successfully updated.',
    'success2'                              => 'The language has been successfully added.',
    'success3'                              => 'The language has been successfully deleted.',
    'success4'                              => 'Language updated successfully',
];
