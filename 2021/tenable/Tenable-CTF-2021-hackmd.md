# Tenable CTF 2021

## Needed challenges (for 100% T-Shirt chance)
- Weird Transmission (**175**,130) *Stego*
- Is the King in Check? (**200**,103) *Code*
- ECDSA Implementation Review (**225**,85) *Crypto*
- Queen's Gambit (**125**,76) *Pwn*

## Priority (old challenges)
 - Thumbnail (**100**,72) *Web*
 - What is dead may never die (**25**,42) *Tenable*
 - Hacker Toolz (**200**,29) *Web*
 - Hacker Manifesto (**250**,27) *Reverse Engineering*
 - Pwntown 3 (**200**,27) *Reverse Engineering*
 - Gambit's Queens (**200**,24) *Pwn*
 - Welcome to The Friendzone (**250**,8) *Pwn*
 - Funny Sound (**75**,9) *Stego*
 - Pwntown 4 (**200**,7) *Reverse Engineering*
 - Pwntown 2 (**200**,0) *Reverse Engineering*

## Priority (new challenges)
- Play me (**200**,36) *Vidya*
- Look at all the pixels, where do they all come from (**125**,13) *Stego*
- Welcome to The Friendzone (**250**,7) *Pwn*
- Evening City PD (**200**,6) *Pwn*
- I have a dream (**100**,3) *Misc*
- SETI (**150**,2) *Misc*
- Music (**100**,0) *Stego*

## Done (TODO)


## Alle Flags nach Kategorie/Challenge
Reihenfolge: wie auf der Challenge Seite
### Tenable
```
flag{1t's eXt3n51bl3} (save as .nessus, search for "flag")
-
flag{bu7 n07 putt1ng 1t 1n 4 fru17 s@l4d, th@t5 W1SD0M} (host info download)
-
flag{Pr0gr4mm1ng Mu57 83 7h3 Pr0c355 0f Putt1ng 7h3m 1n} (get_flag.log,debug logs)
-
```
### Pwn
```
-
-
-
-
```
### Stego
```
flag{Bl4ck_liv3S_MATTER} (rgb channels)
flag{m1cr0dot} (hackerman)
-
-
flag{th0s3_m0nk5_w3r3_cl3v3r} (carve 2nd png, Cistercian Monk Numerals)
flag{otp_reuse_fail} (combine images)
-
-
-
flag{steg0_a3s} (16byte-data=128x(pw=0|1),key=key.png)
```
### Reverse Engineering
```
flag{str1ngs_FTW} (strings)
flag{th3_amazinng_r4c3}
-
-
-
-
```
### Crypto
```
flag{classicvigenere} (pw:anonymous)
flag{congrats_you_got_me} (b64,hex,rot13)
flag{b4d_bl0cks_for_g0nks} (ecb exploit)
-
```
### Code
```
SOLVED
SOLVED
SOLVED
SOLVED
SOLVED
SOLVED
SOLVED
flag{N1ce_Emul8tor!1}
-
```
### Vidya
```
-
```
### Web App
```
flag{mr_roboto} (robots.txt)
flag{best_implants_ever} (source code)
flag{404_oh_no} (trigger 404)
flag{disable_directory_indexes} (/uploads)
flag{selfsignedcert} (goto https)
flag{headersftw} (in response headers)
flag{flag1_517d74} (GET,/main)
flag{flag2_de3981} (POST,/main)
flag{flag3_0d431e} (POST,/main,param:magicWord=please)
flag{flag4_695954} (POST,/main,header:Content-Type=application/json)
flag{flag5_70102b} (OPTIONS,/main)
flag{flag6_ca1ddf} (GET,/main,header:Magic-Word=please)
flag{messing_with_cookies} (cookie=true)
flag{cracked_the_password} (.htpasswd,admin:alesh16)
flag{hidden_flag_1dbc4} (GET,/main?name=please)
flag{xxe_aww_yeah} (xee xml exploit)
flag{session_flag_0dac2c} (GET,/other?name=admin,GET,/)
flag{automation_is_handy} (1582 bytes png)
-
flag{scooby} (phar deserialization attack)
-
```
### Misc
```
flag{wtf_is_brainfuck} (brainfuck)
flag{thy_flag_is_this}
?
flag{not_base64} (base58)
flag{f0ll0w_th3_whit3_r@bb1t} (xor)
flag{son_of_a_bson} (base64,characterset+custom-offsets)
flag{i_miss_aol} (morse,PWD:-RE:,kurz-lang)
?
-
-
```
### Forensics
```
flag{u_p4ss_butt3r} (butter.jpg)
flag{pickl3_NIIICK} (7zip open on windows)
flag{20_minute_adventure} (hex,base64,jpg)
flag{usb_pcaps_are_fun}
flag{hands_off_my_png}
```
### OSINT
```
flag{i_s33_y0u} (peekaboo vuln,tra-2018-25)
```
### _Introduction (free flags)
```
flag{1_w4nt_fr33_stuff}
flag{some_clever_text}
-
```

