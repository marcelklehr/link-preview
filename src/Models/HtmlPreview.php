<?php

namespace Marcelklehr\LinkPreview\Models;

use Marcelklehr\LinkPreview\Contracts\PreviewInterface;
use Marcelklehr\LinkPreview\Traits\HasExportableFields;
use Marcelklehr\LinkPreview\Traits\HasImportableFields;

class HtmlPreview implements PreviewInterface
{
    use HasExportableFields;
    use HasImportableFields;

    /**
     * @var string $description Link description
     */
    private $description;

    /**
     * @var string $cover Cover image (usually chosen by webmaster)
     */
    private $cover;

    /**
     * @var array Images found while parsing the link
     */
    private $images = [];

    /**
     * @var string $title Link title
     */
    private $title;
    
    /**
     * @var string $title Link favicon url
     */
    private $favicon;

    /**
     * @var string $video Video for the page (chosen by the webmaster)
     */
    private $video;

    /**
     * @var string $videoType If there is  video, what type it is
     */
    private $videoType;

    /**
     * Fields exposed
     * @var array
     */
    private $fields = [
        'cover',
        'images',
        'title',
        'favicon',
        'description',
        'video',
        'videoType',
    ];
}
