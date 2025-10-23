#!/usr/bin/env php
<?php
/**
 * Add Phan-in-Browser demo links to markdown files
 *
 * Usage: php add_demo_links.php ../v6-Generic-Types.md
 *
 * Finds PHP code blocks in markdown and adds "Try this in Phan" links below them.
 */

// ============================================================================
// LZ-String PHP Implementation (embedded for self-contained script)
// From: https://github.com/nullpunkt/lz-string-php
// ============================================================================

class LZUtil
{
    public static $keyStrUriSafe = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+-$";
    public static $baseReverseDic = [];

    public static function getBaseValue($alphabet, $character)
    {
        if (!isset(self::$baseReverseDic[$alphabet])) {
            self::$baseReverseDic[$alphabet] = [];
            for ($i = 0; $i < strlen($alphabet); $i++) {
                self::$baseReverseDic[$alphabet][substr($alphabet, $i, 1)] = $i;
            }
        }
        return self::$baseReverseDic[$alphabet][$character];
    }

    public static function utf8_charAt($str, $index)
    {
        $char = mb_substr($str, 0, 1, 'UTF-8');
        if (mb_check_encoding($char, 'UTF-8')) {
            return $char;
        } else {
            return null;
        }
    }
}

class LZData
{
    public $str = '';
    public $val = 0;
    public $position = 0;
    public $index = 1;

    public function __construct($str, $position)
    {
        $this->str = $str;
        $this->position = $position;
    }
}

class LZContext
{
    public $dictionary = [];
    public $dictionaryToCreate = [];
    public $c = "";
    public $wc = "";
    public $w = "";
    public $enlargeIn = 2;
    public $dictSize = 3;
    public $numBits = 2;
    public $result = "";
    public $data_string = "";
    public $data_val = 0;
    public $data_position = 0;
}

class LZReverseDictionary
{
    public $dictionary = [];
    public $enlargeIn = 0;
    public $dictSize = 0;
    public $numBits = 0;
    public $entry = "";
    public $result = "";
    public $c = null;
    public $errorCount = 0;
    public $literal = null;
    public $w = "";
}

class LZString
{
    public static function compressToEncodedURIComponent($input)
    {
        if ($input === null) {
            return "";
        }
        return self::_compress(
            $input,
            6,
            function($a) {
                return LZUtil::$keyStrUriSafe[$a];
            }
        );
    }

    public static function decompressFromEncodedURIComponent($input)
    {
        if ($input === null) {
            return "";
        }
        if ($input === "") {
            return null;
        }

        $input = str_replace(' ', "+", $input);

        return self::_decompress(
            $input,
            32,
            function($data) {
                $sub = substr($data->str, $data->index, 6);
                $sub = LZUtil::utf8_charAt($sub, 0);
                $data->index++;
                return LZUtil::getBaseValue(LZUtil::$keyStrUriSafe, $sub);
            }
        );
    }

