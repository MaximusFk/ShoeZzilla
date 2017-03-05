<?php
require_once('HTML/BBCodeParser.php');
$arrayBBCode=[
    '' => ['type' => BBCODE_TYPE_ROOT, 'childs' => '!i'],
    'i' => ['type' => BBCODE_TYPE_NOARG, 'open_tag' => '<i>',
        'close_tag' => '</i>', 'childs' => 'b'],
    'url' => ['type' => BBCODE_TYPE_OPTARG,
        'open_tag' => '<a href="{PARAM}">', 'close_tag' => '</a>',
        'default_arg' => '{CONTENT}',
        'childs' => 'b,i'],
    'img' => ['type' => BBCODE_TYPE_NOARG,
        'open_tag' => '<img src="', 'close_tag' => '" />',
        'childs' => ''],
    'b' => ['type' => BBCODE_TYPE_NOARG, 'open_tag' => '<b>',
        'close_tag' => '</b>']
];

function decode_bb($string) {
    $tags = [
        'url' => ['htmlopen' => 'a', 'htmlclose' => 'a', 'attributes' => ['url' => 'href=%2$s%1$s%2$s']]
    ];
    $bb = new HTML_BBCodeParser();
    $bb->addFilters($tags);
    return $bb->qparse($string);
}