---
# Detailed challenge notes

### Web - Send a letter
```
Data: Message to dasf appended to /tmp/messages_outbound.txt for pickup. Mailbox flag raised.
Status: success
```

Potentieller Ablauf:
1. XML wird in den Query-Parametern in $_GET übergeben.
2. XML wird geparsed.
3. Der Brief wird an /tmp/messages_outbound.txt angefügt.
4. Feld aus dem geparsten XML werden für die Antwort verwendet


### Web - Protected Directory
```
/admin

http_basic Abfrage

http://167.71.246.232/.htpasswd
admin:$apr1$1U8G15kK$tr9xPqBn68moYoH4atbg20

gecracked mit john
--> alesh16
```

### Web - Certified Rippers
```
Certified Rippers

    Rippers:
      Don Sue Mei: dds123@example.com
      Lok Mah NoHans: llhsa@example.com
      Mr Flagerific: flag{messing_with_cookies}

```

### Web - Dirextory Indexes
```
/images wurde aufgelistet

http://167.71.246.232/images/aljdi3sd.txt

flag{disable_directory_indexes}
```

### Web - Rabbit Hole
```
Liste von 1582 Bytes ziehen in Hex-Format und zu einer Datei zusammenfügen.
Ergebnis: PNG Bild wo drin steht "flag{automation_is_handy}"
```


### Code - Random Encryption Fixed
It's disgusting but it works
```python=
import random


resSeeds = [9925, 8861, 5738, 1649, 2696, 6926, 1839, 7825, 6434, 9699, 227, 7379, 9024, 817, 4022, 7129, 1096, 4149, 6147, 2966, 1027, 4350, 4272]
encRiptFlag = [184, 161, 235, 97, 140, 111, 84, 182, 162, 135, 76, 10, 69, 246, 195, 152, 133, 88, 229, 104, 111, 22, 39]

charArray = []

for i in range(0, len(resSeeds)):
    random.seed(resSeeds[i])

    rands = []
    for j in range(0,4):
        rands.append(random.randint(0,255))
        
    rightRandom = rands[i%4]
    charArray.append(chr(encRiptFlag[i] ^ rightRandom))
    
print(''.join(charArray))
```

### Pwn - Qweens Gambit
1) Play
Ra1 - Qg7 - Kd2
Gewonnen aber keine Flag

2) Code injection
 `1&&123456` bei der ersten eingabe führt dazu, dass gespielt wird, der Name ist aber '56'
 
3) Overflow
Der Name kann eine länge von 14 Zeichen haben

4) Move*s*
Beim letzten Spielzug wird von Zügen gesprochen

5) Namen
Borgovotron | Qween | Admin | Shaibel -> kein Erfolg 

Hint:
```
if you're having trouble maintaining a shell, try using cat to keep stdin open:

#(cat ./exploit ; cat - ) | nc xxxx yy

or

#(python sploit.py ; cat - ) | nc xxxx yy

etc...
```

