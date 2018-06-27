<?php

namespace Tests\TextAnalysis\Tokenizers;

use TextAnalysis\Tokenizers\TwitterTokenizer;

/**
 *
 * @author yooper
 */
class TwitterTokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenizer()
    {
        $tokens = (new TwitterTokenizer)->tokenize('This is a common Tweet #format where @mentions and.errors!!!!like this:-))))) might #appear❤ ❤☺❤#ThisIsAHashtag!?!');
        $this->assertCount(33, $tokens);
        
    }
    
    public function testForUrlAndEmail()
    {
        $tokens = (new TwitterTokenizer)->tokenize('Custom Software Development http://redbeardtechnologies.com/ 906-555-5555 or contact support at support@redbeardtechnologies.com :-)');
        $this->assertCount(11, $tokens);        
    }    
}
