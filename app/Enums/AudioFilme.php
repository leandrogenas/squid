<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class AudioFilme extends Enum
{
    const DUAL_AUDIO = "Dual Áudio";
    const DUBLADO =  "Dublado";
    const LEGENDADO = "Legendado";
}
