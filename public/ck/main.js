import {
    ClassicEditor,
    Alignment,
    Autoformat,
    AutoImage,
    Autosave,
    BlockQuote,
    Bold,
    CodeBlock,
    Emoji,
    Essentials,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    GeneralHtmlSupport,
    Heading,
    Highlight,
    HorizontalLine,
    ImageBlock,
    ImageCaption,
    ImageEditing,
    ImageInline,
    ImageInsert,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Indent,
    IndentBlock,
    Italic,
    Link,
    List,
    Markdown,
    MediaEmbed,
    Mention,
    Paragraph,
    RemoveFormat,
    SourceEditing, // Plugin pour afficher le code source
    Strikethrough,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TextTransformation,
    TodoList,
    Underline
} from 'ckeditor5';

// Importer les traductions
import translations from 'ckeditor5/translations/fr.js';

const LICENSE_KEY =
    'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3NDUxMDcxOTksImp0aSI6IjE2ZjhhYzdiLThlOWEtNDc1OC1iYmY2LTc4NWU0ZWYzOWUzMyIsInVzYWdlRW5kcG9pbnQiOiJodHRwczovL3Byb3h5LWV2ZW50LmNrZWRpdG9yLmNvbSIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiLCJzaCJdLCJ3aGl0ZUxhYmVsIjp0cnVlLCJsaWNlbnNlVHlwZSI6InRyaWFsIiwiZmVhdHVyZXMiOlsiKiJdLCJ2YyI6Ijg1ZmYxMzVjIn0.iyIH0H7BqTVVYZPmsmGcmKO19qm_7xEdmYdiSMAFgg3DV30JI63ks-xoK8cv0U9B3LcI37RrLYiOxR7zLGFBCw';

// Configuration de CKEditor
const editorConfig = {
    toolbar: {
        items: [
            'heading',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'codeBlock',  // Ajout du bouton CodeBlock pour les blocs de code
            'sourceEditing', // Ajout du bouton pour afficher le code source
            'insertImage',
            'link',
            'bulletedList',
            'numberedList',
            'blockQuote',
            'horizontalLine',
            'removeFormat',
            'emoji',
            'specialCharacters'
        ],
        shouldNotGroupWhenFull: false
    },
    plugins: [
        Alignment,
        Autoformat,
        AutoImage,
        Autosave,
        BlockQuote,
        Bold,
        CodeBlock,
        Emoji,
        Essentials,
        FontBackgroundColor,
        FontColor,
        FontFamily,
        FontSize,
        GeneralHtmlSupport,
        Heading,
        Highlight,
        HorizontalLine,
        ImageBlock,
        ImageCaption,
        ImageEditing,
        ImageInline,
        ImageInsert,
        ImageResize,
        ImageStyle,
        ImageTextAlternative,
        ImageToolbar,
        ImageUpload,
        Indent,
        IndentBlock,
        Italic,
        Link,
        List,
        Markdown,
        MediaEmbed,
        Mention,
        Paragraph,
        RemoveFormat,
        SourceEditing, // Assurez-vous d'inclure le plugin SourceEditing
        Strikethrough,
        Table,
        TableCaption,
        TableCellProperties,
        TableColumnResize,
        TableProperties,
        TextTransformation,
        TodoList,
        Underline
    ],
    fontFamily: {
        supportAllValues: true
    },
    fontSize: {
        options: [10, 12, 14, 'default', 18, 20, 22],
        supportAllValues: true
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
            { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
            { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
        ]
    },
    htmlSupport: {
        allow: [
            {
                name: /.*/, // Permet toutes les balises HTML
                attributes: true,
                styles: true,
                classes: true
            }
        ]
    },
    image: {
        toolbar: [
            'toggleImageCaption',
            'imageTextAlternative',
            '|',
            'imageStyle:inline',
            'imageStyle:wrapText',
            'imageStyle:breakText',
            '|',
            'resizeImage'
        ]
    },
    initialData: '',
    language: 'fr',
    licenseKey: LICENSE_KEY,
    link: {
        addTargetToExternalLinks: true,
        defaultProtocol: 'https://',
        decorators: {
            toggleDownloadable: {
                mode: 'manual',
                label: 'Downloadable',
                attributes: {
                    download: 'file'
                }
            }
        }
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true
        }
    },
    mention: {
        feeds: [
            {
                marker: '@',
                feed: [
                    /* Exemples d'utilisateurs à mentionner */
                ]
            }
        ]
    },
    placeholder: 'Type or paste your content here!',
    style: {
        definitions: [
            {
                name: 'Article category',
                element: 'h3',
                classes: ['category']
            },
            {
                name: 'Title',
                element: 'h2',
                classes: ['document-title']
            },
            {
                name: 'Subtitle',
                element: 'h3',
                classes: ['document-subtitle']
            },
            {
                name: 'Info box',
                element: 'p',
                classes: ['info-box']
            },
            {
                name: 'Side quote',
                element: 'blockquote',
                classes: ['side-quote']
            },
            {
                name: 'Marker',
                element: 'span',
                classes: ['marker']
            },
            {
                name: 'Spoiler',
                element: 'span',
                classes: ['spoiler']
            },
            {
                name: 'Code (dark)',
                element: 'pre',
                classes: ['fancy-code', 'fancy-code-dark']
            },
            {
                name: 'Code (bright)',
                element: 'pre',
                classes: ['fancy-code', 'fancy-code-bright']
            }
        ]
    },
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
    },
    translations: [translations]
};

const editorField = document.querySelector('#appels_texte');

// Assurez-vous que le contenu est en HTML
const initialValue = editorField.value; // Récupère la valeur initiale

ClassicEditor.create(editorField, editorConfig)
    .then(newEditor => {
        newEditor.setData(initialValue); // Utilise setData pour définir le contenu

        // Écouter les changements dans l'éditeur
        newEditor.model.document.on('change:data', () => {
            const editorData = newEditor.getData();
            editorField.value = editorData; // Met à jour le champ textarea
        });
    })
    .catch(err => {
        console.error(err.stack);
    });
