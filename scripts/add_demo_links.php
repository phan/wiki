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

const DEMO_LINK_PATTERN = '/^(?:\*\*)?\[(?:▶|Try this)[^\]]*\]\(https:\/\/phan\.github\.io\/demo\//u';
const MARKER_PATTERN = '/^\s*<!--\s*demo:\s*(.*?)\s*-->\s*$/';
const PARAM_KEYS = ['php', 'phan', 'ast'];
const BUILTIN_DEFAULTS = ['php' => '84', 'phan' => 'v6-dev', 'ast' => '1.1.3'];

function generatePhanURL($code, array $params) {
    // Ensure code starts with <?php
    $code = trim($code);
    if (!preg_match('/^<\?php/', $code)) {
        $code = "<?php\n" . $code;
    }

    $compressed = LZString::compressToEncodedURIComponent($code);
    return "https://phan.github.io/demo/?c={$compressed}&php={$params['php']}&phan={$params['phan']}&ast={$params['ast']}";
}

function isDemoLink($line) {
    return (bool)preg_match(DEMO_LINK_PATTERN, $line);
}

/**
 * Read php/phan/ast from an existing demo link so a refresh keeps its versions.
 */
function paramsFromLink($line) {
    if (!preg_match('/\(https:\/\/phan\.github\.io\/demo\/\?([^\s)"]*)/', $line, $m)) {
        return [];
    }
    parse_str($m[1], $query);
    $params = [];
    foreach (PARAM_KEYS as $key) {
        if (isset($query[$key]) && is_string($query[$key]) && $query[$key] !== '') {
            $params[$key] = $query[$key];
        }
    }
    return $params;
}

/**
 * Parse the body of a <!-- demo: ... --> marker.
 * Returns ['skip' => bool, 'params' => [...]] or null on an unrecognized marker.
 */
function parseMarker($body, $file, $lineNo) {
    $result = ['skip' => false, 'params' => []];
    foreach (preg_split('/\s+/', trim($body), -1, PREG_SPLIT_NO_EMPTY) as $token) {
        if ($token === 'skip') {
            $result['skip'] = true;
        } elseif (preg_match('/^(php|phan|ast)=(\S+)$/', $token, $m)) {
            $result['params'][$m[1]] = $m[2];
        } else {
            fwrite(STDERR, "Warning: $file:$lineNo: unknown token '$token' in demo marker, ignoring\n");
        }
    }
    return $result;
}

/**
 * Find the marker attached to a code fence: the nearest non-blank line above it.
 */
function findMarker(array $output, $file, $lineNo) {
    for ($k = count($output) - 1; $k >= 0; $k--) {
        if (trim($output[$k]) === '') {
            continue;
        }
        if (preg_match(MARKER_PATTERN, $output[$k], $m)) {
            return parseMarker($m[1], $file, $lineNo);
        }
        return null;
    }
    return null;
}

/**
 * Starting right after a closing fence, return the index of the last line belonging to
 * existing demo link(s), or -1 if there are none. Consumes a leading blank line and any
 * run of duplicate links (optionally separated by blank lines) left by older versions.
 */
function findExistingLinks(array $lines, $start) {
    $n = count($lines);
    $k = $start;
    if ($k < $n && trim($lines[$k]) === '' && $k + 1 < $n && isDemoLink($lines[$k + 1])) {
        $k++;
    }
    $last = -1;
    while ($k < $n) {
        if (isDemoLink($lines[$k])) {
            $last = $k;
            $k++;
        } elseif (trim($lines[$k]) === '' && $k + 1 < $n && isDemoLink($lines[$k + 1])) {
            $k++;
        } else {
            break;
        }
    }
    return $last;
}

/**
 * @param array $cliParams php/phan/ast values given on the command line
 */