### Stego Hackerman
``` strings {file} | grep flag{```

### Misc McRegex
regex -> flag{thy_flag_is_this}
```flag\{[a-z,_]{16}\}```

### Code Hello
No flag needed
```c=
#include <stdio.h>
int main()
{
  char str[100];
  fgets(str, 100, stdin);
  
  printf("Hello %s\n", str);
  
  return 0;
}
```

### Code Short and Sweet
No flag needed
```python=
def AreNumbersEven(numbers):
  res = []
  for i in range(0,len(numbers)):
    if numbers[i] % 2:
      res.append(False)
    else:
      res.append(True)
      
  return res

numbers = raw_input()
integer_list = [int(i) for i in numbers.split(' ')]
even_odd_boolean_list = AreNumbersEven(integer_list)
print even_odd_boolean_list 
```

### A3S Turtles

```
00111101110010010000011011110110100100101000111011101000100000101100110010110001101110001011110111010001010010101010001001001100
ed570e22d458e25734fc08d849961da9
turtles
```

### Stego Secret Images
Die beiden Bilder einfach übereinander legen
https://futureboy.us/stegano/
https://petapixel.com/2015/08/07/a-look-at-photo-steganography-the-hiding-of-secrets-inside-digital-images/

### Vidya Play Me

```
C030=50
C034=50
C0AE=48
C69F=40
CEBC=80

Ground:   C030=50   C034=50   C0AE=48   C69F=40   CEBC=80 
Mid:   C030=35   C034=35   C0AE=30   C69F=40   CEBC=03 
High:   C030=1A   C034=1A   C0AE=15   C69F=23   CEBC=5A 

ROM0:1EFC "call 1D34"
ROM0:2203 "jr nz, 220C"

Callstack nach Trigger Zone:
2B6D
2B24
IEFC -> ID34
IEFC -> ID34 -> 3375
IEFC -> ID34
21FF
```

### Code Nlowest
No flag needed
```cpp=
#include <iostream>
#include <algorithm>
using namespace std;

void display(int a[], unsigned short length) {
   for(int i = 0; i < length; ++i)
   cout << a[i] << " ";
}

void PrintNLowestNumbers(int arr[], unsigned int length, unsigned short nLowest)
{
    sort(arr, arr+length);
    display(arr, nLowest);

}

int main()
{
	char input[0x100];
	int integerList[0x100];
	unsigned int length;
	unsigned short nLowest;
	std::cin >> nLowest;
	std::cin >> length;
	for (int i=0;i<length;i++)
		 std::cin >> integerList[i];
	PrintNLowestNumbers(integerList, length, nLowest);
}
```

### Parsey McParser
```python=
def main():  
    data = raw_input()
    group_name = data.split('|')[0]
    blob = data.split('|')[1]
    result_names_list = ParseNamesByGroup(blob, group_name)
    print(result_names_list)

'''
:param blob: blob of data to parse through (string)
:param group_name: A single Group name ("Green", "Red", or "Yellow",etc...)

:return: A list of all user names that are part of a given Group
'''
def ParseNamesByGroup(blob, group_name):
    # split blob by '+++;'
    parts = blob.split('+++,')
    companies = []
    for p in parts:
        if len(p) != 0:
            companies.append(p)

    # 
    personal = []
    for c in companies:
        for p in c.split('],['):
            personal.append(p)

    employees = []
    for p in personal:
        username = (p.split('user_name":"')[1]).split('",')[0]
        group = (p.split('Group":"')[1]).split('"')[0]
        if group == group_name:
            employees.append(username)

    return employees

if __name__ == "__main__":
    main()

```

### Stego Monks
```
363736393734326536393666326634613734346136313538
git.io/JtJaX
```

