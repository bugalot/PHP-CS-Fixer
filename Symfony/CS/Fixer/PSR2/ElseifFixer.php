<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶5.1.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ElseifFixer extends AbstractFixer
{
    /**
     * Replace all `else if` (T_ELSE T_IF) with `elseif` (T_ELSEIF)
     *
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            // if token is not T_ELSE - continue searching, this is not right token to fix
            if (!$token->isGivenKind(T_ELSE)) {
                continue;
            }

            $nextIndex = null;
            $nextToken = $tokens->getNextNonWhitespace($index, array(), $nextIndex);

            // if next meaning token is not T_IF - continue searching, this is not the case for fixing
            if (!$nextToken->isGivenKind(T_IF)) {
                continue;
            }

            // now we have T_ELSE following by T_IF so we could fix this
            // 1. clear whitespaces between T_ELSE and T_IF
            $tokens[$index + 1]->clear();

            // 2. change token from T_ELSE into T_ELSEIF
            $token->content = 'elseif';
            $token->id = T_ELSEIF;

            // 3. clear succeeding T_IF
            $nextToken->clear();
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The keyword elseif should be used instead of else if so that all control keywords looks like single words.';
    }
}