    private static function _compress($uncompressed, $bitsPerChar, $getCharFromInt)
    {
        if ($uncompressed === null) {
            return "";
        }

        $context = new LZContext();
        $value = 0;

        for ($ii = 0; $ii < mb_strlen($uncompressed, "UTF-8"); $ii++) {
            $context->c = mb_substr($uncompressed, $ii, 1, "UTF-8");
            if (!isset($context->dictionary[$context->c])) {
                $context->dictionary[$context->c] = $context->dictSize++;
                $context->dictionaryToCreate[$context->c] = true;
            }

            $context->wc = $context->w . $context->c;
            if (isset($context->dictionary[$context->wc])) {
                $context->w = $context->wc;
            } else {
                if (isset($context->dictionaryToCreate[$context->w])) {
                    if (ord($context->w) < 256) {
                        for ($i = 0; $i < $context->numBits; $i++) {
                            $context->data_val = ($context->data_val << 1);
                            if ($context->data_position == $bitsPerChar - 1) {
                                $context->data_position = 0;
                                $context->data_string .= $getCharFromInt($context->data_val);
                                $context->data_val = 0;
                            } else {
                                $context->data_position++;
                            }
                        }
                        $value = ord($context->w);
                        for ($i = 0; $i < 8; $i++) {
                            $context->data_val = ($context->data_val << 1) | ($value & 1);
                            if ($context->data_position == $bitsPerChar - 1) {
                                $context->data_position = 0;
                                $context->data_string .= $getCharFromInt($context->data_val);
                                $context->data_val = 0;
                            } else {
                                $context->data_position++;
                            }
                            $value = $value >> 1;
                        }
                    } else {
                        $value = 1;
                        for ($i = 0; $i < $context->numBits; $i++) {
                            $context->data_val = ($context->data_val << 1) | $value;
                            if ($context->data_position == $bitsPerChar - 1) {
                                $context->data_position = 0;
                                $context->data_string .= $getCharFromInt($context->data_val);
                                $context->data_val = 0;
                            } else {
                                $context->data_position++;
                            }
                            $value = 0;
                        }
                        $value = ord($context->w);
                        for ($i = 0; $i < 16; $i++) {
                            $context->data_val = ($context->data_val << 1) | ($value & 1);
                            if ($context->data_position == $bitsPerChar - 1) {
                                $context->data_position = 0;
                                $context->data_string .= $getCharFromInt($context->data_val);
                                $context->data_val = 0;
                            } else {
                                $context->data_position++;
                            }
                            $value = $value >> 1;
                        }
                    }
                    $context->enlargeIn--;
                    if ($context->enlargeIn == 0) {
                        $context->enlargeIn = pow(2, $context->numBits);
                        $context->numBits++;
                    }
                    unset($context->dictionaryToCreate[$context->w]);
                } else {
                    $value = $context->dictionary[$context->w];
                    for ($i = 0; $i < $context->numBits; $i++) {
                        $context->data_val = ($context->data_val << 1) | ($value & 1);
                        if ($context->data_position == $bitsPerChar - 1) {
                            $context->data_position = 0;
                            $context->data_string .= $getCharFromInt($context->data_val);
                            $context->data_val = 0;
                        } else {
                            $context->data_position++;
                        }
                        $value = $value >> 1;
                    }
                }
                $context->enlargeIn--;
                if ($context->enlargeIn == 0) {
                    $context->enlargeIn = pow(2, $context->numBits);
                    $context->numBits++;
                }

                $context->dictionary[$context->wc] = $context->dictSize++;
                $context->w = $context->c;
            }
        }

        if ($context->w !== "") {
            if (isset($context->dictionaryToCreate[$context->w])) {
                if (ord($context->w) < 256) {
                    for ($i = 0; $i < $context->numBits; $i++) {
                        $context->data_val = ($context->data_val << 1);
                        if ($context->data_position == $bitsPerChar - 1) {
                            $context->data_position = 0;
                            $context->data_string .= $getCharFromInt($context->data_val);
                            $context->data_val = 0;
                        } else {
                            $context->data_position++;
                        }
                    }
                    $value = ord($context->w);
                    for ($i = 0; $i < 8; $i++) {
                        $context->data_val = ($context->data_val << 1) | ($value & 1);
                        if ($context->data_position == $bitsPerChar - 1) {
                            $context->data_position = 0;
                            $context->data_string .= $getCharFromInt($context->data_val);
                            $context->data_val = 0;
                        } else {
                            $context->data_position++;
                        }
                        $value = $value >> 1;
                    }
                } else {
                    $value = 1;
                    for ($i = 0; $i < $context->numBits; $i++) {
                        $context->data_val = ($context->data_val << 1) | $value;
                        if ($context->data_position == $bitsPerChar - 1) {
                            $context->data_position = 0;
                            $context->data_string .= $getCharFromInt($context->data_val);
                            $context->data_val = 0;
                        } else {
                            $context->data_position++;
                        }
                        $value = 0;
                    }
                    $value = ord($context->w);
                    for ($i = 0; $i < 16; $i++) {
                        $context->data_val = ($context->data_val << 1) | ($value & 1);
                        if ($context->data_position == $bitsPerChar - 1) {
                            $context->data_position = 0;
                            $context->data_string .= $getCharFromInt($context->data_val);
                            $context->data_val = 0;
                        } else {
                            $context->data_position++;
                        }
                        $value = $value >> 1;
                    }
                }
                $context->enlargeIn--;
                if ($context->enlargeIn == 0) {
                    $context->enlargeIn = pow(2, $context->numBits);
                    $context->numBits++;
                }
                unset($context->dictionaryToCreate[$context->w]);
            } else {
                $value = $context->dictionary[$context->w];
                for ($i = 0; $i < $context->numBits; $i++) {
                    $context->data_val = ($context->data_val << 1) | ($value & 1);
                    if ($context->data_position == $bitsPerChar - 1) {
                        $context->data_position = 0;
                        $context->data_string .= $getCharFromInt($context->data_val);
                        $context->data_val = 0;
                    } else {
                        $context->data_position++;
                    }
                    $value = $value >> 1;
                }
            }
            $context->enlargeIn--;
            if ($context->enlargeIn == 0) {
                $context->enlargeIn = pow(2, $context->numBits);
                $context->numBits++;
            }
        }

        $value = 2;
        for ($i = 0; $i < $context->numBits; $i++) {
            $context->data_val = ($context->data_val << 1) | ($value & 1);
            if ($context->data_position == $bitsPerChar - 1) {
                $context->data_position = 0;
                $context->data_string .= $getCharFromInt($context->data_val);
                $context->data_val = 0;
            } else {
                $context->data_position++;
            }
            $value = $value >> 1;
        }

        while (true) {
            $context->data_val = ($context->data_val << 1);
            if ($context->data_position == $bitsPerChar - 1) {
                $context->data_string .= $getCharFromInt($context->data_val);
                break;
            } else {
                $context->data_position++;
            }
        }

        return $context->data_string;
    }

