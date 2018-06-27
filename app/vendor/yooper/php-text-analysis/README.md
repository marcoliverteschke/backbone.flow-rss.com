php-text-analysis
=============
![alt text](https://travis-ci.org/yooper/php-text-analysis.svg?branch=master "Build status")

[![Latest Stable Version](https://poser.pugx.org/yooper/php-text-analysis/v/stable)](https://packagist.org/packages/yooper/php-text-analysis)

[![Total Downloads](https://poser.pugx.org/yooper/php-text-analysis/downloads)](https://packagist.org/packages/yooper/php-text-analysis)


PHP Text Analysis is a library for performing Information Retrieval (IR) and Natural Language Processing (NLP) tasks using the PHP language. All the documentation for this project can be found in the wiki. 

Installation Instructions
=============

Add PHP Text Analysis to your project
```
composer require yooper/php-text-analysis
```
Documentation for the library resides in the wiki. 
https://github.com/yooper/php-text-analysis/wiki


### Tokenization
```php
$tokens = tokenize($text);
```

You can customize which type of tokenizer to tokenize with by passing in the name of the tokenizer class
```php
$tokens = tokenize($text, \TextAnalysis\Tokenizers\PennTreeBankTokenizer::class);
```
The default tokenizer is **\TextAnalysis\Tokenizers\GeneralTokenizer::class** . Some tokenizers require parameters to be set upon instantiation. 

### Normalization
By default, **normalize_tokens** uses the function **strtolower** to lowercase all the tokens. To customize
the normalize function, pass in either a function or a string to be used by array_map. 

```php
$normalizedTokens = normalize_tokens(array $tokens); 
```

```php
$normalizedTokens = normalize_tokens(array $tokens, 'mb_strtolower');

$normalizedTokens = normalize_tokens(array $tokens, function($token){ return mb_strtoupper($token); });
```

### Frequency Distributions

The call to **freq_dist** returns a [FreqDist](https://github.com/yooper/php-text-analysis/blob/master/src/Analysis/FreqDist.php) instance. 
```php
$freqDist = freq_dist(tokenize($text));
```

### Ngram Generation
By default bigrams are generated.
```php
$bigrams = ngrams($tokens);
```
Customize the ngrams
```php
// create trigrams with a pipe delimiter in between each word
$trigrams = ngrams($tokens,3, '|');
```
 
Dictionary Installation
=============

To do


