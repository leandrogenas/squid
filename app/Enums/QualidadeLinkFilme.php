<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class QualidadeLinkFilme extends Enum
{
    const LINK_720P = "720p";
    const LINK_1080P = "1080p";
    const LINK_4K = "4K";
}