### Code IsKingInCheck
Python ist Garbage
```python=
def ParseMatches(chess_matches):
    return [c.split('+') for c in chess_matches.strip().split(' ')]

def setUpMatrix():
    chess_column = []
    for i in range(0,8):
        chess_row = []
        for j in range(0,8):
            chess_row.append('')
        chess_column.append(chess_row)
    return chess_column

def charToNumber(posChar):
    if posChar == 'a': 
        return 0
    if posChar == 'b': 
        return 1
    if posChar == 'c': 
        return 2
    if posChar == 'd': 
        return 3
    if posChar == 'e': 
        return 4
    if posChar == 'f': 
        return 5
    if posChar == 'g': 
        return 6
    if posChar == 'h': 
        return 7

def parsePosition(positon):
    return [char for char in positon]

def paresFigure(figure):
    tmp_figure = [c.split(',') for c in figure.split(' ')][0]
    raw_pos = parsePosition(tmp_figure[2])
    tmp_figure[2] = [charToNumber(raw_pos[0]), int(raw_pos[1]) - 1]
    return tmp_figure

def figureInMatrix(figure, matrix):
    figure_pos = figure[2]
    matrix[figure_pos[0]][figure_pos[1]] = figure

def checkColumnRight(king, matrix):
    if king[2][0] == 7:
        return False
    else:
        for i in range(king[2][0] + 1, 8):
            found_figure = matrix[i][king[2][1]]
            if found_figure:
                if found_figure[0] != king[0]:
                    if found_figure[1] == 'q' or found_figure[1] == 'r':
                        return True
                else:
                    return False


def checkColumnLeft(king, matrix):
    if king[2][0] == 0:
        return False
    else:
        for i in range(king[2][0] -1, -1, -1):
            found_figure = matrix[i][king[2][1]]
            if found_figure:
                if found_figure[0] != king[0]:
                    if found_figure[1] == 'q' or found_figure[1] == 'r':
                        return True
                else:
                    return False

def checkRowDown(king, matrix):
    if king[2][1] == 0:
        return False
    else:
        for i in range(king[2][1] -1, -1, -1):
            found_figure = matrix[king[2][0]][i]
            if found_figure:
                if found_figure[0] != king[0]:
                    if found_figure[1] == 'q' or found_figure[1] == 'r':
                        return True
                else:
                    return False

def checkRowUp(king, matrix):
    if king[2][1] == 7:
        return False
    else:
        for i in range(king[2][1] + 1, 8):
            found_figure = matrix[king[2][0]][i]
            if found_figure:
                if found_figure[0] != king[0]:
                    if found_figure[1] == 'q' or found_figure[1] == 'r':
                        return True
                else:
                    return False

def checkRightUp(king, matrix):
    charLine = king[2][0]
    intLine = king[2][1]
    check = False
    while charLine < 7 and intLine < 7:

        found_figure = matrix[charLine+1][intLine+1]
        if found_figure:
            if found_figure[0] != king[0]:
                if found_figure[1] == 'b' or found_figure[1] == 'q':
                    check = True
                elif found_figure[1] == 'p' and found_figure[0] == 'b' and found_figure[2][0] == king[2][0] + 1 and found_figure[2][1] == king[2][1] + 1:
                    check = True
                break
            else:
                break
        charLine = charLine + 1
        intLine = intLine + 1
    return check

def checkRightDown(king, matrix):
    charLine = king[2][0]
    intLine = king[2][1]
    check = False
    while charLine < 7 and intLine > 0:

        found_figure = matrix[charLine+1][intLine-1]
        if found_figure:
            if found_figure[0] != king[0]:
                if found_figure[1] == 'b' or found_figure[1] == 'q':
                    check = True
                elif found_figure[1] == 'p' and found_figure[0] == 'w' and found_figure[2][0] == king[2][0] + 1 and found_figure[2][1] == king[2][1] - 1:

                    check = True
                break
            else:
                break
        charLine = charLine + 1
        intLine = intLine - 1
    return check

def checkLeftUp(king, matrix):
    charLine = king[2][0]
    intLine = king[2][1]
    check = False
    while charLine > 0 and intLine < 7:

        found_figure = matrix[charLine-1][intLine+1]
        if found_figure:
            if found_figure[0] != king[0]:
                if found_figure[1] == 'b' or found_figure[1] == 'q':
                    check = True
                elif found_figure[1] == 'p' and found_figure[0] == 'b' and found_figure[2][0] == king[2][0] - 1 and found_figure[2][1] == king[2][1] + 1:
                    check = True
                break
            else:
                break
        charLine = charLine - 1
        intLine = intLine + 1
    return check

def checkLeftDown(king, matrix):
    charLine = king[2][0]
    intLine = king[2][1]
    check = False
    while charLine > 0 and intLine > 0:
        
        found_figure = matrix[charLine-1][intLine-1]
        if found_figure:
            if found_figure[0] != king[0]:
                if found_figure[1] == 'b' or found_figure[1] == 'q':
                    check = True
                elif found_figure[1] == 'p' and found_figure[0] == 'w' and found_figure[2][0] == king[2][0] - 1 and found_figure[2][1] == king[2][1] - 1:
                    check = True
                break
            else:
                break
        charLine = charLine - 1
        intLine = intLine - 1
    return check

def checkKnight(king, matrix):
    check = False
    try:
        figurHCN = matrix[king[2][0]+2][king[2][1]+1]
        if figurHCN[0] != king[0] and figurHCN[1] == 'n':
            check = True
    except:
        1+1    
    try:
        figurHCN = matrix[king[2][0]+1][king[2][1]+2]
        if figurHCN[0] != king[0] and figurHCN[1] == 'n':
            check = True
    except:
        1+1 
    try:
        figurHCN = matrix[king[2][0]-2][king[2][1]-1]
        if figurHCN[0] != king[0] and figurHCN[1] == 'n':
            check = True
    except:
        1+1 
    try:
        figurHCN = matrix[king[2][0]-1][king[2][1]-2]
        if figurHCN[0] != king[0] and figurHCN[1] == 'n':
            check = True
    except:
        1+1 
    try:
        figurHCN = matrix[king[2][0]-2][king[2][1]+1]
        if figurHCN[0] != king[0] and figurHCN[1] == 'n':
            check = True
    except:
        1+1 
    try:
        figurHCN = matrix[king[2][0]-1][king[2][1]+2]
        if figurHCN[0] != king[0] and figurHCN[1] == 'n':
            check = True
    except:
        1+1 
    try:
        figurHCN = matrix[king[2][0]+1][king[2][1]-2]
        if figurHCN[0] != king[0] and figurHCN[1] == 'n':
            check = True
    except:
        1+1 
    try:
        figurHCN = matrix[king[2][0]+2][king[2][1]-1]
        if figurHCN[0] != king[0] and figurHCN[1] == 'n':
            check = True
    except:
        1+1 
    return check

def IsKingInCheck(chess_match):
    chess_matrix = setUpMatrix()
    kings = []
    for raw_figure in chess_match:
        figure = paresFigure(raw_figure)

        if figure[1] == "k":
            kings.append(figure)
        
        figureInMatrix(figure, chess_matrix)

    for king in kings:
        if checkColumnRight(king, chess_matrix):
            return True
        elif checkColumnLeft(king, chess_matrix):
            return True
        elif checkRowUp(king, chess_matrix):
            return True
        elif checkRowDown(king, chess_matrix):
            return True
        elif checkRightUp(king, chess_matrix):
            return True
        elif checkRightDown(king, chess_matrix):
            return True
        elif checkLeftUp(king, chess_matrix):
            return True
        elif checkLeftDown(king, chess_matrix):
            return True
        elif checkKnight(king, chess_matrix):
            return True
        else:
            return False
    

result = []
chess_matches = ParseMatches(raw_input())
for chess_match in chess_matches:
    result.append(IsKingInCheck(chess_match))
    
print result
```