    private static function _decompress($compressed, $resetValue, $getNextValue)
    {
        // Decompression not needed for this script
        return "";
    }
}

// ============================================================================
// Main Script Logic
// ============================================================================

function generatePhanURL($code, $php = '84', $phan = 'v6-dev', $ast = '1.1.3') {
    $compressed = LZString::compressToEncodedURIComponent($code);
    return "https://phan.github.io/demo/?c={$compressed}&php={$php}&phan={$phan}&ast={$ast}";
}

function processMarkdownFile($inputFile, $outputFile = null) {
    if (!file_exists($inputFile)) {
        fwrite(STDERR, "Error: File not found: $inputFile\n");
        return false;
    }

    $content = file_get_contents($inputFile);
    $lines = explode("\n", $content);
    $output = [];
    $inCodeBlock = false;
    $codeBlockContent = '';
    $codeBlockStart = 0;
    $skipNextLines = 0;

    for ($i = 0; $i < count($lines); $i++) {
        // Skip lines that are marked for removal (old demo links)
        if ($skipNextLines > 0) {
            $skipNextLines--;
            continue;
        }

        $line = $lines[$i];

        if (preg_match('/^```php\s*$/', $line)) {
            // Start of PHP code block
            $inCodeBlock = true;
            $codeBlockContent = '';
            $codeBlockStart = $i;
            $output[] = $line;
        } elseif ($inCodeBlock && preg_match('/^```\s*$/', $line)) {
            // End of code block
            $inCodeBlock = false;
            $output[] = $line;

            // Skip empty code blocks
            if (trim($codeBlockContent) !== '') {
                // Check if there's an existing "Try this" link after this code block
                // We need to detect and replace: empty line + link (and preserve structure)
                $nextLineIdx = $i + 1;
                $hasExistingLink = false;
                $linesToSkip = 0;

                // Check for empty line followed by link
                if ($nextLineIdx < count($lines) && trim($lines[$nextLineIdx]) === '') {
                    if ($nextLineIdx + 1 < count($lines) &&
                        preg_match('/^\*\*\[Try this/', $lines[$nextLineIdx + 1])) {
                        $hasExistingLink = true;
                        // Skip empty line + link line only (not trailing empty)
                        $linesToSkip = 2;
                    }
                }
                // Check for link immediately on next line (no empty line before)
                elseif ($nextLineIdx < count($lines) &&
                         preg_match('/^\*\*\[Try this/', $lines[$nextLineIdx])) {
                    $hasExistingLink = true;
                    // Skip link line only
                    $linesToSkip = 1;
                }

                // Mark lines to skip
                $skipNextLines = $linesToSkip;

                // Add the new demo link (always with leading empty line)
                $url = generatePhanURL($codeBlockContent);
                $output[] = '';
                $output[] = "**[Try this example in Phan-in-Browser →]({$url})**";
            }
        } elseif ($inCodeBlock) {
            // Inside code block - accumulate content
            $codeBlockContent .= $line . "\n";
            $output[] = $line;
        } else {
            // Regular markdown line
            $output[] = $line;
        }
    }

    $result = implode("\n", $output);

    if ($outputFile) {
        file_put_contents($outputFile, $result);
        echo "Wrote output to: $outputFile\n";
    } else {
        echo $result;
    }

    return true;
}

// ============================================================================
// CLI Entry Point
// ============================================================================

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

if ($argc < 2) {
    echo "Usage: php add_demo_links.php <input.md> [output.md]\n";
    echo "\n";
    echo "Examples:\n";
    echo "  php add_demo_links.php ../v6-Generic-Types.md\n";
    echo "  php add_demo_links.php ../v6-Generic-Types.md ../v6-Generic-Types-new.md\n";
    echo "\n";
    echo "If no output file is specified, outputs to stdout.\n";
    exit(1);
}

$inputFile = $argv[1];
$outputFile = $argv[2] ?? null;

// Test LZ compression with the example
if (getenv('TEST')) {
    $testCode = "<?php\necho \"Hello World\";";
    $url = generatePhanURL($testCode);
    echo "Test URL:\n$url\n";
    echo "\nExpected c= parameter should match:\n";
    echo "DwfgDgFmBQCmDGED2ACARACVgG26g6kgE7YAmaA3NEA\n";
    exit(0);
}

processMarkdownFile($inputFile, $outputFile);