function processMarkdownFile($inputFile, $outputFile, array $cliParams) {
    if (!file_exists($inputFile)) {
        fwrite(STDERR, "Error: File not found: $inputFile\n");
        return false;
    }

    $content = file_get_contents($inputFile);
    $lines = explode("\n", $content);
    $output = [];
    $inCodeBlock = false;
    $codeBlockContent = '';
    $marker = null;
    $stats = ['added' => 0, 'refreshed' => 0, 'removed' => 0, 'skipped' => 0];

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];

        if (!$inCodeBlock && preg_match('/^```php\s*$/', $line)) {
            $marker = findMarker($output, $inputFile, $i + 1);
            $inCodeBlock = true;
            $codeBlockContent = '';
            $output[] = $line;
            continue;
        }

        if ($inCodeBlock && preg_match('/^```\s*$/', $line)) {
            $inCodeBlock = false;
            $output[] = $line;

            $lastLinkIdx = findExistingLinks($lines, $i + 1);
            $existingParams = $lastLinkIdx >= 0 ? paramsFromLink($lines[$lastLinkIdx]) : [];
            if ($lastLinkIdx >= 0) {
                // Prefer the first link's params if there were duplicates
                for ($k = $i + 1; $k <= $lastLinkIdx; $k++) {
                    if (isDemoLink($lines[$k])) {
                        $existingParams = paramsFromLink($lines[$k]);
                        break;
                    }
                }
            }

            if (($marker['skip'] ?? false) || trim($codeBlockContent) === '') {
                if ($lastLinkIdx >= 0) {
                    $i = $lastLinkIdx;
                    $stats['removed']++;
                } else {
                    $stats['skipped']++;
                }
                continue;
            }

            // Precedence: marker > command line > existing link > built-in default
            $params = array_merge(BUILTIN_DEFAULTS, $existingParams, $cliParams, $marker['params'] ?? []);
            $url = generatePhanURL($codeBlockContent, $params);
            $output[] = '';
            $output[] = "[▶]({$url} \"Try this example in Phan-in-Browser\")";
            if ($lastLinkIdx >= 0) {
                $i = $lastLinkIdx;
                $stats['refreshed']++;
            } else {
                $stats['added']++;
            }
            continue;
        }

        if ($inCodeBlock) {
            $codeBlockContent .= $line . "\n";
        }
        $output[] = $line;
    }

    $result = implode("\n", $output);

    if ($outputFile) {
        file_put_contents($outputFile, $result);
        echo "Wrote output to: $outputFile\n";
    } else {
        echo $result;
    }
    fwrite(STDERR, sprintf(
        "Links: %d added, %d refreshed, %d removed; %d blocks skipped\n",
        $stats['added'], $stats['refreshed'], $stats['removed'], $stats['skipped']
    ));

    return true;
}

function usage() {
    echo <<<EOT
Usage: php add_demo_links.php [--php=84] [--phan=v6-dev] [--ast=1.1.3] <input.md> [output.md]

Adds or refreshes a Phan-in-Browser link after every ```php block.
If no output file is specified, outputs to stdout.

Options (override versions kept from existing links, but not per-block markers):
  --php=VER    PHP version (81-86; the demo must have a build for it). Default: 84
  --phan=VER   Phan version (5.5.2, 6.0.11, v6-dev). Default: v6-dev
  --ast=VER    php-ast version (1.1.2, 1.1.3). Default: 1.1.3

Per-block markers (on the line before the ```php fence):
  <!-- demo: skip -->            no link for this block (removes an existing one)
  <!-- demo: php=85 -->          set versions for this block only

Examples:
  php add_demo_links.php ../Generic-Types-V6.md
  php add_demo_links.php --php=85 ../PHP-8.4-8.5-Support.md /tmp/out.md

EOT;
}

// ============================================================================
// CLI Entry Point
// ============================================================================

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

// Test LZ compression with the example
if (getenv('TEST')) {
    $expected = 'DwfgDgFmBQCmDGED2ACARACVgG26g6kgE7YAmaA3EA';
    $url = generatePhanURL("<?php\necho \"Hello World\";", BUILTIN_DEFAULTS);
    echo "Test URL:\n$url\n";
    $ok = strpos($url, "?c=$expected&") !== false;
    echo $ok ? "OK: c= parameter matches $expected\n" : "FAIL: expected c=$expected\n";
    exit($ok ? 0 : 1);
}

$cliParams = [];
$positional = [];
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '-h' || $arg === '--help') {
        usage();
        exit(0);
    } elseif (preg_match('/^--(php|phan|ast)=(\S+)$/', $arg, $m)) {
        $cliParams[$m[1]] = $m[2];
    } elseif (strncmp($arg, '--', 2) === 0) {
        fwrite(STDERR, "Error: unknown option $arg\n");
        exit(1);
    } else {
        $positional[] = $arg;
    }
}

if (count($positional) < 1 || count($positional) > 2) {
    usage();
    exit(1);
}

exit(processMarkdownFile($positional[0], $positional[1] ?? null, $cliParams) ? 0 : 1);
